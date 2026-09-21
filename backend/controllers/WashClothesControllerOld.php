<?php

namespace backend\controllers;

use Yii;
use backend\models\WashClothesUser;
use backend\models\WashClothesCommon;
use yii\data\Pagination;
use common\models\Utility;
use yii\helpers\ArrayHelper;
use backend\models\UserInfo;
use backend\models\WashClothes;

class WashClothesController extends BaseController
{
    
    public function actionIndex()
    {
        $request = Yii::$app->request->queryParams;
        $searchModel = new WashClothes();
        $data = $searchModel->search($request);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        $uid                  = $this->getUid();
        $userInfo             = UserInfo::findOne(['id' => $uid]);
        return $this->render('index', [
            'request'      => $request,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'userInfo'     => $userInfo,
            'pagination'   => $pagination
        ]);
    }

    public function actionUser()
    {
        $request = Yii::$app->request->queryParams;
        $searchModel = new WashClothesUser();
        $data = $searchModel->search($request);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        return $this->render('user', [
            'request'      => $request,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination
        ]);
    }

    /**
     * 未送洗的原因
     */
    // public function actionCommon()
    // {
    //     $request = Yii::$app->request->queryParams;
    //     $pidArr = WashClothesUser::find()->where(['status' => 1])->select('pid')->distinct()->column();
    //     if(empty($pidArr)){
    //         return $this->render('common', ['dataProvider' => []]);
    //     }
    //     $startTime = ArrayHelper::getValue($request, 'start_time', '');
    //     $firstDayOfWeek = !empty($startTime) ? $startTime : date('Y-m-d 23:59:59', strtotime("this week"));

    //     $endTime = ArrayHelper::getValue($request, 'end_time', '');
    //     $lastDayOfWeek = !empty($endTime) ? $endTime : date('Y-m-d 23:59:59', strtotime("this week +6 days"));
 
    //    // $pidCommon = WashClothesCommon::find()->select('pid')->where(['>=','create_time',$firstDayOfWeek])->andWhere(['<','create_time',$lastDayOfWeek])->distinct()->column();
    //     $rfidArr   = WashClothes::find()->select('rfid')->where(['>=','invest_time',$firstDayOfWeek])->andWhere(['<','invest_time',$lastDayOfWeek])->distinct()->column();
    //     if(!empty($rfidArr)){
    //         $pidSuccessArr = WashClothesUser::find()->select(['pid'])->where(['in', 'rfid', $rfidArr])->distinct()->column();
    //         foreach($pidArr as $key => $pid){
    //             if(in_array($pid,$pidSuccessArr)){
    //                 unset($pidArr[$key]);
    //             }
    //         }
    //     }
       
    //     $query = UserInfo::find();
    //     $query->where(['in', 'pid', $pidArr]);
    //     $area = ArrayHelper::getValue($request, 'area', '');
    //     if($area){
    //         $query->andFilterWhere(['area' => $request['area']]);
    //     }
    //     $query->asArray();
    //     $dataProvider =$query->all();
    //     return $this->render('common', ['request'=> $request,'dataProvider' => $dataProvider]);
    // }
    public function actionCommon()
    {
        $request = Yii::$app->request->queryParams;
        $pidArr = WashClothesUser::find()->where(['status' => 1])->select('pid')->distinct()->column();
        if(empty($pidArr)){
            return $this->render('common', ['dataProvider' => []]);
        }
        $startTime = ArrayHelper::getValue($request, 'start_time', '');
        $firstDayOfWeek = !empty($startTime) ? $startTime : date('Y-m-d 23:59:59', strtotime("this week"));

        $endTime = ArrayHelper::getValue($request, 'end_time', '');
        $lastDayOfWeek = !empty($endTime) ? $endTime : date('Y-m-d 23:59:59', strtotime("this week +6 days"));
 
        $area = ArrayHelper::getValue($request, 'area', '');

        $washClothesUserPidArr = WashClothesUser::find()->select('pid')->where(['status' => 1])->distinct()->column();
        $weekRangesArr = $this->getWeekRanges($firstDayOfWeek, $lastDayOfWeek);
        $dataProvider  = [];
        foreach($weekRangesArr as $key => $weekArr){
            $washClothesPidArr = WashClothes::find()->select('pid')->where(['>=','invest_time',$weekArr['startWeek']])->andWhere(['<','invest_time',$weekArr['endWeek']])->andWhere(['not', ['pid' => null]])->distinct()->column();
            $diffUserPidArr = array_diff($washClothesUserPidArr, $washClothesPidArr);
            $startWeek= $weekArr['startWeek'];
            $wednesday= date("Y-m-d 10:00:00",strtotime("$startWeek Wednesday"));
            $pidCommon = WashClothesCommon::find()->select('pid')->where(['>=','create_time',$weekArr['startWeek']])->andWhere(['<','create_time',$weekArr['endWeek']])->distinct()->column();
            $pidArr = array_diff($diffUserPidArr, $pidCommon);
            
            $query = UserInfo::find();
            $query->where(['in', 'pid', $pidArr]);
            if($area){
                $query->andFilterWhere(['area' => $request['area']]);
            }
            $query->asArray();
            $userDataArr = $query->all();
            foreach($userDataArr as $user){
                $dataProvider[] = ['pid' => $user['pid'],'chinese_name'=>$user['chinese_name'],'shift'=>$user['shift'],'area'=>$user['area'],'wednesday'=>$wednesday];
            } 
        }
        $uid                  = $this->getUid();
        $userInfo             = UserInfo::findOne(['id' => $uid]);
        return $this->render('common', ['request'=> $request, 'userInfo'=> $userInfo,'dataProvider' => $dataProvider]);
    }

