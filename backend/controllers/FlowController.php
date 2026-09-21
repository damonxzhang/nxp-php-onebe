<?php
namespace backend\controllers;

use backend\models\Flow;
use backend\models\FlowAudit;
use backend\models\FlowNode;
use backend\models\search\FlowSearch;
use backend\models\User;
use backend\models\UserInfo;
use yii\data\Pagination;
use yii\helpers\Json;
use Yii;

class FlowController extends BaseController
{
    /**
     * 流程列表
     * @return string
     */
    public function actionIndex()
    {
        $searchModel  = new FlowSearch();
        $data         = $searchModel->search();
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination
        ]);
    }

    /**
     * 添加流程
     * @return string
     * @throws yii\db\Exception
     */
    public function actionCreate()
    {
        if (Yii::$app->request->isPost) {
            $post       = Yii::$app->request->post();
            $userIdList = explode(',', $post['user_id']);
            if (empty($userIdList)) {
                return $this->json(100, '提交数据参数错误!');
            }
            $transaction = Yii::$app->db->beginTransaction();
            $flowId      = (new Flow())->create($post);
            if (!$flowId) {
                return $this->json(100, '添加流程失败!');
            }
            $data = [];
            foreach ($userIdList as $uId) {
                $data[] = [$flowId, $uId];
            }
            $insertFlowNode = Yii::$app->db->createCommand()->batchInsert(FlowNode::tableName(), ['flow_id', 'user_id'], $data)->execute();
            if ($insertFlowNode > 0) {
                $transaction->commit();
                return $this->json(200, '添加流程成功!');
            }
            $transaction->rollBack();
            return $this->json(100, '添加流程失败!');
        }
        $connectUser = (new UserInfo())->getConnectUserList(UserInfo::$leaderArr);
        return $this->render('create', ['connectUser' => $connectUser]);
    }

    /**
     * 修改流程状态
     * @return string
     */
    public function actionUpdate()
    {
        if (Yii::$app->request->isPost) {
            $post       = Yii::$app->request->post();
            $checkAudit = FlowAudit::findAll(['flow_id' => $post['id'], 'status' => FlowAudit::STATUS_WAIT]);
            if ($checkAudit) {
                return $this->json(100, '有的流程审批尚未结束,不能禁用流程状态!');
            }
            $model         = Flow::findOne($post['id']);
            $model->status = $post['status'];
            if (!$model->save(false)) {
                return $this->json(100, '修改失败!');
            }
            return $this->json(200, '修改成功!');
        }
        $id = Yii::$app->request->get('id');
        $model = Flow::findOne(['id' => $id]);
        return $this->render('update', ['model' => $model]);
    }

    /**
     * 获取流程审核人列表
     * @return string
     */
    public function actionUserList()
    {
        $data   = '{"code": 0,"msg": "","count":';
        $result = (new UserInfo())->getUserRoleData();
        if (!empty($result)) {
            $count = count($result);
            $data  .= $count . ',"data": ' . Json::encode($result) . '}';
        } else {
            $data .= ' 0,"data": []}';
        }
        return $data;
    }

}