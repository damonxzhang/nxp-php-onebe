<?php

namespace backend\controllers;

use backend\models\BrushShoes;
use backend\models\search\BrushShoesSearch;
use yii\data\Pagination;
use Yii;
use backend\models\UserInfo;
use yii\helpers\ArrayHelper;
use yii\db\Query;

class BrushShoesController extends BaseController
{

    public function actionIndex()
    {

        $request      = Yii::$app->request->queryParams;
        $searchModel  = new BrushShoesSearch();
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
     * @return bool
     */
    public function actionCheck()
    {
        //根据站别获取人员信息
        $stationArr = [];//站别主键
        $idArr      = UserInfo::find()->select('id')->where(['in', 'station_id', $stationArr])->column();

        //获取规定时间段内的人员信息
        $beginToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $startTime  = date('Y-m-d H:i:s', $beginToday);
        $endTime    = date('Y-m-d H:i:s', time());

        $query = BrushShoes::find();
        $query->select('id');
        $query->where(['>=', 'brush_date', $startTime]);
        $query->andWhere(['<=', 'brush_date', $endTime]);
        $brushShoesData = $query->column();
        if (!$brushShoesData) {
            return false;
        }

        // 先取两个数组交集然后取不在数组idArr交集的差集
        $userIdDiffArr = array_diff($idArr, array_intersect($brushShoesData, $idArr));
        if (empty($userIdDiffArr)) {
            return false;
        }

        //根据获取的未刷卡的人员获取详细信息
        $params      = [];
        $userInfoArr = UserInfo::find()->select('id')->where(['in', 'id', $userIdDiffArr])->asArray()->all();
        foreach ($userInfoArr as $value) {
            $array                 = [];
            $array['chinese_name'] = $value[''];
            $array['area']         = ArrayHelper::getValue(UserInfo::$areaArr, $value[''], '');
            $array['shift']        = ArrayHelper::getValue(UserInfo::$shiftArr, $value['shift'], '');
            $params[]              = $array;
        }
        $send = [];
        $this->sendEmail($send, $params);
        return true;
    }


    /**
     * 发送邮件
     * @param array  $send    收件人信息
     * @param array  $params  发送的数据
     * @param string $subject 发送邮件主题
     * @return bool|void
     */
    public function sendEmail($send, $params, $subject = '未洗鞋的员工名单')
    {
        $html = $this->renderFile('@common/mail/brushshoes/brush-email-view.php', [
            'params'   => $params,
            'userName' => $send['chinese_name']
        ]);
        $mail = Yii::$app->mailer->compose();
        $mail->setHtmlBody($html);
        $mail->setSubject($subject);
        $mail->setTo($send['email']);
        @$mail->send();
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
            $query = BrushShoes::find();
            $query->where(['in', 'id', $idList]);
            $query->asArray();
            $dataTemp = $query->all();
        }else{

            $query = new Query();
            $query->from(BrushShoes::tableName() . ' bs');
            $query->select('bs.brush_date,u.id as user_id');
            $query->leftJoin(UserInfo::tableName() . ' u', 'bs.user_id = u.id');
            $query->andFilterWhere(['u.area' => $request['area']]);
            $query->andFilterWhere(['u.shift' => $request['shift']]);
            $query->andFilterWhere(['like', 'u.chinese_name', trim($request['chinese_name'])]);
            $query->andFilterWhere(['>=', 'bs.brush_date', $request['start_time']]);
            $query->andFilterWhere(['<=', 'bs.brush_date', $request['end_time']]);
            $query->orderBy(['bs.brush_date' => SORT_DESC]);
            $dataTemp = $query->all();
        }
  
        $data = [];
        if (!empty($dataTemp)) {
            foreach ($dataTemp as $key => $value) {
                $data[$key][] = (new UserInfo())->getUserName($value['user_id']);
                $userInfoModel = UserInfo::findOne(['id'=>$value['user_id']]);
                $data[$key][] = $userInfoModel->meal_card_code;
                $data[$key][] = date('Y-m-d H:i:s', strtotime($value['brush_date']));
                $areaId = (new UserInfo())->getAreaById($value['user_id']);
                $data[$key][] = ArrayHelper::getValue(UserInfo::$areaArr, $areaId, '');
				$data[$key][] = (new UserInfo())->getShift($value['user_id']);
            }
        }
        $fileName = '洗鞋机刷卡列表_' . time() . '.xlsx';
        $header   = ['刷卡人','卡号', '刷卡时间', '所属区域', '所属班别'];
        array_unshift($data, $header); //添加头部标题
        return $this->json(200, $fileName, $data);
    }


}