    //将输入的开始时间和截止时间转化为此时间段的每周开始时间和截止时间
    function getWeekRanges($startTime, $endTime) {

        $endWeek   = date("Y-m-d 23:00:00",strtotime("$startTime Sunday"));  
        $startWeek = date("Y-m-d 00:00:00",strtotime("$endWeek - 6 days"));

        $weekArr[] = ['startWeek' => $startWeek,'endWeek' => $endWeek];
        for($i=1;$i<100;$i++){
            $startTime = date("Y-m-d 00:00:00", strtotime($endWeek)+172800);
            $endWeek   = date("Y-m-d 23:00:00",strtotime("$startTime Sunday"));  
            if(strtotime($endWeek) > strtotime($endTime)){
                break;
            }
            $startWeek = date("Y-m-d 00:00:00",strtotime("$endWeek - 6 days"));
            $weekArr[] = ['startWeek' => $startWeek,'endWeek' => $endWeek];
           
        }
        return $weekArr;
    }
    
    /**
     * 导出excel
     * @param null $idlist
     * @param null $type
     */
    public function actionExprotCommon(){
        $get = Yii::$app->request->get();
        $request = [];
        if(!empty($get['query'])){
            $request = json_decode($get['query'],1);
        }
        $pidArr = WashClothesUser::find()->where(['status' => 1])->select('pid')->distinct()->column();
        if(empty($pidArr)){
            return $this->render('common', ['dataProvider' => []]);
        }
        $startTime = ArrayHelper::getValue($request, 'start_time', '');
        $firstDayOfWeek = !empty($startTime) ? $startTime : date('Y-m-d 23:59:59', strtotime("this week"));

        $endTime = ArrayHelper::getValue($request, 'end_time', '');
        $lastDayOfWeek = !empty($endTime) ? $endTime : date('Y-m-d 23:59:59', strtotime("this week +6 days"));
       
        $area = ArrayHelper::getValue($request, 'area', '');

        $washClothesUserPidArr = WashClothesUser::find()->select('pid')->where(['status' => 1])->distinct()->column();
        $weekRangesArr = $this->getWeekRanges($firstDayOfWeek, $lastDayOfWeek);
        $dataProvider  = [];
        foreach($weekRangesArr as $key => $weekArr){
            $washClothesPidArr = WashClothes::find()->select('pid')->where(['>=','invest_time',$weekArr['startWeek']])->andWhere(['<','invest_time',$weekArr['endWeek']])->andWhere(['not', ['pid' => null]])->distinct()->column();
            $pidArr = array_diff($washClothesUserPidArr, $washClothesPidArr);
            // $pidCommon = WashClothesCommon::find()->select('pid')->where(['>=','create_time',$weekArr['startWeek']])->andWhere(['<','create_time',$weekArr['endWeek']])->distinct()->column();
            // $pidArr = array_diff($diffUserPidArr, $pidCommon);
            
            $query = UserInfo::find();
            $query->where(['in', 'pid', $pidArr]);
            if($area){
                $query->andFilterWhere(['area' => $request['area']]);
            }
            $query->asArray();
            $userDataArr = $query->all();
            $startWeek= $weekArr['startWeek'];
            $collecttime = $cardtime = date('Y-m-d 10:30:00', strtotime('next Monday', strtotime($startWeek)));
          
            $wednesday= date("Y-m-d 10:00:00",strtotime("$startWeek Wednesday"));
            foreach($userDataArr as $user){
                $dataProvider[] = ['chinese_name'=>$user['chinese_name'],'pid' => $user['pid'],'clothes' => '','rfid' => '','wednesday'=>$wednesday,'collecttime'=>$collecttime,'cardtime'=>$cardtime];
            } 
        }
        $data = [];
        if(!empty($dataProvider)){
            foreach ($dataProvider as $key => $value) {
                $data[$key][] = $value['chinese_name'];
                $data[$key][] = $value['pid'];
                $data[$key][] = $value['clothes'];
                $data[$key][] = $value['rfid'];
                $data[$key][] = $value['wednesday'];
                $data[$key][] = $value['collecttime'];
                $data[$key][] = $value['cardtime'];
            } 
        }
      
        $fileName = '未清洗记录-' . date("Y-m-d H:i:s",time()). '.xlsx';
        $header   = ['姓名','PID', '静电衣名称','RFID', '清洗时间','领取时间','刷卡时间'];
        array_unshift($data, $header); //添加头部标题
        return $this->json(200, $fileName, $data);
    }

