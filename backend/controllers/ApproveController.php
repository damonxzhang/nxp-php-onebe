<?php

namespace backend\controllers;

use backend\models\ApproveConfirm;
use backend\models\ApproveDetails;
use backend\models\NoticeUser;
use backend\models\search\ApproveConfirmSearch;
use Yii;
use yii\data\Pagination;
use yii\db\Exception;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use common\models\SendEmail;
use backend\models\Approve;
use backend\models\ApproveFile;
use backend\models\Base;
use backend\models\search\ApproveSearch;
use backend\models\UserStation;
use backend\models\Notice;
use backend\models\UserInfo;

class ApproveController extends BaseController
{
    public $enableCsrfValidation = FALSE;

    /**
     * 通知列表
     * @return string
     */
    public function actionIndex()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new ApproveSearch();
        $data         = $searchModel->search($request);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        $userInfo     = Yii::$app->user->identity->userInfo;
        return $this->render('index', [
            'userInfo'     => $userInfo,
            'request'      => $request,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination
        ]);
    }

    /**
     * 添加数据
     * @return string
     * @throws Exception
     */
    public function actionCreate()
    {
        if (Yii::$app->request->isPost) {
            $post              = Yii::$app->request->post();
            $uid               = $this->getUid();
            $approve = new Approve();
            $post['self_number']= $approve->getSelfNumber($uid);
            $post['number']     = $approve->createNumber($uid);
            $post['user_id']   = $uid;
            $model             = UserInfo::findOne(['id' => $uid]);
            $post['leader_id'] = $model->leader_uid;
            $post['status']    = Approve::STATUS_PROCESSING;
            $post['is_draft']  = Approve::IS_DRAFT_NO;
            $transaction       = Yii::$app->db->beginTransaction();
            $approveId         = $approve->create($post);
            if (!$approveId) {
                $transaction->rollBack();
                return $this->json(100, '添加失败!');
            }

            $post['approve_id'] = $approveId;
            //添加消息材料
            $trainUser = $this->approveFile($post);
            if (!$trainUser) {
                $transaction->rollBack();
                return $this->json(100, '添加文件失败!');
            }

            $transaction->commit();
            return $this->json(200, '添加成功!');
        }
        $uid        = $this->getUid();
        $number     = (new Approve())->createNumber($uid);
        $stationArr = (new UserStation())->getStation();//获取站别
        $stationArr = ArrayHelper::map($stationArr, 'id', 'station');
        return $this->render('create', [
            'uid'        => $uid,
            'number'     => $number,
            'stationArr' => $stationArr,
        ]);
    }

    /**
     * 添加用户消息记录
     * @param $param
     * @return bool
     * @throws Exception
     */
    public function createApproveConfirm($param)
    {
        $data  = [];
        $time  = date('Y-m-d H:i:s', time());
        $userIdArr = (new UserInfo()) ->getUserIdList($param);
		
        foreach ($userIdArr as $uid) {
            $data[] = [
                'approve_id'  => $param['approve_id'],
                'user_id'     => $uid,
                'unusual'     => ApproveConfirm::UNUSUAL_TRUE,
                'status'      => ApproveConfirm::STATUS_NO,
                'create_time' => $time
            ];
        }

        if (empty($data)) return TRUE;

        //添加消息签到信息
        $transaction = Yii::$app->db->beginTransaction();
        $result      = $this->insertData(ApproveConfirm::tableName(), ['approve_id', 'user_id', 'unusual', 'status', 'create_time'], $data);
        if (!$result) {
            $transaction->rollBack();
            return FALSE;
        }
        $transaction->commit();
        return TRUE;
    }

    /**
     * 上传文件
     * @return string
     */
    public function actionUpload()
    {
        $result = $this->upload('/approve/');
        return Json::encode($result);
    }

    /**
     * 添加文件
     * @param $param
     * @param $key
     * @return bool
     * @throws \yii\db\Exception
     */
    public function approveFile($param, $key = 'approve_id')
    {
        //检查是否上传文件
        if (!isset($param['upload_file'])) {
            return TRUE;
        }
        $data = [];
        $time = date('Y-m-d H:i:s', time());
        foreach ($param['upload_file'] as $file) {
            $path      = pathinfo($file);
            $extension = $path['extension'];
            $data[]    = [$param[$key], $file, $path['filename'], $extension, $time];
        }
        $insertFlowNode = Yii::$app->db->createCommand()->batchInsert(ApproveFile::tableName(), [$key, 'file_path', 'file_name', 'file_extension', 'create_time'], $data)->execute();
        if ($insertFlowNode > 0) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * 消息详情
     * @return string
     */
    public function actionDetail()
    {
        $id       = Yii::$app->request->get('id');
        $model    = Approve::findOne($id);
        $baseUrl  = Yii::$app->params['base_url'];
        $approveFile = (new ApproveFile())->getApproveFileData(['approve_id' => $id]);
        return $this->render('detail', ['model' => $model, 'baseUrl' => $baseUrl, 'approveFile' => $approveFile]);
    }

    /**
     * 待我审批列表
     * @return string
     */
    public function actionFeedback()
    {
        $userId       = $this->getUid();
        $dataProvider = Approve::find()->where(['leader_id' => $userId,'status' =>Approve::STATUS_PROCESSING])->orderBy(['create_time' => SORT_DESC])->all();
        return $this->render('feedback', [
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * 主管审批
     * @return string
     * @throws Exception
     */
    public function actionSelf()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if ($post['status'] == ApproveDetails::STATUS_REJECT && empty($post['reply'])) {
                return $this->json(100, '驳回的审批意见不能为空!');
            }
            $post['user_id']  = $this->getUid();
            $transaction      = Yii::$app->db->beginTransaction();
            $approveDetailsId = (new ApproveDetails())->create($post);
            if (!$approveDetailsId) {
                $transaction->rollBack();
                return $this->json(100, '审批信息添加失败!');
            }
            $param = Approve::find()->where(['id' => $post['approve_id']])->ay()->one();
			$param['shift'] = explode(',',$param['shift']);

            $approveModel = Approve::findOne(['id' => $post['approve_id']]);

            //判断是否被驳回
            if ($post['status'] == ApproveDetails::STATUS_REJECT) {
                $approveModel->status = Approve::STATUS_DEFERRED;
                if (!$approveModel->save(false)) {
                    $transaction->rollBack();
                    return $this->json(100, '审批信息失败!');
                }
                //提交事物
                $transaction->commit();

                //发邮件给发起者
//                $userInfoModel = UserInfo::findOne(['id' => $param['user_id']]);
//                $urlLink       = Yii::$app->request->getHostInfo();
//                $subject       = 'You have a message that the approval has been rejected';
//                $body          = '
//                    <div class="verify-email">
//                        <p>Hello : ' . $userInfoModel->chinese_name . ' </p> <br/>
//                        <p>' . $subject . ' </p> <br/> <br/>
//                        <a href="' . $urlLink . '" > ' . $urlLink . '</a>
//                    </div>';
//                $this->sendEmail($userInfoModel->email, $subject, $body);//给发起者发邮件
                return $this->json(200, '审批成功!');
            }

            //添加需要通知人员信息
            $param['approve_id'] = $post['approve_id'];
            $createApproveConfirm = $this->createApproveConfirm($param);
            if (!$createApproveConfirm) {
                $transaction->rollBack();
                return $this->json(100, '添加审批信息失败!!');
            }

            //更新审批表数据
            if (empty($approveModel->alarm_time)) $approveModel->alarm_time = date("Y-m-d", strtotime("+90 days"));
            $approveModel->effective_time = date("Y-m-d", time());
            $approveModel->status = Approve::STATUS_SUCCESS;
            if (!$approveModel->save(false)) {
                $transaction->rollBack();
                return $this->json(100, '审批信息失败!!');
            }

            //发送消息给通知人
            $param['approve_id'] = $post['approve_id'];
            $sendMessage = $this->sendMessage($param);
            if (!$sendMessage) {
                $transaction->rollBack();
                return $this->json(100, '发送消息失败!');
            }
            //发送邮件
            // $this->sendEmailToUser($param);

            $transaction->commit();
            return $this->json(200, '审批成功!');
        }
        $id          = Yii::$app->request->get('id');
        $model       = Approve::findOne($id);
        $baseUrl     = Yii::$app->params['base_url'];
        $approveFile = (new ApproveFile())->getApproveFileData(['approve_id' => $id]);
        return $this->render('self', ['model' => $model, 'baseUrl' => $baseUrl, 'approveFile' => $approveFile]);
    }

    /**
     * 延期修改消息
     * @return string
     * @throws Exception
     */
    public function actionUpdate()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if (empty($post['alarm_time'])) {
                return $this->json(100, '延期时间不能为空!');
            }
            $model = Approve::findOne($post['id']);
            if ($post['alarm_time'] == $model->alarm_time) {
                return $this->json(100, '延期时间不能等于当前延期日期!');
            }
            if (!empty($model->second_delay_time)) {
                return $this->json(100, '抱歉只能延期两次!');
            }
            if (empty($model->first_delay_time)) {
                $model->first_delay_time = $post['alarm_time'];
            } elseif (empty($model->second_delay_time)) {
                $model->second_delay_time = $post['alarm_time'];
            }
            $model->progress = 0;
            $model->status   = Approve::STATUS_PROCESSING;
            if (!$model->save(FALSE)) {
                return $this->json(100, '延期失败!!!');
            }
            return $this->json(200, '延期成功!');
        }
        $id         = Yii::$app->request->get('id');
        $model      = Approve::findOne(['id' => $id]);
        $stationArr = (new UserStation())->getStation();//获取站别
        $stationArr = ArrayHelper::map($stationArr, 'id', 'station');
        return $this->render('update', [
            'id'         => $id,
            'model'      => $model,
            'stationArr' => $stationArr,
        ]);
    }


    /**
     * 发送消息
     * @param $param
     * @return bool
     * @throws Exception
     */
    public function sendMessage($param)
    {
        //添加信息
        $transaction = Yii::$app->db->beginTransaction();
        //添加通知标题
        $noticeData = [
            'title'        => $param['title'],
            'type'         => Notice::TYPE_APPROVE,
            'content_data' => $param['content_data'],
        ];
        $noticeId   = (new Notice())->create($noticeData);
        if (!$noticeId) {
            $transaction->rollBack();
            return FALSE;
        }

        //添加通知人
        $NoticeData    = [];
        $status        = NoticeUser::STATUS_UNREAD;
        $time          = date('Y-m-d H:i:s', time());
        $userIdArr     = (new UserInfo())->getUserIdList($param);
        foreach ($userIdArr as $uId) {
            $NoticeData[] = [$noticeId, $uId, $status, $time, $param['approve_id']];
        }

        if (empty($NoticeData)){
            $transaction->commit();
            return TRUE;
        }
        //添加通知人信息
        $insertFlowNode = $this->insertData(NoticeUser::tableName(), ['notice_id', 'user_id', 'status', 'create_time', 'connect_id'], $NoticeData);
        if (!$insertFlowNode) {
            $transaction->rollBack();
            return FALSE;
        }
        $transaction->commit();
        return TRUE;
    }

    /**
     * 发送邮件
     * @param $param
     * @return bool
     */
    public function sendEmailToUser($param)
    {
        $param['user_list'] = (new UserInfo())->getUserIdList($param);
        $param['type']      = Notice::TYPE_APPROVE;
        $userName           = (new UserInfo())->getUserName($this->getUid());
        $param['message']   = '用户:' . $userName . ' ,创建/更新了通知消息, ID:' . $param['approve_id'] . ',标题为: ' . $param['title'] . '的日志';
        $sendEmail          = (new SendEmail())->sendEmailUserList($param);
        if ($sendEmail) {
            return true;
        }
        return false;
    }


    /**
     * 草稿列表
     * @return string
     */
    public function actionApproveDraft()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new ApproveSearch();
        $data         = $searchModel->searchDraft($request);
        $dataProvider = $data->getModels();
        $uid          = (new Base())->getUid();
        return $this->render('approve-draft', [
            'uid'          => $uid,
            'request'      => $request,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'   => $data->pagination
        ]);
    }

    /**
     * 草稿文件
     * @param $param
     * @param $key
     * @return bool
     * @throws \yii\db\Exception
     */
    public function approveDraftFiles($param)
    {
        //删除原有数据
        ApproveFile::deleteAll(['approve_id' => $param['approve_id']]);
        //检查是否上传文件
        if (!isset($param['upload_file'])) {
            return TRUE;
        }
        $data = [];
        $time = date('Y-m-d H:i:s', time());
        foreach ($param['upload_file'] as $file) {
            $path      = pathinfo($file);
            $extension = $path['extension'];
            $data[]    = [$param['approve_id'], $file, $path['filename'], $extension, $time];
        }
        if(empty($data)){
            return TRUE;
        }
        $insertFlowNode = Yii::$app->db->createCommand()->batchInsert(ApproveFile::tableName(), ['approve_id', 'file_path', 'file_name', 'file_extension', 'create_time'], $data)->execute();
        if ($insertFlowNode > 0) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * 创建草稿
     * @return string
     * @throws Exception
     */
    public function actionCreateDraft()
    {
        if (Yii::$app->request->isPost) {
            $post              = Yii::$app->request->post();
            $uid               = $this->getUid();
            $approve = new Approve();
            $post['self_number']= $approve->getSelfNumber($uid);
            $post['number']     = $approve->createNumber($uid);
            $post['user_id']   = $uid;
            $model             = UserInfo::findOne(['id' => $uid]);
            $post['leader_id'] = $model->leader_uid;
            $post['status']    = Approve::STATUS_PROCESSING;
            $post['is_draft']  = Approve::IS_DRAFT_YES;
            $transaction       = Yii::$app->db->beginTransaction();
            $approveId         = $approve->create($post);
            if (!$approveId) {
                $transaction->rollBack();
                return $this->json(100, '添加草稿失败!');
            }

            $post['approve_id'] = $approveId;
            //添加消息材料
            $approveFile = $this->approveFile($post);
            if (!$approveFile) {
                $transaction->rollBack();
                return $this->json(100, '添加文件失败!');
            }

            $transaction->commit();
            return $this->json(200, '添加成功!');
        }
        return $this->json(100, '非法请求！');
    }

    /**
     * 更新草稿
     * @return string
     * @throws Exception
     */
    public function actionUpdateDraft()
    {
        if (Yii::$app->request->isPost) {
            $post                       = Yii::$app->request->post();
            $transaction                = Yii::$app->db->beginTransaction();
            $approveModel               = Approve::findOne(['id' => $post['approve_id']]);
            $approveModel->is_draft     = Approve::IS_DRAFT_NO;
            $approveModel->content_data = $post['content_data'];
            $approveModel->alarm_time   = $post['alarm_time'];
            $approveModel->station      = empty($post['station']) ? implode(',', $post['station']) : '';
            $approveModel->shift        = empty($post['shift']) ? implode(',', $post['shift']) : '';
            if (!$approveModel->save(FALSE)) {
                $transaction->rollBack();
                return $this->json(100, '更新草稿失败!');
            }

            //添加消息材料
            $approveFile = $this->approveFile($post);
            if (!$approveFile) {
                $transaction->rollBack();
                return $this->json(100, '添加文件失败!');
            }

            $transaction->commit();
            return $this->json(200, '添加成功!');
        }
        $id          = Yii::$app->request->get('id');
        $model       = Approve::findOne(['id' => $id]);
        $approveFile = ApproveFile::find()->where(['approve_id' => $id])->all();
        $stationArr  = (new UserStation())->getStation();//获取站别
        $stationArr  = ArrayHelper::map($stationArr, 'id', 'station');
        return $this->render('update-draft', [
            'model'       => $model,
            'stationArr'  => $stationArr,
            'approveFile' => $approveFile
        ]);
    }

    /**
     * 删除草稿和对应的用户和图片
     * @param $id
     * @return string
     * @throws \Throwable
     */
    public function actionDeleteDraft($id)
    {
        $model = Approve::findOne(['id' => $id, 'is_draft' => Approve::IS_DRAFT_YES]);
        if (empty($model)) {
            return $this->json(100, '数据错误！');
        }
        if ($model->deleteDraf()) {
            return $this->json(200, '删除成功！');
        } else {
            return $this->json(100, $model->errorToString());
        }

    }

    /**
     * 导出数据
     * @return string
     */
    public function actionExport()
    {
        $request = Yii::$app->request->queryParams;
        $idList  = ArrayHelper::getValue($request, 'idlist', []);
        if (!empty($idList)) {
            $query = Approve::find();
            $query->where(['in', 'id', $idList]);
            $query->asArray();
            $dataProvider = $query->all();
        } else {
            $query = (new ApproveSearch())->getSearchQuery($request);
            $query->asArray();
            $dataProvider = $query->all();
        }
        $data = [];
        if (!empty($dataProvider)) {
            foreach ($dataProvider as $key => $value) {
                $data[$key][] = $value['title'];
                $data[$key][] = $value['number'];
                $data[$key][] = $value['content_data'];
                $data[$key][] = (new UserInfo())->getUserName($value['user_id']);
                $data[$key][] = ArrayHelper::getValue(Approve::$statusArr, $value['status'], '');
                $data[$key][] = $value['progress'] . '%';
                $data[$key][] = $value['create_time'];
                $data[$key][] = $value['effective_time'];
                $data[$key][] = $value['alarm_time'];
                $data[$key][] = $value['first_delay_time'];
                $data[$key][] = $value['second_delay_time'];
            }
        }
        $fileName = '消息列表_' . time() . '.xlsx';
        $header   = ['标题', '编号', '内容', '创建人', '完成状态', '签到率', '创建时间', '生效日期', '过期时间', '首次延期时间', '二次延期时间'];
        array_unshift($data, $header); //添加头部标题
        return $this->json(200, $fileName, $data);
    }

    /**
     * 确认率
     * querenlv
     * @return string
     */
    public function actionConfirm()
    {
        $id              = Yii::$app->request->get('id');
        $appeoveConfirm  = (new ApproveConfirm());
        $approveModel    = Approve::findOne(['id' => $id]);
        //未确认人数
        $param           = ['approve_id' => $id, 'status' => ApproveConfirm::STATUS_NO, 'unusual' => ApproveConfirm::UNUSUAL_TRUE];
        $confirmNo       = ApproveConfirm::find()->where($param)->count();
        $userListNo      = $appeoveConfirm->getSignPerson($param);
        //已确认人数
        $param['status'] = ApproveConfirm::STATUS_YES;
        $confirmYes      = ApproveConfirm::find()->where($param)->count();
        $userListYes     = $appeoveConfirm->getSignPerson($param);                                                                              //已确认
        $userListUnusual = $appeoveConfirm->getSignPerson(['approve_id' => $id, 'unusual' => ApproveConfirm::UNUSUAL_FALSE]);                            //异常
        return $this->render('confirm', [
            'approveModel'    => $approveModel,
            'confirmNo'       => $confirmNo,
            'confirmYes'      => $confirmYes,
            'userListNo'      => $userListNo,
            'userListYes'     => $userListYes,
            'userListUnusual' => $userListUnusual
        ]);
    }

    /**
     * 人员确认消息
     * @return string
     */
    public function actionConfirmData()
    {
        $approveId = Yii::$app->request->post('id');
        $userId    = $this->getUid();
        $model     = ApproveConfirm::findOne(['approve_id' => $approveId, 'user_id' => $userId]);
        if (!$model) {
            return $this->json(100, '无对应消息!');
        }
        $model->status       = ApproveConfirm::STATUS_YES;
        $model->confirm_time = date('Y-m-d H:i:s', time());
        if (!$model->save()) {
            return $this->json(100, '消息确认失败!');
        }
        //更新消息完成率
        (new Approve()) ->updateProgress($approveId);
        return $this->json(200, '消息确认完成!');
    }


    /**
     * 将用户设置为异常
     * @return string
     */
    public function actionAddUnusual()
    {
        $id        = Yii::$app->request->post('id');
        $reason    = Yii::$app->request->post('reason');
        $model     = ApproveConfirm::findOne(['id' => $id]);
        if (!$model) {
            return $this->json(100, '无对应数据信息!');
        }
        //判断是否正常还是异常
        if ($reason == ApproveConfirm::REASON_REAL){
            $model->unusual = ApproveConfirm::UNUSUAL_TRUE;
            $model->reason  = ApproveConfirm::REASON_REAL;
        }else{
            $model->unusual = ApproveConfirm::UNUSUAL_FALSE;
            $model->reason  = $reason;
        }
        if (!$model->save(false)) {
            return $this->json(100, '用户异常设置失败!');
        }
        //更新消息确认率
        (new Approve()) ->updateProgress($model->approve_id);
        return $this->json(200, '修改成功!');
    }

    /**
     * 确认记录列表
     * @return string
     */
    public function actionConfirmRecord()
    {
        $request      = Yii::$app->request->queryParams;
        $request['approve_id'] = Yii::$app->request->get('approve_id');
        $searchModel  = new ApproveConfirmSearch();
        $data         = $searchModel->search($request);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        return $this->render('confirm-record', [
            'request'      => $request,
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination
        ]);
    }

    /**
     * 消息确认完成
     * @return string
     */
    public function actionFinish()
    {
        $id = Yii::$app->request->post('id');
        $model = Approve::findOne(['id' => $id]);
        $model->finish = Approve::FINISH_SUCCESS;
        if (!$model->save(false)) {
            return $this->json(100, '消息结束失败!');
        }
        return $this->json(200, '消息结束完成!');
    }

}
