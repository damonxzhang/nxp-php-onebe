<?php
namespace backend\controllers;

use backend\models\Notice;
use backend\models\NoticeUser;
use backend\models\search\TrainCheckSearch;
use backend\models\Train;
use backend\models\TrainCheck;
use backend\models\TrainFile;
use yii\data\Pagination;
use Yii;

class TrainCheckController extends BaseController
{
    /**
     * 培训列表
     * @return string
     */
    public function actionIndex()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new TrainCheckSearch();
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
     * 培训签到
     * @return string
     */
    public function actionDetail()
    {
        $Id         = Yii::$app->request->get('id');
        $train      = Train::findOne(['id' => $Id]);
        $trainFile  = (new TrainFile())->getTrainFileData($Id);
        return $this->render('detail', [
            'train'     => $train,
            'trainFile' => $trainFile,
        ]);
    }

    /**
     * 签到
     * @return string
     * @throws \yii\db\Exception
     */
    public function actionChangeStatus()
    {
        $post  = Yii::$app->request->post();
        $uId   = $this->getUid();
        $model = TrainCheck::findOne(['train_id' => $post['id'], 'user_id' => $uId]);
        $model->status = TrainCheck::STATUS_CHECKED;
        $model->check_time = date('Y-m-d H:i:s', time());
        if (!$model->save(false)) {
            return $this->json(100, '签到失败!');
        }

        //发送消息
        $check = TrainCheck::findOne(['train_id' => $post['id'], 'status' => TrainCheck::STATUS_NO_SIGNED]);
        if(!$check){
            $data  = Train::findOne(['id' => $post['id']]);
            $data->status = Train::STATUS_SUCCESS;
            if (!$data->save(false)) {
                return $this->json(100, '签到失败!');
            }
            $sendMessage = $this->sendMessage($data);
            if(!$sendMessage){
                return $this->json(100, '签到失败!!');
            }
        }
        return $this->json(200, '签到成功!');
    }

    /**
     * 培训详情
     * @return string
     */
    public function actionView()
    {
        $Id         = Yii::$app->request->get('id');
        $train      = Train::findOne(['id' => $Id]);
        $trainFile  = (new TrainFile())->getTrainFileData($Id);
        return $this->render('view', [
            'train'     => $train,
            'trainFile' => $trainFile,
        ]);
    }

    /**
     * 发送消息
     * @param $param
     * @return bool
     * @throws \yii\db\Exception
     */
    public function sendMessage($param)
    {
        //添加通知标题
        $noticeData = [
            'title' => $param['title'],
            'type'  => Notice::TYPE_TRAIN,
            'content_data' => $param['content_data'],
        ];
        $noticeId   = (new Notice())->create($noticeData);
        if (!$noticeId) {
            return false;
        }

        //添加通知人
        $status = NoticeUser::STATUS_UNREAD;
        $NoticeData = ['notice_id' => $noticeId, 'user_id' => $param['user_id'], 'status' => $status];
        $insertFlowNode = (new NoticeUser()) -> create($NoticeData);
        //添加日志通知人信息
        if ($insertFlowNode['status'] != 200) {
            return false;
        }
        return true;
    }
}