     /**
     * 批量导入数据
     * @return string
     */
    public function actionCommonImport()
    {
        $isPost = Yii::$app->request->isPost;
        if (!$isPost) {
            return $this->render('common-import');
        }
        $file = $_FILES['file'];
        if (empty($file['tmp_name'])) {
            return $this->json(100, '上传文件不能为空!');
        }
        $tmp    = explode('.', $file['name']);
        $suffix = array_pop($tmp);
        if ('xlsx' !== $suffix) {
            return $this->json(100, '上传文件无法识别，请使用模版!');
        }

        if ($file['size'] > 2097152) {
            return $this->json(100, '上传文件不能超过2MB!');
        }
        $excelData = Utility::getExcelData($file['tmp_name']);
        if (empty($excelData)) {
            return $this->json(100, '上传文件无内容!');
        }
        $return    = [
            'successCount' => 0,
            'errorCount'   => 0,
            'errorList'    => '',
        ];

        $errorList = [];
        foreach ($excelData as $item) {
            $investTime = trim($item[4]);
            $receiveTime = trim($item[5]);
            $rfid      = str_replace(' ', '', trim(strtoupper($item[3])));// 去除字符串中所有的空格并转换为大写
            $checkData = WashClothes::findOne(['rfid' => $rfid,'invest_time' => $investTime]);
            
            if($checkData){
                $errorList[] = $item[2] . '的静电服:' . $rfid . '之前已经导入完成!';
                $return['errorCount']++;
                continue;
            }else{

                $model              = new WashClothes();
                $model->rfid        = $rfid;
                $model->pid         = $item[1];
                $model->user_name   = $item[2];
                $model->create_time = date('Y-m-d H:i:s', time());
                $model->invest_time = $investTime;
                $model->receive_time= $receiveTime;
                if ($model->save(false)) {
                    $return['successCount']++;
                } else {
                    $errorList[] = $item[2] . '静电服:' . $rfid . '添加失败!';
                    $return['errorCount']++;
                }
            }
           
        }
        $return['errorList'] = join("<br>", $errorList);
        return $this->json(200, '导入结果!', $return);
    }

    public function actionCreateCommon()
    {
        $get = Yii::$app->request->get();
        return $this->render('create-common', [
            'pid'  => $get['pid']
        ]);
    }

    public function actionAddcommon()
    {
        $firstDayOfWeek = date('Y-m-d 00:00:00', strtotime("this week"));// 获取本周第一天
        $lastDayOfWeek  = date('Y-m-d 23:59:59', strtotime("this week +6 days")); // 获取本周最后一天
        $model         = new WashClothesCommon();
        $model->pid    =  Yii::$app->request->post('pid');
        $model->start_week_time = $firstDayOfWeek;
        $model->end_week_time   = $lastDayOfWeek;
        $model->create_time     = date('Y-m-d H:i:s', time());//添加时间
        $model->common          = Yii::$app->request->post('common');
        if ($model->save(false)) {
            return $this->json(200, '添加成功!');
        }
        return $this->json(100, '添加失败!!');
    }


