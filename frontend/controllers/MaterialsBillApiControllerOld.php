<?php

namespace frontend\controllers;

use Yii;
use backend\models\UserInfo;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use backend\models\ShelfMaterials;
use backend\models\ShelfMaterialsBig;
use backend\models\ShelfMaterialsModel;
use backend\models\ShelfMaterialsBill;
use backend\models\ShelfMaterialsBillRelation;
use backend\models\ShelfMaterialsBillQr;
use backend\models\ShelfMaterialsBillAbnormal;
use backend\models\ShelfMaterialsBillLog;

class MaterialsBillApiController extends FrontendBaseController
{
    /**
     * 待处理列表接口
     * 请求方式 POST
     */
    public function actionGetApply()
    {
        //提交方式为POST
        $isPost = Yii::$app->request->isPost;
        if (!$isPost) {
            return $this->outputJson('false', '请求方法不正确!');
        }

        $post = Yii::$app->request->post();
        if (!$post || !is_array($post)) {
            return $this->outputJson('false', '请求参数不能为空!');
        }
        $modelbilllog       = new ShelfMaterialsBillLog();
        $modelbilllog->log  = json_encode($post);
        $modelbilllog->save();
        //检查参数
        $base = ['uid', 'page'];
        $this->checkParameter($post, $base);
        if ($post['page'] < 1) {
            return $this->outputJson('false', '分页参数不能小于1!');
        }

        $status = ShelfMaterialsBill::STATUS_PENDING;
        $data   = $this->materialsBillData($post,$status);
        if(!$data['status']){
            return $this->outputJson('false', $data['message']);
        }
        $modelbilllog       = new ShelfMaterialsBillLog();
        $modelbilllog->log  = json_encode($data);
        $modelbilllog->save();
        return $this->outputJson('true', 'success', $data['data']);
    }

    /**
     * 获取物料单数据
     */
    public function materialsBillData($post = [],$status){
        $result = ['status' => false, 'message' => '','data' => []];
       
        //检查是大小物料
        $type = 0;
        $userInfoModel  = UserInfo::findOne(['id' => $post['uid']]);
        if (!$userInfoModel) {
            $result['message'] = '用户不存在!';
            return $result;
        }
        //大小材料
        if ($userInfoModel->section == UserInfo::SECTION_NON) {
            $type = 1;//小材料
        }elseif ($userInfoModel->section == UserInfo::SECTION_LEADED) {
            $type = 2;//大材料
        }
        if ($type == 0) {
            $result['message'] = '用户所属分部错误,只能Non-lead和Leaded 能使用!';
            return $result;
        }
        /*
		// 因为之前 Compound历史打印出不来 所以注释掉了这行代码,改为定时任务,每5分钟执行一次 定时任务地址 /shelf-materials-bill-cron/index  加在了backstage > TimingData 5min.bat中
        //先更新一下物料单的状态
        $materialsBillIdArr = ShelfMaterialsBill::find()->select(['id'])->where(['type' => $type, 'status' => $status])->column();
        if (empty($materialsBillIdArr)) {
            $result['status'] = true;
            return $result;
        }

        //查询所有型号已完成的数量
        foreach ($materialsBillIdArr as $id) {
            $shelfMaterialsBillRelation = ShelfMaterialsBillRelation::find()->where(['bill_id' => $id])->asArray()->all();
            if (empty($shelfMaterialsBillRelation)) {
                continue;
            }
            $checkNUmber   = count($shelfMaterialsBillRelation); //标志位所有型号数量
            $successNumber = 0; //标志位用于统计已完成的型号
            foreach ($shelfMaterialsBillRelation as $value) {
                $total = $value['print_number'] + $value['abnormal_number'];
                //判断申请的数量是否等于打印数量加异常数量
                if ($value['apply_number'] != $total) {
                    continue;
                }
                $successNumber++;
            }

            //判断所有打印完成的型号是否和申请的型号数量相等
            if ($successNumber == $checkNUmber) {
                $model         = ShelfMaterialsBill::findOne($id);
                $model->status = ShelfMaterialsBill::STATUS_SUCCESS;
                if (!$model->save()) {
                    $result['message'] = '物料单数据错误!';
                    return $result;
                }
            }
        }
		*/
        //分页获取数据
        $billData   = [];
        $offset = ($post['page'] - 1) * 10;
        $query  = ShelfMaterialsBill::find();
        $query->where(['type' => $type, 'status' => $status]);
        $query->offset($offset);
        $query->limit(10);
        $query->orderBy(['id' => SORT_DESC]);
        $shelfMaterialsBill = $query->asArray()->all();
        if (empty($shelfMaterialsBill)) {
            $result['status']  = true;
            $result['message'] = 'success';
            return $result;
        }

        //数据不为空
        foreach ($shelfMaterialsBill as $key => $value) {
            $user       = UserInfo::findOne(['id' => $value['user_id']]);
            $chineseName = empty($user) ? '' : $user->chinese_name;
            $insertArr   = [];
            $insertArr['bill_id']        = $value['id'];
            $insertArr['document_no']    = $value['document_no'];
            $insertArr['type']           = 'Molding Compound';
            $insertArr['make_people']    = $chineseName;
            $insertArr['confirm_people'] = $chineseName;
            $insertArr['status']         = ArrayHelper::getValue(ShelfMaterialsBill::$statusArr, $value['status']);
            $insertArr['create_time']    = $value['create_time'];
            $billData[]                  = $insertArr;
        }
        $result['status']  = true;
        $result['message'] = 'success';
        $result['data']    = $billData;
        return $result;
    }

