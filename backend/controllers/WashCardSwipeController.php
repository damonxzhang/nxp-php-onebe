<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\JsonParser;
use backend\models\WashClothesLog;
use backend\models\WashClothes;
use backend\models\WashClothesUser;

class CardSwipeController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['parsers']['application/json'] = JsonParser::class;
        return $behaviors;
    }

    // 刷卡展示页面
    public function actionIndex()
    {
        return $this->render('index');
    }

    // 前端定时接口：同步日志+聚合数据
    public function actionSyncAndGetRecords()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // 1. 同步今日未处理日志到业务表
            $logs = WashClothesLog::getTodayUnprocessedLogs()->all();
            foreach ($logs as $log) {
                $userAsset = WashClothesUser::findOne(['rfid' => $log->rfid]);
                if (!$userAsset || $userAsset->status != WashClothesUser::STATUS_NORMAL) {
                    $log->is_processed = WashClothesLog::PROCESS_YES;
                    $log->save(false);
                    continue;
                }

                $weekDay = (int)date('N', strtotime($log->swipe_time));
                if (!in_array($weekDay, [1,2,3,4])) {
                    $log->is_processed = WashClothesLog::PROCESS_YES;
                    $log->save(false);
                    continue;
                }

                $operationType = $weekDay <= 2 ? WashClothes::OP_RECEIVE : WashClothes::OP_DEPLOY;
                $currentYear  = date('Y', strtotime($log->swipe_time));
                $deployStatus = WashClothesUser::getDeployStatus($userAsset->pid, $currentYear);
                $lastWeekRfid = WashClothesUser::getLastWeekRfid($userAsset->pid, $userAsset->types, $currentYear);
                $investTime  = $operationType == WashClothes::OP_DEPLOY ? $log->swipe_time : null;
                $receiveTime = $operationType == WashClothes::OP_RECEIVE ? $log->swipe_time : null;

                $record = new WashClothes();
                $record->rfid = $log->rfid;
                $record->pid  = $userAsset->pid;
                $record->user_name = $userAsset->user_name;
                $record->types = $userAsset->types;
                $record->operation_type = $operationType;
                $record->deploy_status  = $deployStatus;
                $record->last_week_rfid = $lastWeekRfid;
                $record->invest_time    = $investTime;
                $record->receive_time   = $receiveTime;
                $record->save(false);

                $log->is_processed = WashClothesLog::PROCESS_YES;
                $log->save(false);
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return $this->asJson([
                'code' => 500,
                'msg' => '同步失败：' . $e->getMessage(),
                'data' => [],
                'swipe_results' => []
            ]);
        }

        // 2. 聚合今日人员刷卡数据
        $todayStart = date('Y-m-d 00:00:00');
        $userPids = WashClothes::find()
            ->select('DISTINCT pid')
            ->where(['>=', 'create_time', $todayStart])
            ->column();

        $userList = [];
        $swipeResults = [];
        foreach ($userPids as $pid) {
            $userInfo = WashClothesUser::find()
                ->select(['user_name', 'pid', 'area'])
                ->where(['pid' => $pid])
                ->groupBy(['user_name', 'pid', 'area'])
                ->one();

            if (!$userInfo) continue;

            $userData = [
                'name'  => $userInfo->user_name,
                'pid'   => $userInfo->pid,
                'area'  => $userInfo->area ?: '未分配',
                'hat'   => ['swipe_time' => '未刷卡', 'info' => '无', 'result' => '无'],
                'cloth' => ['swipe_time' => '未刷卡', 'info' => '无', 'result' => '无'],
                'pants' => ['swipe_time' => '未刷卡', 'info' => '无', 'result' => '无'],
            ];

            // 获取该人员今日刷卡记录
            $userRecords = WashClothes::find()
                ->where(['pid' => $pid, '>=', 'create_time', $todayStart])
                ->orderBy(['create_time' => SORT_DESC])
                ->all();

            foreach ($userRecords as $record) {
                $swipeTime = date('Y-m-d H:i:s', strtotime($record->create_time));
                $operationText = WashClothes::getOperationText($record->operation_type);
                switch ($record->deploy_status) {
                    case WashClothes::DEPLOY_FINISHED:
                        $statusText = '已完成投放';
                    case WashClothes::DEPLOY_UNFINISHED:
                        $statusText = '未投放';
                    case WashClothes::DEPLOY_PART:
                        $statusText = '部分投放';
                    default:
                        $statusText = '未知状态';
                }
                $info = "{$operationText} - {$statusText}";

                // 判定刷卡结果状态
                $lastWeekRfid = $record->last_week_rfid;
                $currentRfid  = $record->rfid;
                $weekDay = (int)date('N', strtotime($record->create_time));
                $result  = '';

                if ($weekDay <= 2) { // 领取操作
                    $result = empty($lastWeekRfid) ? '上周未投放' : '本周领取成功';
                } else { // 投放操作
                    if (empty($lastWeekRfid)) {
                        $result = '上周已经清洗过';
                    } else {
                        $result = $currentRfid == $lastWeekRfid ? '本周投放失败' : '本周投放成功';
                    }
                }

                // 赋值到对应物资类型
                $typeText = WashClothes::getTypeText($record->types);
                switch ($record->types) {
                    case WashClothes::TYPE_HAT:
                        $userData['hat']['swipe_time'] = $swipeTime;
                        $userData['hat']['info'] = $info;
                        $userData['hat']['result'] = $result;
                        $swipeResults[] = ['name' => $userInfo->user_name, 'type' => $typeText, 'result' => $result];
                        break;
                    case WashClothes::TYPE_CLOTH:
                        $userData['cloth']['swipe_time'] = $swipeTime;
                        $userData['cloth']['info'] = $info;
                        $userData['cloth']['result'] = $result;
                        $swipeResults[] = ['name' => $userInfo->user_name, 'type' => $typeText, 'result' => $result];
                        break;
                    case WashClothes::TYPE_PANTS:
                        $userData['pants']['swipe_time'] = $swipeTime;
                        $userData['pants']['info'] = $info;
                        $userData['pants']['result'] = $result;
                        $swipeResults[] = ['name' => $userInfo->user_name, 'type' => $typeText, 'result' => $result];
                        break;
                }
            }

            $userList[] = $userData;
        }

        return $this->asJson([
            'code' => 200,
            'msg' => '同步成功',
            'data' => $userList,
            'swipe_results' => $swipeResults,
            'last_sync' => date('Y-m-d H:i:s')
        ]);
    }

    // 模拟刷卡接口（硬件/前端调用）
    public function actionSwipe()
    {
        $post = Yii::$app->request->post();
        $rfid = $post['rfid'] ?? '';
        $card = $post['card'] ?? '';

        if (empty($rfid)) {
            return $this->asJson(['code' => 400, 'msg' => 'RFID码不能为空']);
        }

        $log = new WashClothesLog();
        $log->rfid = $rfid;
        $log->card = $card;
        $log->swipe_time = date('Y-m-d H:i:s');
        if (!$log->save()) {
            return $this->asJson(['code' => 500, 'msg' => '刷卡日志写入失败', 'error' => $log->errors]);
        }

        return $this->asJson(['code' => 200, 'msg' => '刷卡成功，等待同步']);
    }
}