    /**
     * 批量导入数据
     * @return string
     */
    public function actionImport()
    {
        $isPost = Yii::$app->request->isPost;
        if (!$isPost) {
            return $this->render('import');
        }
        $file = $_FILES['file'];
        if (empty($file['tmp_name'])) {
            return $this->json(100, '上传文件不能为空!');
        }
        $tmp    = explode('.', $file['name']);
        $suffix = array_pop($tmp);
        if ('xlsx' !== $suffix) {
            return $this->json(100, '上传文件无法识别，请使用模版!');
        }

        if ($file['size'] > 2097152) {
            return $this->json(100, '上传文件不能超过2MB!');
        }
        $excelData = Utility::getExcelData($file['tmp_name']);
        if (empty($excelData)) {
            return $this->json(100, '上传文件无内容!');
        }
        $return    = [
            'successCount' => 0,
            'errorCount'   => 0,
            'errorList'    => '',
        ];

        $errorList = [];
        foreach ($excelData as $item) {
            $rfid      = str_replace(' ', '', trim(strtoupper($item[1])));// 去除字符串中所有的空格并转换为大写
            $checkData = WashClothesUser::findOne(['rfid' => $rfid]);
            if($checkData){
                $errorList[] = $item[0] . '的静电服:' . $rfid . '之前已经导入完成!';
                $return['errorCount']++;
                continue;
            }else{
                $checkWashClothesData = WashClothesUser::findOne(['user_name' => trim($item[0])]);
                if($checkWashClothesData){
                    $checkWashClothesData->status = 2;
                    $checkWashClothesData->save(false);
                }
                $model              = new WashClothesUser();
                $model->user_name   = trim($item[0]);
                $model->str         = trim(strtoupper($item[1]));
                $model->rfid        = $rfid;
                $model->pid         = trim($item[2]);
                $model->max_number  = 26;
                $model->status      = 1;
                $createTime         = trim($item[3]);
                $model->clerk_email = trim($item[4]);
                if (!empty($createTime)) {
                    $model->end_time = date("Y-m-d H:i:s",strtotime("+1years",strtotime(gmdate('Y-m-d H:i:s', \PHPExcel_Shared_Date::ExcelToPHP($createTime)))));
                    $createTime = date('Y-m-d H:i:s', strtotime(gmdate('Y-m-d H:i:s', \PHPExcel_Shared_Date::ExcelToPHP($createTime))));
                  
                }else{
                    $model->end_time = '';
                }
                $model->create_time = $createTime;
                $model->year        = date('Y', strtotime($createTime));
                if ($model->save(false)) {
                    $return['successCount']++;
                } else {
                    $errorList[] = $item[0] . '静电服:' . $rfid . '添加失败!';
                    $return['errorCount']++;
                }
            }
           
        }
        $return['errorList'] = join("<br>", $errorList);
        return $this->json(200, '导入结果!', $return);
    }

    /**
     * 导出数据
     * @return string
     */
    public function actionExport()
    {
        $query = WashClothes::find();
        $query->alias('wc');
        $query->select(['wc.*','u.shift','u.area']);
        $query->leftJoin(UserInfo::tableName() . ' u', 'u.pid = wc.pid');
        $query->orderBy(['wc.id' => SORT_DESC]);
        $query->asArray();
        $dataProvider = $query->all();
        $data = [];
        if (!empty($dataProvider)) {
            foreach ($dataProvider as $key => $value) {
                $data[$key][] = $value['id'];
                $data[$key][] = $value['user_name'];
                $data[$key][] = $value['rfid'];
                $data[$key][] = $value['pid'];
                $data[$key][] = ArrayHelper::getValue(UserInfo::$shiftArr, $value['shift'], '');
                $data[$key][] = ArrayHelper::getValue(UserInfo::$areaArr, $value['area'], '');
                $data[$key][] = $value['invest_time'];
                $data[$key][] = $value['receive_time'];
                $data[$key][] = $value['create_time'];
            }
        }
        $fileName = '清洗列表_' . time() . '.xlsx';
        $header   = ['ID', '姓名', 'RFID', 'PID','班别', '区域','清洗时间','领取时间','刷卡时间'];
        array_unshift($data, $header); //添加头部标题
        return $this->json(200, $fileName, $data);
    }

    /**
     * 修改名称
     * @return string
     */
    public function actionUpdate(){

        if(Yii::$app->request->isPost){
            $post  = Yii::$app->request->post();
            $rfid   = str_replace(' ', '', trim(strtoupper($post['str'])));// 去除字符串中所有的空格并转换为大写
            $check = WashClothesUser::find()->where(['str' => $rfid])->andWhere(['!=', 'id', $post['id']])->one();
            if($check){
                return $this->json(100, 'RFID已存在!');
            }
           
            $model = WashClothesUser::findOne($post['id']);
            if($model){
                $model->user_name  = trim($post['user_name']);
                $model->status     = $post['status'];
              //  $model->updated_at = date('Y-m-d H:i:s', time());
                $model->str        = trim($post['str']);
                $model->rfid       = $rfid;
                $model->pid        = trim($post['pid']);
                if ($model->save(false)) {
                    return $this->json(200, '修改成功!');
                }
                return $this->json(100,'修改失败!');
            }
            return $this->json(100,'数据异常!');
        }
        $id = Yii::$app->request->get('id');
        $model = WashClothesUser::findOne($id);
        return $this->render('update',['model' => $model]);

    }

}