    /**
     * 历史物料单接口
     * 请求方式 POST
    */
    public function actionGetHistory()
    {
        //提交方式为POST
        $isPost = Yii::$app->request->isPost;
        if (!$isPost) {
            return $this->outputJson('false', '请求方法不正确!');
        }

        $post = Yii::$app->request->post();
        if (!$post || !is_array($post)) {
            return $this->outputJson('false', '请求参数不能为空!');
        }

        //检查参数
        $base = ['uid', 'page'];
        $this->checkParameter($post, $base);
        if ($post['page'] < 1) {
            return $this->outputJson('false', '分页参数不能小于1!');
        }

        $status = ShelfMaterialsBill::STATUS_SUCCESS;
        $data = $this->materialsBillData($post,$status);
        if(!$data['status']){
            return $this->outputJson('false', $data['message']);
        }
        return $this->outputJson('true', 'success', $data['data']);
    }

    /**
     * 查看物料单接口
     * 请求方式 POST
     */
    public function actionGetBillList()
    {
        //提交方式为POST
        $isPost = Yii::$app->request->isPost;
        if (!$isPost) {
            return $this->outputJson('false', '请求方法不正确!');
        }
        $post = Yii::$app->request->post();
        if (!$post || !is_array($post)) {
            return $this->outputJson('false', '请求参数不能为空!');
        }
        //检查参数是否为空
        foreach ($post as $key => $value) {
            if (empty($value)) {
                return $this->outputJson('false', "请求参数'" . $key . "'不能为空!");
            }
        }
        //检查参数
        $base = ['bill_id'];
        $this->checkParameter($post, $base);

        $data = [];
        $shelfMaterialsBillRelation = ShelfMaterialsBillRelation::find()->where(['bill_id' => $post['bill_id']])->asArray()->all();
        if (empty($shelfMaterialsBillRelation)) {
            return $this->outputJson('true', 'success', $data);
        }

        foreach ($shelfMaterialsBillRelation as $key => $value) {
            $query  = ShelfMaterialsModel::findOne(['id' => $value['model_id']]);
            if (!$query) {
                return $this->outputJson('false', 'Part No 不能为空!');
            }
            $insertArr   = [];
            $insertArr['part_no']         = $query->part_no;
            $insertArr['apply_number']    = $value['apply_number'];
            $insertArr['print_number']    = $value['print_number'];
            $insertArr['abnormal_number'] = $value['abnormal_number'];
            $printSuccess = false;
            $total        = $value['print_number'] + $value['abnormal_number'];
            if ($value['apply_number'] == $total) {
                $printSuccess = true;
            }
            $insertArr['print_success']   = $printSuccess;
            $data[]                       = $insertArr;
        }
        return $this->outputJson('true', 'success', $data);
    }

    /**
     * 查看历史物料单接口
     * 请求方式 POST
     */
    public function actionGetHistoryBill()
    {
        //提交方式为POST
        $isPost = Yii::$app->request->isPost;
        if (!$isPost) {
            return $this->outputJson('false', '请求方法不正确!');
        }
        $post = Yii::$app->request->post();
        if (!$post || !is_array($post)) {
            return $this->outputJson('false', '请求参数不能为空!');
        }
        //检查参数是否为空
        foreach ($post as $key => $value) {
            if (empty($value)) {
                return $this->outputJson('false', "请求参数'" . $key . "'不能为空!");
            }
        }
        //检查参数
        $base = ['bill_id'];
        $this->checkParameter($post, $base);

        $data = [];
        $shelfMaterialsBillRelation = ShelfMaterialsBillRelation::find()->where(['bill_id' => $post['bill_id']])->asArray()->all();
        if (empty($shelfMaterialsBillRelation)) {
            return $this->outputJson('true', 'success', $data);
        }

        foreach ($shelfMaterialsBillRelation as $key => $value) {
            $query  = ShelfMaterialsModel::findOne(['id' => $value['model_id']]);
            if (!$query) {
                return $this->outputJson('false', 'Part No 不能为空!!');
            }
            $insertArr   = [];
            $insertArr['part_no']         = $query->part_no;
            $insertArr['apply_number']    = $value['apply_number'];
            $insertArr['received_number'] = $value['print_number'];
            $insertArr['abnormal_number'] = $value['abnormal_number'];
            $data[]                       = $insertArr;
        }
        return $this->outputJson('true', 'success', $data);
    }

