<?php
namespace backend\controllers;

use backend\models\Notice;
use backend\models\NoticeUser;
use backend\models\search\NoticeSearch;
use backend\models\UserInfo;
use backend\models\UserStation;
use Yii;
use yii\data\Pagination;

class NoticeController extends BaseController
{
    /**
     * 数据列表
     * @return string
     */
    public function actionIndex()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new NoticeSearch();
        $data         = $searchModel->search($request);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        return $this->render('index', [
            'request'      => $request,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination
        ]);
    }

    /**
     * 添加通知
     * @return string
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {
        if (!Yii::$app->request->isPost) {
            $uid         = $this->getUid();
            $connectUser = (new UserInfo())->getConnectUserList();
            $userInfo    = UserInfo::find()->select('area')->where(['id' => $uid])->limit(1)->one();
            $section = ['id'=>$userInfo['section'],'value' =>  UserInfo::$sectionArr[$userInfo['section']]];
            $userStations =  UserStation::find()->where(['status' => 1])->select('id,station')->asArray()->all();

            return $this->render('create', [
                'connectUser' => $connectUser,
                'uid'=>$uid,
                'userStations' => $userStations,
                'section' => $section
            ]);
        }
        $post       = Yii::$app->request->post();
        $userIdList = explode(',', $post['user_id']);
        if (empty($userIdList)) {
            return $this->json(100, '提交数据参数错误!');
        }
        $transaction = Yii::$app->db->beginTransaction();
        $noticeId      = (new Notice())->create($post);
        if (!$noticeId) {
            return $this->json(100, '添加通知失败!');
        }
        $status = NoticeUser::STATUS_UNREAD;
        $time = date('Y-m-d H:i:s', time());
        $data = [];
        foreach ($userIdList as $uId) {
            $data[] = [$noticeId, $uId, $status, $time];
        }
        $insertFlowNode = Yii::$app->db->createCommand()->batchInsert(NoticeUser::tableName(), ['notice_id', 'user_id', 'status', 'create_time'], $data)->execute();
        if ($insertFlowNode > 0) {
            $transaction->commit();
            return $this->json(200, '添加通知成功!');
        }
        $transaction->rollBack();
        return $this->json(100, '添加通知失败!');
    }

    /**
     * 修改通知
     * @return string
     * @throws \yii\base\Exception
     */
    public function actionUpdate()
    {
        $id = Yii::$app->request->get('id');
        $model = Notice::findOne($id);
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $model = new Notice();
            $res = $model->edit($post);
            if ($res['status'] != 200) {
                return $this->json(100, $res['msg']);
            }
            return $this->json(200, $res['msg']);
        }
        return $this->render('update', compact('model'));
    }

    /**
     * 通知详情
     * @return string
     */
    public function actionDetail()
    {
        $Id         = Yii::$app->request->get('id');
        $list      = Notice::findOne(['id' => $Id]);
        $readUserName  = (new NoticeUser())->getNoticeSignPerson($Id, NoticeUser::STATUS_READ);
        $noReadUserName  = (new NoticeUser())->getNoticeSignPerson($Id, NoticeUser::STATUS_UNREAD);
        return $this->render('detail', [
            'list'     => $list,
            'readUserName' => $readUserName,
            'noReadUserName' => $noReadUserName
        ]);
    }

}

