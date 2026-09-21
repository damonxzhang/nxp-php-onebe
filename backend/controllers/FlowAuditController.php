<?php
namespace backend\controllers;

use backend\models\FlowApproval;
use backend\models\FlowAudit;
use backend\models\FlowLine;
use backend\models\FlowNode;
use backend\models\Notice;
use backend\models\NoticeUser;
use backend\models\search\FlowAuditSearch;
use yii\data\Pagination;
use Yii;

class FlowAuditController extends BaseController
{
    /**
     * 流程列表
     * @return string
     */
    public function actionIndex()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new FlowAuditSearch();
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
     * 审批
     * @return string
     */
    public function actionView()
    {
        $id   = Yii::$app->request->get('id');
        $data = (new FlowAuditSearch())->getAuditData($id);
        return $this->render('view', ['id' => $id, 'data' => $data]);
    }

    /**
     * 添加审批信息
     * @return string
     * @throws yii\db\Exception
     */
    public function actionUpdate()
    {
        $post        = Yii::$app->request->post();
        $transaction = Yii::$app->db->beginTransaction();
        if($post['status'] == FlowAudit::STATUS_REJECT && empty($post['audit_info'])){
            return $this->json(100, '驳回的审批意见不能为空!');
        }
        $blogId      = (new FlowAudit())->edit($post);
        if (!$blogId) {
            return $this->json(100, '审批信息添加失败!');
        }
        //修改审批信息
        $auditModel             = FlowAudit::findOne(['id' => $post['id']]);
        $approvalModel          = FlowApproval::findOne(['id' => $auditModel->approval_id]);
        $lineModel              = FlowLine::findOne(['flow_id' => $approvalModel->flow_id, 'prev_node_id' => $auditModel->node_id]);
        //判断是否被驳回
        if($post['status'] == FlowAudit::STATUS_REJECT){
            $nextNodeId = FlowLine::LINE_NODE_OTHER;
            $approvalModel->status = FlowApproval::STATUS_REJECT;
        }else{
            $nextNodeId             = $lineModel->next_node_id;
            $approvalModel->node_id = $nextNodeId;
        }
        if ($nextNodeId == FlowLine::LINE_NODE_ID) {
            $approvalModel->status = FlowApproval::STATUS_SUCCESS;
        }
        if (!$approvalModel->save()) {
            $transaction->rollBack();
            return $this->json(100, '审批信息添加失败!');
        }

        //添加下一审批信息
        if($nextNodeId > 0 && $nextNodeId != FlowLine::LINE_NODE_OTHER){
            $addData['flow_id']     = $approvalModel->flow_id;
            $addData['node_id']     = $nextNodeId;
            $addData['approval_id'] = $auditModel->approval_id;
            $modelNode              = FlowNode::findOne(['id' => $nextNodeId]);
            $addData['user_id']     = $modelNode->user_id;
            $addData['status']      = FlowAudit::STATUS_WAIT;
            $result                 = (new FlowAudit())->create($addData);
            if (!$result) {
                $transaction->rollBack();
                return $this->json(100, '审批信息添加失败!!');
            }
        }

        //发消息
        $userId = $approvalModel->user_id; //被驳回或者审批通过会给发起者发消息
        if($post['status'] != FlowAudit::STATUS_REJECT){
            $modelNode = FlowNode::findOne(['id' => $nextNodeId]);
            $userId = $modelNode->user_id;
        }
        $param = [
            'title' => $approvalModel->title,
            'content_data' => $approvalModel->content_data,
            'user_id' => $userId
        ];
        $sendMessage = $this->sendMessage($param);
        if(!$sendMessage){
            $transaction->rollBack();
            return $this->json(100, '审批信息添加失败!!!');
        }
        $transaction->commit();
        return $this->json(200, '审批信息添加成功!');
    }

    /**
     * 审批详情
     * @return string
     */
    public function actionDetail()
    {
        $id   = Yii::$app->request->get('id');
        $data = (new FlowAuditSearch())->getAuditLine($id);
        return $this->render('detail', ['data' => $data]);
    }

    /**
     * 发送通知
     * @param $param
     * @return bool
     * @throws \yii\db\Exception
     */
    public function sendMessage($param)
    {
        //添加通知标题
        $noticeData = [
            'title' => $param['title'],
            'type' => Notice::TYPE_APPROVAL,
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