    /**
     * 扫描二维码打印接口
     * 请求方式 POST
     */
    public function actionGetQrcode()
    {
        //提交方式为POST
        $isPost = Yii::$app->request->isPost;
        if (!$isPost) {
            return $this->outputJson('false', '请求方法不正确!');
        }
        $post = Yii::$app->request->post();
        if (!$post || !is_array($post)) {
            return $this->outputJson('false', '请求参数不能为空!');
        }
        //检查参数是否为空
        foreach ($post as $key => $value) {
            if (empty($value)) {
                return $this->outputJson('false', "请求参数'" . $key . "'不能为空!");
            }
        }
         $modelbilllog       = new ShelfMaterialsBillLog();
         $modelbilllog->log  = json_encode($post);
         $modelbilllog->save();

        //检查参数
        $base = ['type', 'qrcodestr', 'bill_id','uid'];
        $this->checkParameter($post, $base);
        $userInfoModel  = UserInfo::findOne(['id' => $post['uid']]);
        if (!$userInfoModel) {
            return $this->outputJson('false', '用户不存在!');
        }
        //物料表查询
        $ShelfMaterialsBillModel  = ShelfMaterialsBill::findOne(['id' => $post['bill_id']]);
        if (!$ShelfMaterialsBillModel) {
            return $this->outputJson('false', '物料不存在!');
        }
        $billType = $ShelfMaterialsBillModel->type; //1小材料 2大材料

        //匹配扫描出的结果
        $qrCodeArr  = explode(';', $post['qrcodestr']);
        if (count($qrCodeArr) < 5) {
            return $this->outputJson('false', "扫描二维码参数缺失!");
        }

        $partNo  = $qrCodeArr[0];
        $lotNo   = $qrCodeArr[1];
        $boxStr  = $qrCodeArr[2];
        $mfgDate = $qrCodeArr[3];
        $expDate = $qrCodeArr[4];
        
        if($post['type'] == 2){
            //小材料一箱检查
            $contNoArr = $lotNo . '.' . $boxStr . 'A'; 
            //大材料一箱检查
            if($billType == 2){
                $contNoArr = $lotNo . '.' . $boxStr;
            }
            //检查物料是否已经添加过了
            $gertShelfMaterialsBillQr  = ShelfMaterialsBillQr::find()->where(['cont_no' => $contNoArr,'part_no' => $partNo,'type'=>$billType])->asArray()->one();
            if ($gertShelfMaterialsBillQr) {
                return $this->outputJson('false', '此二维码已打印!');
            }
        }
    
        //先从表中获取设置的过期时间
        $shelfMaterialsModel  = ShelfMaterialsModel::findOne(['part_no' => $partNo]);
        if (!$shelfMaterialsModel) {
            return $this->outputJson('false', 'Part No 不存在!');
        }

        //根据bill_id和model_id从中间表获取数据
        $shelfMaterialsBillRelation  = ShelfMaterialsBillRelation::findOne(['bill_id' => $post['bill_id'], 'model_id' => $shelfMaterialsModel->id]);
        if (!$shelfMaterialsBillRelation) {
            return $this->outputJson('false', '关联数据不存在!');
        }

        //判断申请的数量是否等于打印数量加异常数量
        $total = $shelfMaterialsBillRelation->print_number + $shelfMaterialsBillRelation->abnormal_number;
        if ($shelfMaterialsBillRelation->apply_number == $total) {
            return $this->outputJson('false', '本型号：'.$partNo.'的物料单编码已经全部打印完成!');
        }
        
        //检查申请数量是否接近打印数量+异常数量
        $checkNumber = $total + 0.5;
        if ($post['type'] == 2 && $checkNumber >= $shelfMaterialsBillRelation->apply_number) {
            return $this->outputJson('false', '您只能申请一袋!');
        }
       
        $baseTime      = time();
        $getStartTime  = ShelfMaterialsBillQr::find()->select('start_time')->where(['bill_id' => $post['bill_id'],'part_no' => $partNo])->orderBy(['id' => SORT_ASC])->scalar();
        if ($getStartTime) {
            $baseTime = strtotime($getStartTime);
        }
        $timing = intval($shelfMaterialsBillRelation->timing);
        if($timing >= 0){
            $nowTime = $baseTime - $timing * 3600; //当前时间-页面配置的调整日期
        }else{
            $nowTime = $baseTime + abs($timing) * 3600; //当前时间+页面配置的调整日期
        }

        //解冻时间
        $thawingTime = $shelfMaterialsModel->thawing_time;
        $compareTime = $nowTime + $shelfMaterialsModel->expiry_time * 3600;

        //如果当前时间加上设置的过期时间大于扫描二维码的物料过期时间就报警
        if($compareTime > strtotime($expDate)){
           return $this->outputJson('false', 'WARN');
        }

       // 调用Socket 获取12NC
        $getMaterialpart = $this->getMaterialpart($partNo);
        if(!$getMaterialpart['status']){
            return $this->outputJson('false',$getMaterialpart['message']);
        }

      
        $data    = $resultdata = []; //最后返回给前端的数据
        $insertDate                  = []; //拼接数据用与保存数据
        $endTime                     = $nowTime + $thawingTime * 3600;//解冻完成日期
        $expireTime                  = $nowTime + $shelfMaterialsModel->expiry_time * 3600;  //解冻过期时间
        $insertDate['end_time']      = date('Y-m-d H:i:s', $endTime); //解冻完成日期 领取日期+后台配置的解冻时间
        $insertDate['start_time']    = date('Y-m-d H:i:s', $nowTime); //领取日期  当前系统时间
        $insertDate['expire_time']   = date('Y-m-d H:i:s', $expireTime); //解冻过期日期  解冻完成日期+后台配置的解冻过期时间
        $insertDate['create_time']   = date('Y-m-d H:i:s', $nowTime);
        $insertDate['bill_id']       = $post['bill_id'];
        $insertDate['relation_id']   = $shelfMaterialsBillRelation->id;
        $insertDate['part_no']       = $resultdata['part_no'] = $partNo;
        $insertDate['lot_no']        = $resultdata['lot_no']  = $lotNo;
        $insertDate['boxstr']        = $boxStr;
        $insertDate['material_part'] = $resultdata['material_part'] = $getMaterialpart['data']['MATERIALPART']; //返回的12NC
        $insertDate['no_box']        = $resultdata['no_box'] = $shelfMaterialsModel->no_box;
        $resultdata['end_time']      = date('Y-m-d H:i', $endTime); //解冻完成日期 领取日期+后台配置的解冻时间
        $resultdata['start_time']    = date('Y-m-d H:i', $nowTime); //领取日期  当前系统时间
        $resultdata['expire_time']   = date('Y-m-d H:i', $expireTime); //解冻过期日期  解冻完成日期+后台配置的解冻过期时间
       
        //拼接申请Compound 参数数据
        $paramArr['materialPartName']               = $getMaterialpart['data']['MATERIALPART']; //返回的12NC
        $paramArr['MaterialManufacturerExpiryDate'] = date('m/d/Y H:i:s', strtotime($expDate)); //箱体过期日期
        $paramArr['MaterialExpiryTimestamp']        = date('m/d/Y H:i:s', $expireTime); //解冻过期日期
        $paramArr['MaterialThawingTimestamp']       = date('m/d/Y H:i:s', $endTime); //解冻完成日期
        $paramArr['MaterialWithdrawalTimestamp']    = date('m/d/Y H:i:s', $nowTime); //领取日期
        $paramArr['MaterialVendorLotNumber']        = $lotNo;
        $paramArr['operator']                       = $userInfoModel->wbi_code;

        $contCharArr = ['A'=>'B','B'=>'C','C'=>'D','D'=>'E','E'=>'F','F'=>'G','G'=>'H','H'=>'I','I'=>'J','J'=>'K','K'=>'L','L'=>'M','M'=>'N','N'=>'O','O'=>'P','P'=>'Q','Q'=>'R','R'=>'S','S'=>'T','T'=>'U','U'=>'V','V'=>'W','W'=>'X','X'=>'Y','Y'=>'Z'];
        //小材料
        if($billType == 1){
            //一箱
            if ($post['type'] == 2) {
                $paramArr['qty'] = "5";  //小材料每袋一袋 5公斤
                $smallContNo = '';
                $gertShelfMaterialsBillQr  = ShelfMaterialsBillQr::find()->where(['boxstr'=>$boxStr,'lot_no'=>$lotNo,'type' => 1])->orderBy(['id' => SORT_DESC])->one();
                if ($gertShelfMaterialsBillQr) {
                    $char = substr($gertShelfMaterialsBillQr->cont_no, -1);
                    if(!is_numeric($char)){//判断是否是字符   
                        if($char == 'Y'){
                            return $this->outputJson('false', '物料单二维码无法打印!');
                        }
                        $checkContNo =  $lotNo . '.' . $boxStr . 'A';
                        $checkBillQrSelect  = ShelfMaterialsBillQr::find()->where(['cont_no'=>$checkContNo,'type' => 1])->one();
                        if ($checkBillQrSelect) {
                            $smallContNo = $lotNo . '.' . $boxStr . $contCharArr[$char];
                        }else{
                            $smallContNo = $checkContNo; 
                        }
                    }else{
                        $smallContNo = $lotNo . '.' . $boxStr . 'A';  
                    }
                }else{
                    $smallContNo = $lotNo . '.' . $boxStr . 'A';
                }
                //这是第一袋
                $paramArr['materialLotId'] = $smallContNo;
                $postMaterialsData = $this->postMaterialsData($paramArr);//调用Socket 申请Compound
                if(!$postMaterialsData['status']){
                    return $this->outputJson('false',$postMaterialsData['message']);
                }
                $insertDate['type']       = 1; 
                $insertDate['big_select'] = 2;
                $insertDate['cont_no'] = $resultdata['cont_no'] = $smallContNo;
                $data[] = $resultdata;
                $insert = (new ShelfMaterialsBillQr())->create($insertDate);
                if (!$insert) {
                    return $this->outputJson('false', '数据添加异常!');
                }
                
                //保存小材料
                $this->insertMaterials($insertDate,$post['bill_id']);

                $smallContNoSecond = $lotNo . '.' . $boxStr . 'B';
                $char = substr($smallContNo, -1);
                if(!is_numeric($char)){//判断是否是字符   
                    if($char == 'Y'){
                        return $this->outputJson('false', '物料单二维码无法打印!');
                    }
                    $checkBillQrSelect  = ShelfMaterialsBillQr::find()->where(['cont_no'=>$smallContNoSecond,'type' => 1])->one();
                    if ($checkBillQrSelect) {
                        $smallContNoSecond = $lotNo . '.' . $boxStr . $contCharArr[$char];
                    }
                }

                //这是第二袋
                $insertDate['cont_no'] = $resultdata['cont_no'] = $paramArr['materialLotId'] = $smallContNoSecond;
                $postMaterialsData = $this->postMaterialsData($paramArr);//调用Socket 申请Compound
                if(!$postMaterialsData['status']){
                    return $this->outputJson('false',$postMaterialsData['message']);
                }

                $data[] = $resultdata;
                $insert = (new ShelfMaterialsBillQr())->create($insertDate);
                if (!$insert) {
                    return $this->outputJson('false', '数据添加异常！!');
                }

                $shelfMaterialsBillRelation->print_number += 1;
                $shelfMaterialsBillRelation->update_time  = date('Y-m-d H:i:s', time());
                if (!$shelfMaterialsBillRelation->save()) {
                    return $this->outputJson('false', '保存数据错误!');
                }
                //保存小材料
                $this->insertMaterials($insertDate,$post['bill_id']);

            } elseif ($post['type'] == 1) {
                //小材料一袋
                $insertDate['big_select'] = 1;
                $insertDate['type']       = 1; 
                $smallContNo = '';
                $gertShelfMaterialsBillQr  = ShelfMaterialsBillQr::find()->where(['boxstr'=>$boxStr,'lot_no'=>$lotNo,'type' => 1])->orderBy(['id' => SORT_DESC])->one();
                if ($gertShelfMaterialsBillQr) {
                    $char = substr($gertShelfMaterialsBillQr->cont_no, -1);
                    if(!is_numeric($char)){//判断是否是字符   
                        if($char == 'Y'){
                            return $this->outputJson('false', '物料单二维码无法打印!');
                        }
                        $checkContNo =  $lotNo . '.' . $boxStr . 'A';
                        $checkBillQrSelect  = ShelfMaterialsBillQr::find()->where(['cont_no'=>$checkContNo,'type' => 1])->one();
                        if ($checkBillQrSelect) {
                            $smallContNo = $lotNo . '.' . $boxStr . $contCharArr[$char];
                        }else{
                            $smallContNo = $checkContNo; 
                        }
                    }else{
                        $smallContNo = $lotNo . '.' . $boxStr . 'A';  
                    }
                }else{
                    $smallContNo = $lotNo . '.' . $boxStr . 'A';
                }
                $paramArr['qty'] = "5";  //一袋 5公斤
                $paramArr['materialLotId'] = $insertDate['cont_no'] = $resultdata['cont_no'] = $smallContNo;
                $postMaterialsData = $this->postMaterialsData($paramArr); //调用Socket 申请Compound
                if(!$postMaterialsData['status']){
                    return $this->outputJson('false',$postMaterialsData['message']);
                }
               
                $data[] = $resultdata;
                $insert = (new ShelfMaterialsBillQr())->create($insertDate);
                if (!$insert) {
                    return $this->outputJson('false', '数据添加异常');
                }
                $shelfMaterialsBillRelation->print_number += 0.5;
                $shelfMaterialsBillRelation->update_time  = date('Y-m-d H:i:s', time());
                if (!$shelfMaterialsBillRelation->save()) {
                    return $this->outputJson('false', '保存数据错误');
                }
                
                //保存小材料
                $this->insertMaterials($insertDate,$post['bill_id']);
            }
        }else{
         
            $modelbilllog       = new ShelfMaterialsBillLog();
            $modelbilllog->log  = json_encode($insertDate);
            $modelbilllog->save();
            //大材料
            $bigContNo = '';
            if($post['type'] == 1){
                $gertShelfMaterialsBillQr  = ShelfMaterialsBillQr::find()->where(['part_no'=>$partNo,'boxstr'=>$boxStr,'lot_no'=>$lotNo,'type' => 2,'big_select'=>1])->orderBy(['id' => SORT_DESC])->one();
                if ($gertShelfMaterialsBillQr) {
                    $char = substr($gertShelfMaterialsBillQr->cont_no, -1);
                    if(!is_numeric($char)){//判断是否是字符
                        if($char == 'Y'){
                            return $this->outputJson('false', '物料单二维码无法打印!');
                        }
                        $bigContNo = $lotNo . '.' . $boxStr . $contCharArr[$char];
                    }else{
                        $modelbilllog       = new ShelfMaterialsBillLog();
                        $modelbilllog->log  = 1;
                        $modelbilllog->save();
                        $bigContNo = $lotNo . '.' . $boxStr . 'A';  
                    }
                }else{
                    $modelbilllog       = new ShelfMaterialsBillLog();
                    $modelbilllog->log  = 2;
                    $modelbilllog->save();
                    $bigContNo = $lotNo . '.' . $boxStr . 'A';
                }
            }else{
                $bigContNo = $lotNo . '.' . $boxStr;
            }
            //大材料扫描整箱时候是出两个相同的号，所以大材料是10公斤
            if($post['type'] == 1){
                $paramArr['qty'] = "5";  //一袋 5公斤
            }else{
                $paramArr['qty'] = "10"; //一箱10公斤
            }
            $insertDate['big_select'] = $post['type'] == 2 ? 2 : 1;//一箱还是一袋
            $insertDate['type']    = 2; //大材料
            $insertDate['cont_no'] = $resultdata['cont_no'] = $paramArr['materialLotId'] = $bigContNo;
            $postMaterialsData     = $this->postMaterialsData($paramArr);//调用Socket 申请Compound
            if(!$postMaterialsData['status']){
                return $this->outputJson('false',$postMaterialsData['message']);
            }
            //一袋要打印相同的号两次
            if($post['type'] == 2){
                $data[] = $resultdata;
                $data[] = $resultdata;
                $bigUnmber = 1;
            }else{
                $data[]    = $resultdata;
                $bigUnmber = 0.5;
            }
            //保存打印数量
            $shelfMaterialsBillRelation->print_number += $bigUnmber;
            $shelfMaterialsBillRelation->update_time  = date('Y-m-d H:i:s', time());
            if (!$shelfMaterialsBillRelation->save()) {
                return $this->outputJson('false', '保存数据错误!');
            }
            $insert = (new ShelfMaterialsBillQr())->create($insertDate);
            if (!$insert) {
                return $this->outputJson('false', '数据添加异常！!');
            }
            
            //保存大小材料
            $this->insertMaterials($insertDate,$post['bill_id']);
        }

       
        return $this->outputJson('true', 'success', $data);
    }

