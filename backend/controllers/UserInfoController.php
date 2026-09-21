<?php

namespace backend\controllers;

use backend\models\Base;
use backend\models\Role;
use backend\models\search\UserInfoSearch;
use backend\models\Team;
use backend\models\User;
use backend\models\UserInfo;
use backend\models\UserStation;
use Yii;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class UserInfoController extends BaseController
{
    /**
     * 列表
     * @return string
     */
    public function actionIndex()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new UserInfoSearch();
        $data         = $searchModel->search($request);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);

        $stations =  ArrayHelper::map( (new UserStation())->getStation(),'id','station');
        $uid = (new Base())->getUid();
        
        $get_role_id = UserInfo::findOne($uid);
        $role_id = '';
        if (!empty($get_role_id))
            $role_id = $get_role_id->role_id;

        return $this->render('index', [
            'uid'          => $uid,
            'role_id'          => $role_id,
            'request'      => $request,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination,
            'stations' => $stations
        ]);
    }

    /**
     *  根据分组搜索用户
     * @return json
     */
    public function actionSearchUser(){
        $request = Yii::$app->request->post();

        $result = [
            'code' => 1,
            'msg' => '没有数据',
            'results' => []
        ];
        $userInfo = new UserInfo();
        $params = $userInfo->BySearchGetBlogUserList($request);
        if(!empty($params)){
            $result['code'] = 0;
            $result['msg'] = 'success';
            $result['results'] = $params;
        }
        return json_encode($result);
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
            $model = new UserInfo();
            $res   = $model->create($post);
            if ($res['status'] != 200) {
                return $this->json(100, $res['msg']);
            }
            // print_r($model);die;
            // 调用接口同步充电柜数据库            
            $post_data = array(
                "email"	=>	$model->email	,
                "pid"	=>	$model->pid	,
                "chinese_name"	=>	$model->chinese_name	,
                "english_name"	=>	$model->english_name	,
                "position"	=>	$model->position	,
                "shift"	=>	$model->shift	,
                "area"	=>	$model->area	,
                "station_id"	=>	$model->station_id	,
                "sfc_number"	=>	$model->sfc_number	,
                "fsl_number"	=>	$model->fsl_number	,
                "meal_card_code"	=>	$model->meal_card_code	,
                "wbi_code"	=>	$model->wbi_code	,
                "badge_code"	=>	$model->badge_code	,
                "skill"	=>	$model->skill	,
                "department_number"	=>	$model->department_number	,
                "section"	=>	$model->section	,
            );
            
            // print_r($post_data);die;
            $url = 'http://10.193.226.69:9010/api/user-info-api/change-user-info';//随便写的
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);//用post方法传送参数
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($ch);
            curl_close($ch);

            return $this->json(200, '添加成功!');
        }
        $connectUser = (new UserInfo())->getConnectUserList(UserInfo::$leaderArr);
        $roles       = Role::find()->where(['>', 'id', 1])->orderBy('id asc')->all();
        $station     = (new UserStation())->getStation();
        $team = array_column(Team::find()->where(['status' => Team::STATUS_YES])->asArray()->all(),'teamName','id');

        return $this->render('create', [
            'connectUser' => $connectUser,
            'roles'       => $roles,
            'team'        => $team,
            'station'     => $station,
        ]);
    }

    /**
     * 修改
     * @return string
     */
    public function actionUpdate()
    {
        if (Yii::$app->request->isPost) {
            $post  = Yii::$app->request->post();
            $MemberModel = new UserInfo();
            $res   = $MemberModel->edit($post);
            if ($res['status'] != 200) {
                return $this->json(100, $res['msg']);
            }
                        // print_r($post);die;

            // 调用接口同步充电柜数据库            
            $post_data = array(
                "email"=>$post['email'],
                "pid"=>$post['pid'],
                "chinese_name"=>$post['chinese_name'],
                "english_name"=>$post['english_name'],
                "position"=>$post['position'],
                "shift"=>$post['shift'],
                "area"=>$post['area'],
                "station_id"=>$post['station_id'],
                "sfc_number"=>$post['sfc_number'],
                "fsl_number"=>$post['fsl_number'],
                "meal_card_code"=>$post['meal_card_code'],
                "wbi_code"=>$post['wbi_code'],
                "badge_code"=>$post['badge_code'],
                "skill"=>$post['skill'],
                "department_number"=>$post['department_number'],
                "section"=>$post['section'],
            );
            
            // print_r($post_data);die;
            $url = 'http://10.193.226.69:9010/api/user-info-api/change-user-info';//随便写的
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);//用post方法传送参数
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($ch);
            curl_close($ch);

            return $this->json(200, $res['msg']);
        }
        $id    = Yii::$app->request->get('id');
        $model = UserInfo::findOne($id);
        $connectUser = (new UserInfo())->getConnectUserList(UserInfo::$leaderArr);
        $roles       = Role::find()->where(['>','id',1])->orderBy('id asc')->all();
        $station     = (new UserStation())->getStation();
        $team = array_column(Team::find()->where(['status' => Team::STATUS_YES])->asArray()->all(),'teamName','id');

        return $this->render('update', [
            'connectUser' => $connectUser,
            'team'        => $team,
            'roles'       => $roles,
            'station'     => $station,
            'model'       => $model,
        ]);
    }

    /**
     * 创建密码
     * @return string
     * @throws \yii\base\Exception
     */
    public function actionCreatePassword()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $res = (new User())->create($post);
            if ($res['status'] != 200) {
                return $this->json(100, $res['msg']);
            }
            return $this->json(200, $res['msg']);
        }
        $id = Yii::$app->request->get('id');
        return $this->render('create-password', ['id' => $id]);
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
              // 发送GET请求
            $member = UserInfo::findOne(['id'=>$model->user_id]);
            $url = 'http://10.193.227.41:8080/backend/manager-onebe/update-password'; // 调用CSAM网站地址
            $url .= '?wbi_code=' . urlencode($member->wbi_code);
            $url .= '&password=' . urlencode($post['password']);
            // 使用curl发送GET请求
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 如果需要HTTPS
            $response = curl_exec($ch);
            curl_close($ch);
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
        $params = $query->asArray()->all();
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