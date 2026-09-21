<?php

namespace backend\controllers;

use backend\models\Base;
use backend\models\Role;
use backend\models\search\UserInfoSearch;
use backend\models\Team;
use backend\models\User;
use backend\models\UserInfo;
use backend\models\UserStation;
use backend\models\UserTempCard;
use backend\models\UserTempCardRecord;
use Yii;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class UserTempCardController extends BaseController
{
    /**
     * 列表
     * @return string
     */
    public function actionIndex()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new UserTempCard();
        $data         = $searchModel->search($request);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);

        $request = Yii::$app->request->post();



        return $this->render('index', [
            'request'      => $request,
            'pagination'   => $pagination,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 借还列表
     * @return string
     */
    public function actionRecord()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new UserTempCardRecord();
        $data         = $searchModel->search($request);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);

        $request = Yii::$app->request->post();

        return $this->render('record', [
            'request'      => $request,
            'pagination'   => $pagination,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 详情
     * @return string
     */
    public function actionDetail()
    {
        $id    = Yii::$app->request->get('id');
        $model = UserInfo::findOne($id);
        $team =  Team::findOne(['id' => $model->team]);
        return $this->render('detail', [
            'model'       => $model,
            'team'       => $team,
        ]);
    }

    /**
     * 添加
     * @return string
     */
    public function actionCreate()
    {
        if (Yii::$app->request->isPost) {
            $post  = Yii::$app->request->post();
            $model = new UserTempCard();
            $model->number = $post['number'];
            $model->status = $post['status'];
            $model->created_at = date('Y-m-d H:i:s', time());

            if ($model->save(false)) {
                return $this->json(200, '添加成功');
            }
            return $this->json(100, '添加失败!');
        }
        return $this->render('create');
    }

    /**
     * 修改
     * @return string
     */
    public function actionUpdate()
    {
        if (Yii::$app->request->isPost) {
            $post  = Yii::$app->request->post();
            $model = UserTempCard::findOne($post['id']);
            $model->number = $post['number'];
            $model->status = $post['status'];
            $model->updated_at = date('Y-m-d H:i:s', time());

            if ($model->save(false)) {
                return $this->json(200, '编辑成功');
            }
            return $this->json(100, '编辑失败!');
        }

        $id    = Yii::$app->request->get('id');
        $model = UserTempCard::findOne($id);

        return $this->render('update', [
            'model'       => $model,
        ]);
    }

    /**
     * 借还
     * @return string
     * @throws \yii\base\Exception
     */
    public function actionTake()
    {
        if (Yii::$app->request->isPost) {
            $post  = Yii::$app->request->post();
            $getUserId = UserInfo::find();
            $getUserId->andWhere(['pid'=>$post['pid']]);
            $getUserIdData = $getUserId->asArray()->all();
            if (!$getUserIdData)
                return $this->json(100, 'PID错误，查无此人!');

            $checkStatus = UserTempCard::findOne($post['id']);
            if ($checkStatus->user_id != null){
                if ($post['status'] == 1)
                    return $this->json(100, '该卡已被领用，请先归还');

            }else{
                if ($post['status'] == 2)
                    return $this->json(100, '该卡可以领用，无需归还');

            }

            if ($post['status'] == 1){

                $model = UserTempCard::findOne($post['id']);
                $model->user_id = $getUserIdData[0]['id'];
                $model->save();

                $modelNew = new UserTempCardRecord();
                $modelNew->temp_card_id = $post['id'];
                $modelNew->user_id = $getUserIdData[0]['id'];
                $modelNew->status = $post['status'];
                $modelNew->created_at = date('Y-m-d H:i:s', time());
                $modelNew->save();

                return $this->json(200, '编辑成功');

            }
            else{
                $check = UserTempCard::findOne($post['id']);
                if ($getUserIdData[0]['id'] != $check->user_id){
                    return $this->json(100, '不是本人归还');
                }


                $model = UserTempCard::findOne($post['id']);
                $model->user_id = '';
                $model->save();

                $modelNew = new UserTempCardRecord();
                $modelNew->temp_card_id = $post['id'];
                $modelNew->user_id = $getUserIdData[0]['id'];
                $modelNew->status = $post['status'];
                $modelNew->created_at = date('Y-m-d H:i:s', time());
                $modelNew->save();

                return $this->json(200, '编辑成功');

            }

        }

        $id    = Yii::$app->request->get('id');
        $model = UserTempCard::findOne($id);

        return $this->render('take', [
            'model'       => $model,
        ]);
    }

    /**
     * 编辑密码
     * @return string
     * @throws \yii\base\Exception
     */
    public function actionUpdatePassword()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if ($post['user_id'] == 1) {
                return $this->json(100, 'admin禁止修改');
            }
            $model = User::findOne(['user_id' =>$post['user_id']]);
            $model->name = $post['name'];
            if ($post['password']) {
                if($post['password'] === $post['rePassword']){
                    $model->password = Yii::$app->security->generatePasswordHash($post['password']);
                }else{
                    return $this->json(100, '两次输入的密码不一致!');
                }
            }
            if (!$model->save()) {
                return $this->json(100, '修改密码失败!');
            }
            return $this->json(200, '修改密码成功!');
        }
        $userId = Yii::$app->request->get('id');
        $user = User::findOne(['user_id' => $userId]);
        return $this->render('update-password', ['userId' => $userId, 'user' => $user]);
    }

    /**
     * 修改状态
     * @return string
     */
    public function actionChangeStatus()
    {
        $post           = Yii::$app->request->post();
        $member         = UserInfo::findOne($post['id']);
        $status         = $post['status'] == 1 ? 2 : 1;
        $member->status = $status;
        if (!$member->save(false)) {
            return $this->json(100, '操作失败');
        }
        return $this->json(200, '操作成功');
    }

    /**
     * 导出数据
     */
    public function actionExport()
    {
        $search = Yii::$app->request->get('search');
        $query  = UserInfo::find();
        $data   = $query->all();
        $header = ['ID', '昵称', '手机号码', '邮箱', '创建时间', '状态'];
        $list[] = implode(',', $header);
        if ($data) {
            foreach ($data as $val) {
                $item   = [
                    $val->id,
                    $val->nickname,
                    $val->tel,
                    $val->email,
                    date('Y-m-d H:i:s', $val->create_time),
                    $val->status == 1 ? '启用' : '禁用',
                ];
                $list[] = implode(',', $item);
            }
        }
        $fileName = '用户列表_' . date('Y-m-d H:i');
        return $this->csvExport($list, $fileName);
    }

    /**
     *  根据用户名称搜索用户
     */
    public function actionSearchByName($area = null){
        $name = Yii::$app->request->get('keyword');
        $result = [
            'code' => 1,
            'msg' => '没有数据',
            'results' => []
        ];

        $query = UserInfo::find()->select('id,chinese_name as text');
        if($name){
            $query->where(['like','chinese_name',$name]);
        }
        if($area){
            $query->andWhere(['area' => $area]);
        }
        $query->andWhere(['>','role_id',1]);
        $params = $query->limit(20)->asArray()->all();
        if($params){
            $result['code'] = 0;
            $result['msg'] = 'success';
            $result['results'] = $params;
        }


        return json_encode($result);
    }

    /**
     *  根据用户名称搜索用户
     */
    public function actionSearchUserByName(){
        $name = Yii::$app->request->get('keyword');
        $result = [
            'code' => 1,
            'msg' => '没有数据',
            'results' => []
        ];

        $query = UserInfo::find()->select('id,chinese_name as text');
        if($name){
            $query->where(['like','chinese_name',$name]);
        }
        $query->where(['<>','email',null]);
        $query->andWhere(['>','role_id',1]);
        $params = $query->limit(20)->asArray()->all();
        if($params){
            $result['code'] = 0;
            $result['msg'] = 'success';
            $result['results'] = $params;
        }


        return json_encode($result);
    }

}