    /**
     * 保存物料
     * 
    */
    public function insertMaterials($data,$billId)
    {

        //检查物料获取大小材料
        $billModel         = ShelfMaterialsBill::findOne($billId);
        if (!$billModel) {
            return 1;
        }
        
        $insertData = ['cont_no' => $data['cont_no'], 'part_no' => $data['part_no'], 'lot_no' => $data['lot_no'], 'start_time' => $data['start_time'], 'end_time' => $data['end_time'], 'expire_time' => $data['expire_time']];
        //小材料
        if($billModel->type == 1){
            //检查物料是否已经存在
            $materialsModel = ShelfMaterials::findOne(['cont_no' => trim($data['cont_no'])]);
            if ($materialsModel) {
                return 1;
            }
            //添加物料
            $result = (new ShelfMaterials())->create($insertData);
            if (!$result) {
                return 1;
            }
        }else{
            // 大材料
            $materialsModel = ShelfMaterialsBig::findOne(['cont_no' => trim($data['cont_no'])]);
            if ($materialsModel) {
                return 1;
            }
            //添加物料
            $result = (new ShelfMaterialsBig())->create($insertData);
            if (!$result) {
                return 1;
            }
        }

       return 1;
    }


    private $socket;

    /**
     * 通过Socket 接口获取12NC
     */
    public function getMaterialpart($partNo = '')
    {
        $status = ['status' => false, 'message' => '', 'data' => []];
        if ($partNo == '') return $status;
        error_reporting(E_ALL);
        set_time_limit(0);

        $param = [
            "subject" => "ATTJ.REQ.MES.ONLINE_QUERY.*",
            "eqpName" => "",
            "appName" => "MFGBE1",
            "data"    => [
                "MESOnlineQueryRequest" => [
                    "appName"          => "MFGBE1",
                    "onlineQuerySetup" => "EI_IsMaterialInfoExist",
                    "operator"         => "",
                    "requestType"      => "byParms",
                    "parameters"       => [
                        "PARAM" => $partNo
                    ]
                ]
            ]
        ];
      
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $result       = socket_connect($this->socket, 'fsbgengateway-attj-f5.wbi.nxp.com', '28555');
        if ($result >= 0) {
            $msg =  Json::encode($param);
            if (!socket_write($this->socket, $msg, strlen($msg))) {
                $this->close();
                $status['message'] = '数据获取失败！';
                return $status;
            } else {
                while ($out = socket_read($this->socket, 8192)) {
                    $result = Json::decode($out);
                    if ($result['MESOnlineQueryResponse']['mesErrorCode'] == '') {
                        $status['status']  = true;
                        $status['message'] = 'Success';
                        $status['data']    = ['MATERIALPART' => $result['MESOnlineQueryResponse']['rpsData']['__row'][0]['MATERIALPART']];
                        $this->close();
                        return $status;
                    } else {
                        $status['message'] = $result['MESOnlineQueryResponse']['errorText'];
                        $this->close();
                        return $status;
                    }
                }
            }
        }
        $this->close();
        $status['message'] = 'Socket 链接失败！';
        return $status;
    }

