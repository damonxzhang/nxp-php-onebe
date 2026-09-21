<?php
namespace backend\controllers;

use backend\models\Flow;
use backend\models\FlowApproval;
use backend\models\FlowAudit;
use backend\models\FlowLine;
use backend\models\FlowNode;
use backend\models\Notice;
use backend\models\NoticeUser;
use backend\models\search\FlowApprovalSearch;
use backend\models\search\FlowAuditSearch;
use yii\data\Pagination;
use Yii;

class FlowApprovalController extends BaseController
{
    /**
     * 流程列表
     * @return string
     */
    public function actionIndex()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new FlowApprovalSearch();
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
     * 添加审批
     * @return string
     * @throws yii\db\Exception
     */
    public function actionCreate()
    {
        if (Yii::$app->request->isPost) {
            $post            = Yii::$app->request->post();
            $post['user_id'] = $this->getUid();
            $approvalId      = (new FlowApproval())->create($post);
            if (!$approvalId) {
                return $this->json(100, '添加审批失败!');
            }
            if ($post['status'] == FlowApproval::STATUS_APPROVAL) {
                $post['approval_id'] = $approvalId;
                $result              = $this->addFlowLine($post);
                if (!$result) {
                    return $this->json(100, '添加审批失败!');
                }
            }
            return $this->json(200, '添加审批成功!');
        }
        $flow = Flow::find()->where(['status' => Flow::STATUS_OPEN])->asArray()->all();
        return $this->render('create', compact('flow'));
    }

    /**
     * 添加审批流程节点
     * @param $param
     * @return bool
     * @throws yii\db\Exception
     */
    public function addFlowLine($param)
    {
        $nodeIdList = (new FlowNode())->getIdListByFlowId($param['flow_id']);
        if (empty($nodeIdList)) {
            return false;
        }
        $checkLine = FlowLine::findOne(['flow_id' => $param['flow_id']]);
        if(!$checkLine){
            $prevId = 0;
            $data   = [];
            foreach ($nodeIdList as $id) {
                $data[] = [$param['flow_id'], $prevId, $id];
                $prevId = $id;
            }
            $data[]         = [$param['flow_id'], $prevId, FlowLine::LINE_NODE_ID];//添加最后截止节点
            $insertFlowLine = Yii::$app->db->createCommand()->batchInsert(FlowLine::tableName(), ['flow_id', 'prev_node_id', 'next_node_id'], $data)->execute();
            if ($insertFlowLine <= 0) {
                return false;
            }
        }
        $modelLine        = FlowLine::findOne(['flow_id' => $param['flow_id'], 'prev_node_id' => 0]);
        $param['node_id'] = $modelLine->next_node_id;
        $modelNode        = FlowNode::findOne(['id' => $param['node_id']]);
        $param['user_id'] = $modelNode->user_id;
        $param['status']  = FlowAudit::STATUS_WAIT;
        $result           = (new FlowAudit())->create($param);
        if (!$result) {
            return false;
        }

        //发消息
        $sendMessage = $this->sendMessage($param);
        if(!$sendMessage){
            return false;
        }
        return true;
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

    /**
     * 审批详情
     * @return string
     */
    public function actionView()
    {
        $approvalId = Yii::$app->request->get('id');
        $data       = (new FlowAuditSearch())->getAuditLine($approvalId);
        return $this->render('view', ['data' => $data]);
    }

    /**
     * 修改审批
     * @return string
     * @throws Yii\db\Exception
     */
    public function actionUpdate()
    {
        if (Yii::$app->request->isPost) {
            $post           = Yii::$app->request->post();
            $transaction    = Yii::$app->db->beginTransaction();
            $model          = FlowApproval::findOne($post['id']);
            $model->title   = $post['title'];
            $model->content_data = $post['content_data'];
            $model->status  = $post['status'];
            if (!$model->save()) {
                return $this->json(100, '修改审批失败!');
            }
            if ($post['status'] == FlowApproval::STATUS_APPROVAL) {
                $post['approval_id'] = $post['id'];
                $post['user_id']     = $this->getUid();
                $result              = $this->addFlowLine($post);
                if (!$result) {
                    $transaction->rollBack();
                    return $this->json(100, '修改审批失败!');
                }
            }
            $transaction->commit();
            return $this->json(200, '修改审批成功!');
        }
        $id    = Yii::$app->request->get('id');
        $model = FlowApproval::findOne($id);
        $flow  = Flow::find()->where(['status' => Flow::STATUS_OPEN])->asArray()->all();
        return $this->render('update', ['flow' => $flow,'model' => $model]);
    }

}