    /**
     * 通过Socket 申请Compound
     */
    public function postMaterialsData($paramArr = [])
    {
        $status = ['status' => false, 'message' => ''];
        if (empty($paramArr)) return $status;
        error_reporting(E_ALL);
        set_time_limit(0);

        $param = [
            "subject" => "ATTJ.REQ.MES.SET_MATERIAL_LOT_CREATE.*",
            "eqpName" => "",
            "appName" => "MFGBE1",
            "data" => [
                "SetMaterialLotCreateRequest" => [
                    "appName"              => "MFGBE1",
                    "operator"             => $paramArr['operator'], //物料员工号
                    "materialLotId"        => $paramArr['materialLotId'], //原材料型号
                    "qty"                  => $paramArr['qty'], //一袋传5 一箱传10
                    "materialPartName"     => $paramArr['materialPartName'], //12NC (Deviec->API->12NC)
                    "workflowName"         => "DummyML",
                    "workflowStep"         => "Material Store",
                    "owner"                => "REWORK",
                    "factory"              => "ATTJ",
                    "materialVendor"       => "",
                    "isMaterialWithdrawal" => "true",
                    "withdrawalToWorkflow" => "",
                    "withdrawalToStep"     => "",
                    "lotModifyAttributes"  => [
                        [
                            "attributeName"  => "MaterialManufacturerExpiryDate",
                            "attributeValue" => $paramArr['MaterialManufacturerExpiryDate'] //箱体过期日期
                        ],
                        [
                            "attributeName"  => "MaterialExpiryTimestamp",
                            "attributeValue" => $paramArr['MaterialExpiryTimestamp'] //解冻过期日期
                        ],
                        [
                            "attributeName"  => "MaterialThawingTimestamp",
                            "attributeValue" => $paramArr['MaterialThawingTimestamp'] //解冻完成日期
                        ],
                        [
                            "attributeName"  => "MaterialWithdrawalTimestamp",
                            "attributeValue" => $paramArr['MaterialWithdrawalTimestamp'] //领取日期
                        ],
                        [
                            "attributeName"  => "MaterialVendorLotNumber",
                            "attributeValue" => $paramArr['MaterialVendorLotNumber']
                        ]
                    ]
                ]
            ]
        ];

        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $result = socket_connect($this->socket,  'fsbgengateway-attj-f5.wbi.nxp.com', '28555');

        if ($result >= 0) {
            $msg =  Json::encode($param);
            if (!socket_write($this->socket, $msg, strlen($msg))) {
                $this->close();
                $status['message'] = '数据获取失败！';
                return $status;
            } else {
                while ($out = socket_read($this->socket, 8192)) {
                    $result = Json::decode($out);
                    if ($result['SetMaterialLotCreateResponse']['errorText'] == '') {
                        $status['status'] = true;
                        $status['message'] = 'Success';
                        $this->close();
                        return $status;
                    } else {
                        $status['message'] = $result['SetMaterialLotCreateResponse']['errorText'];
                        $this->close();
                        return $status;
                    }
                }
            }
        }
        $this->close();
        $status['message'] = 'Socket 链接失败！';
        return $status;
    }

    //关闭Socket
    public function close()
    {
        socket_close($this->socket);
    }

    /**
     * 补打物料接口
     * 请求方式 POST
     */
    public function actionPatchwork()
    {
        //提交方式为POST
        $isPost = Yii::$app->request->isPost;
        if (!$isPost) {
            return $this->outputJson('false', '请求方法不正确!');
        }
        $post = Yii::$app->request->post();
        if (!$post || !is_array($post)) {
            return $this->outputJson('false', '请求参数不能为空!');
        }
        //检查参数是否为空
        foreach ($post as $key => $value) {
            if (empty($value)) {
                return $this->outputJson('false', "请求参数'" . $key . "'不能为空!");
            }
        }
        //检查参数
        $base = ['bill_id'];
        $this->checkParameter($post, $base);

        $data  = ShelfMaterialsBillQr::find()->where(['bill_id' => $post['bill_id']])->orderBy(['id' => SORT_DESC])->asArray()->one();
        if (!$data) {
            return $this->outputJson('false', '获取数据为空!');
        }
        $data['start_time']  = date('Y-m-d H:i',strtotime($data['start_time']));
        $data['end_time']    = date('Y-m-d H:i',strtotime($data['end_time']));
        $data['expire_time'] = date('Y-m-d H:i',strtotime($data['expire_time']));
        $data['create_time'] = date('Y-m-d H:i:s',strtotime($data['create_time']));
        if($data['big_select'] == 2){
            if($data['type'] == 2){
                $result = [0 => $data, 1 => $data ];
            }else{
                //小材料一箱
                $contCharArr = ['B'=>'A','C'=>'B','D'=>'C','E'=>'D','F'=>'E','G'=>'F','H'=>'G','I'=>'H','J'=>'I','K'=>'J','L'=>'K','M'=>'L','N'=>'M','O'=>'N','P'=>'O','Q'=>'P','R'=>'Q','S'=>'R','T'=>'S','U'=>'T','V'=>'U','W'=>'V','X'=>'W','Y'=>'X','Z'=>'Y'];
                $char = substr($data['cont_no'], -1);
                if(!is_numeric($char)){//判断是否是数字
                    if($char == 'A'){
                        $result = $data;
                    }else{
                        $length = strlen($data['cont_no']);
                        // 截取从第一个字符到倒数第二个字符的子字符串
                        $contNo     = substr($data['cont_no'], 0, $length - 1);
                        $contNoStr  = $contNo . $contCharArr[$char];
                        $firstData  = ShelfMaterialsBillQr::find()->where(['cont_no' => $contNoStr])->asArray()->one();
                        if($firstData){
                            $firstData['start_time']  = date('Y-m-d H:i',strtotime($firstData['start_time']));
                            $firstData['end_time']    = date('Y-m-d H:i',strtotime($firstData['end_time']));
                            $firstData['expire_time'] = date('Y-m-d H:i',strtotime($firstData['expire_time']));
                            $firstData['create_time'] = date('Y-m-d H:i:s',strtotime($firstData['create_time']));
                            $result = [0 => $firstData, 1 => $data];
                        }
                    }
                }else{
                    $result = $data;
                }
            }

        }else{
            $result = $data;  
        }
        return $this->outputJson('true', 'success', $result);
    }

    /**
     * 自定义补打物料接口
     * 请求方式 POST
     */
    public function actionCustomPatchwork()
    {
        //提交方式为POST
        $isPost = Yii::$app->request->isPost;
        if (!$isPost) {
            return $this->outputJson('false', '请求方法不正确!');
        }
        $post = Yii::$app->request->post();
        if (!$post || !is_array($post)) {
            return $this->outputJson('false', '请求参数不能为空!');
        }
        //检查参数是否为空
        foreach ($post as $key => $value) {
            if (empty($value)) {
                return $this->outputJson('false', "请求参数'" . $key . "'不能为空!");
            }
        }
        //检查参数
        $base = ['bill_id'];
        $this->checkParameter($post, $base);
        $data = ShelfMaterialsBillQr::find()->select(['part_no'])->where(['bill_id' => $post['bill_id']])->groupBy('part_no')->column();
        return $this->outputJson('true', 'success', $data);
    }

    /**
     * 自定型号下拉列表接口
     * 请求方式 POST
     */
    public function actionGetContNo()
    {
        //提交方式为POST
        $isPost = Yii::$app->request->isPost;
        if (!$isPost) {
            return $this->outputJson('false', '请求方法不正确!');
        }
        $post = Yii::$app->request->post();
        if (!$post || !is_array($post)) {
            return $this->outputJson('false', '请求参数不能为空!');
        }
        //检查参数
        $base = ['bill_id', 'part_no', 'page'];
        if ($post['page'] < 1) {
            return $this->outputJson('false', '分页参数不能小于1!');
        }
        $offset = ($post['page'] - 1) * 10;
        $query = ShelfMaterialsBillQr::find();
        $query->select(['id','big_select', 'type', 'cont_no']);
        $query->where(['bill_id' => $post['bill_id']]);
        if (!empty($post['part_no'])) {
            $query->andFilterWhere(['part_no' => $post['part_no']]);
        }
        $query->offset($offset);
        $query->limit(10);
        $query->orderBy(['id' => SORT_DESC]);
        $query->asArray();
        $data = $query->all();
        foreach ($data as $key => $value) {
            $data[$key]['big_select'] = $value['type'] == 1  ? 1 : $value['big_select'];//小材料显示一袋,大材料显示两袋
            unset($data[$key]['type']);
        }
        return $this->outputJson('true', 'success', $data);
    }

    /**
     * 补打物料型号接口
     * 请求方式 POST
     */
    public function actionPrint()
    {
        //提交方式为POST
        $isPost = Yii::$app->request->isPost;
        if (!$isPost) {
            return $this->outputJson('false', '请求方法不正确!');
        }
        $post = Yii::$app->request->post();
        if (!$post || !is_array($post)) {
            return $this->outputJson('false', '请求参数不能为空!');
        }
        //检查参数是否为空
        foreach ($post as $key => $value) {
            if (empty($value)) {
                return $this->outputJson('false', "请求参数'" . $key . "'不能为空!");
            }
        }
        //检查参数
        $base = ['qr_id'];
        $this->checkParameter($post, $base);

        //获取数据
        $data = ShelfMaterialsBillQr::find()->where(['id' => $post['qr_id']])->asArray()->one();
        if (!$data) {
            return $this->outputJson('false', '数据不存在!');
        }
        $data['start_time']  = date('Y-m-d H:i',strtotime($data['start_time']));
        $data['end_time']    = date('Y-m-d H:i',strtotime($data['end_time']));
        $data['expire_time'] = date('Y-m-d H:i',strtotime($data['expire_time']));
        $data['create_time'] = date('Y-m-d H:i:s',strtotime($data['create_time']));
      
        // $printData = [];
        // if($data['type'] ==2){
        //     if($data['big_select'] == 1){
        //         $printData = $data;
        //     }else{
        //         $printData[] = $data;
        //         $printData[] = $data;
        //     }
        // }else{
        //     $printData = $data;
        // }
        return $this->outputJson('true', 'success', $data);
    }

    /**
     * 存在过期物料-异常接口
     *
     */
    public function actionAbnormal()
    {

        //提交方式为POST
        $isPost = Yii::$app->request->isPost;
        if (!$isPost) {
            return $this->outputJson('false', '请求方法不正确!');
        }
        $post = Yii::$app->request->post();
        if (!$post || !is_array($post)) {
            return $this->outputJson('false', '请求参数不能为空!');
        }
        //检查参数是否为空
        foreach ($post as $key => $value) {
            if (empty($value)) {
                return $this->outputJson('false', "请求参数'" . $key . "'不能为空!");
            }
        }
        //检查参数
        $base = ['uid', 'bill_id'];
        $this->checkParameter($post, $base);

        //保存
        $model               = new ShelfMaterialsBillAbnormal();
        $model->user_id      = $post['uid'];
        $model->bill_id      = $post['bill_id'];
        $model->status       = 0;
        $model->create_time  = date('Y-m-d H:i:s', time());
        if (!$model->save()) {
            return $this->outputJson('false', '保存数据错误!');
        }
        return $this->outputJson('true', 'success');
    }

    /**
     * 搜索接口
     * 请求方式 POST
     */
    public function actionSearch()
    {
        //提交方式为POST
        $isPost = Yii::$app->request->isPost;
        if (!$isPost) {
            return $this->outputJson('false', '请求方法不正确!');
        }
        $post = Yii::$app->request->post();
        if (!$post || !is_array($post)) {
            return $this->outputJson('false', '请求参数不能为空!');
        }
        //检查参数是否为空
        foreach ($post as $key => $value) {
            if (empty($value)) {
                return $this->outputJson('false', "请求参数'" . $key . "'不能为空!");
            }
        }

        //检查参数
        $base = ['qrcodestr','bill_id'];
        $this->checkParameter($post, $base);

        //匹配扫描出的结果
        $qrCodeArr  = explode(';', $post['qrcodestr']);
        if (count($qrCodeArr) < 5) {
            return $this->outputJson('false', "扫描二维码参数缺失!");
        }

        $partNo  = $qrCodeArr[0];
        $lotNo   = $qrCodeArr[1];
        $boxStr  = $qrCodeArr[2];
        $data    = ShelfMaterialsBillQr::find()->select(['id','cont_no'])->where(['part_no' =>  $partNo,'lot_no'=>$lotNo,'bill_id'=>$post['bill_id']])->andFilterWhere(['like','cont_no',$lotNo.'.'.$boxStr])->asArray()->all();
        if (!$data) {
            return $this->outputJson('false', '无此二维码数据!');
        }
        return $this->outputJson('true', 'success', $data);
    }
    
      /**
     * 申请物料接口
     * 请求方式 POST
     */
    public function actionApply()
    {
        //提交方式为POST
        $isPost = Yii::$app->request->isPost;
        if (!$isPost) {
            return $this->outputJson('false', '请求方法不正确!');
        }
        $post = Yii::$app->request->post();
        if (!$post || !is_array($post)) {
            return $this->outputJson('false', '请求参数不能为空!');
        }
        //检查参数是否为空
        foreach ($post as $key => $value) {
            if (empty($value)) {
                return $this->outputJson('false', "请求参数'" . $key . "'不能为空!");
            }
        }
        //检查参数
        $base = ['cont_no', 'badge_code'];
        $this->checkParameter($post, $base);

        //检查物料批次是否存在
        $materialsBigModel = ShelfMaterialsBig::findOne(['cont_no' => $post['cont_no']]);
        if (!$materialsBigModel) {
            return $this->outputJson('false', '申请的物料不存在!');
        }

        //物料已经申请了
        if($materialsBigModel->status == ShelfMaterialsBig::STATUS_USING){
            return $this->outputJson('false', '物料已经申请了!');
        }
        
        //物料已过期
        if(time() > strtotime($materialsBigModel->expire_time)){
            return $this->outputJson('false', '物料已过期!');
        }

        //未醒料完成
        if(time() < strtotime($materialsBigModel->end_time)){
            return $this->outputJson('false', '物料未能醒料完成!');
        }
        
        //检查人员是否存在
        $userInfoModel = UserInfo::findOne(['sfc_number' => $post['badge_code']]);
        if (!$userInfoModel) {
            return $this->outputJson('false', '申请的人员不存在!');
        }

        $materialsBigModel->apply_id     = $userInfoModel->id;
        $materialsBigModel->status       = ShelfMaterialsBig::STATUS_USING;
        $materialsBigModel->library_time = date('Y-m-d H:i:s',time());
        $materialsBigModel->update_time  = date('Y-m-d H:i:s',time());
        if (!$materialsBigModel->save()) {
            return $this->outputJson('false', '更新物料状态失败!');
        }
        return $this->outputJson('true', 'success');
    }
    
}
