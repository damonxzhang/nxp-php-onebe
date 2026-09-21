<?php
namespace backend\controllers;

use Yii;
use yii\db\Query;
use yii\helpers\Json;
use yii\data\Pagination;
use backend\models\UserInfo;
use backend\models\User;
use backend\models\Shelf;
use backend\models\ShelfMaterials;
use backend\models\ShelfMaterialsBig;
use backend\models\ShelfMaterialsModel;
use backend\models\ShelfMaterialsBill;
use backend\models\ShelfMaterialsBillQr;
use backend\models\ShelfMaterialsApiLog;
use backend\models\ShelfMaterialsApiPartNo;
use backend\models\ShelfMaterialsBillAbnormal;
use backend\models\ShelfMaterialsBillRelation;
use backend\models\search\ShelfMaterialsModelSearch;
use backend\models\search\ShelfMaterialsBillSearch;
use backend\models\search\ShelfMaterialsBillAbnormalSearch;

class ShelfMaterialsBillController extends BaseController
{

    /**
     * 大小材料人员判断
     * 1为小材料 2为大材料 0 未区分
     */
    public function getUserSection()
    {
        $type      = 0;
        $id        = Yii::$app->user->getId();
        $userModel = User::findOne($id);
        if(!$userModel){
            return $type;
        }
        $userInfoModel = UserInfo::findOne($userModel ->user_id);
        if(!$userInfoModel){
            return $type;
        }
        if($userInfoModel ->section == UserInfo::SECTION_LEADED ){
            $type = 2;
        }elseif($userInfoModel ->section == UserInfo::SECTION_NON ){
            $type = 1;
        }
        return $type;
    }

    /**
     * 物料单列表
     * @return string
     */
    public function actionBill()
    {
        $request            = Yii::$app->request->queryParams;
        $request['user_id'] = $this->getUid();
        $request['status']  = ShelfMaterialsBill::STATUS_PENDING;
        $data               = $this->searchBillData($request);
        return $this->render('bill', $data);
    }

    
    /**
     * 历史物料单
     * @return string
     */
    public function actionBillHistory()
    {
        $request            = Yii::$app->request->queryParams;
        $request['user_id'] = $this->getUid();
        $request['status']  = ShelfMaterialsBill::STATUS_SUCCESS;
        $data               = $this->searchBillData($request);
        return $this->render('bill-history', $data);
    }

     /**
     * 查询物料单数据
     * @param $request
     * @return array
     */
    public function searchBillData($request)
    {
        $searchModel  = new ShelfMaterialsBillSearch();
        $data         = $searchModel->search($request);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        return [
            'request'      => $request,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination
        ];
    }

    /**
     * 物料单异常数量
     * @return string
     */
    public function actionBillAbnormalNumber()
    {
        if (Yii::$app->request->isPost) {
            $post        = Yii::$app->request->post();
            $billDataArr = explode('|',$post['abnormal_str']);

            $transaction = Yii::$app->db->beginTransaction();
            $billId      = 0;
            $relationNUmber = 0;//用于统计一共有多少个关联型号
            $successNumber  = 0; //标志位用于统计已完成的型号
            foreach($billDataArr as $value){
                if(empty($value)) continue;
                $dataArr = explode(';',$value);
                $model   = ShelfMaterialsBillRelation::findOne($dataArr[0]);
                $model->abnormal_number = $dataArr[1];
                $model->update_time     = date('Y-m-d H:i:s', time());
                $billId = $model->bill_id;
                $printNumber = $model->print_number;
                $applyNumber = $model->apply_number;
                if (!$model->save()) {
                    $transaction->rollBack();
                    return $this->json(100, '填写失败!');
                }
                $relationNUmber++;
                $total = $printNumber + $dataArr[1];
                //判断申请的数量是否等于打印数量加异常数量
                if ($applyNumber == $total) {
                    $successNumber++;
                }
            }
            //判断所有打印完成的型号是否和申请的型号数量相等
            if ($successNumber == $relationNUmber && $billId > 0) {
                $shelfMaterialsBillmodel         = ShelfMaterialsBill::findOne($billId);
                $shelfMaterialsBillmodel->status = ShelfMaterialsBill::STATUS_SUCCESS;
                if (!$shelfMaterialsBillmodel->save()) {
                    $transaction->rollBack();
                    return $this->json(100, '填写失败!!');
                }
            }

            $transaction->commit();
            return $this->json(200, '填写完成!');
        }
        $billId           = Yii::$app->request->get('id');
        $dataProvider     = ShelfMaterialsBillRelation::find()->where(['bill_id' => $billId])->asArray()->all();
        return $this->render('bill-abnormal-number', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 物料单异常列表
     * @return string
     */
    public function actionBillAbnormal()
    {
        $request            = Yii::$app->request->queryParams;
        $searchModel        = new ShelfMaterialsBillAbnormalSearch();
        $searchData         = $searchModel->search($request);
        $totalCount         = $searchData->totalCount;
        $dataProvider       = $searchData->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);

        $data =  [
            'request'      => $request,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination
        ];
        return $this->render('bill-abnormal', $data);
    }

    /**
     * 添加物料单异常
     * @return string
     */
    public function actionBillAbnormalCreate()
    {
        if (Yii::$app->request->isPost) {
           
            $post                = Yii::$app->request->post();
            $model               = ShelfMaterialsBillAbnormal::findOne(['bill_id' => $post['bill_id'],'status' => 0]);
            $model->abnormal     = trim($post['abnormal']);
            $model->status       = 1;
            $model->update_time  = date('Y-m-d H:i:s', time());
            if (!$model->save()) {
                return $this->json(100, '添加失败!');
            }
            return $this->json(200, '添加成功!');
        }
        $bill_id     = Yii::$app->request->get('bill_id');
        $shelfMaterialsBillModel  = ShelfMaterialsBill::findOne(['id' => $bill_id]);
        return $this->render('bill-abnormal-create', [
            'document_no' =>$shelfMaterialsBillModel->document_no,
            'bill_id' => $bill_id,
        ]);
    }

    /**
     * 添加物料单列表
     * @return string
     */
    public function actionBillCreate()
    {
        $type       = $this->getUserSection();
        $partNoArr  = ShelfMaterialsModel::find()->select(['id','part_no'])->where(['type' => $type])->asArray()->all();
        return $this->render('bill-create', [
            'partNoArr' => $partNoArr,
        ]);
    }

    /**
     * 根据物料型号获取中间库库存和产线库存
     */
    public function actionGetModelDetail()
    {
        $type       = $this->getUserSection();
        if($type == 0){
            return $this->json(100, '当前账号不能创建物料单!');
        }
        $post            = Yii::$app->request->post();
        $materialsModel  = ShelfMaterialsModel::findOne($post['id']);
        if (!$materialsModel) {
            return $this->json(100, '物料不存在!');
        }
       
        $data['id']       = $materialsModel->id;
        $data['part_no']  = $materialsModel->part_no;
        $partNo           = $materialsModel->part_no;
        $partNoNumberData = $this->getPartNoNumber($partNo);
        if($partNoNumberData['status'] == false){
            return $this->json(100, $partNoNumberData['message']);
        }
        //小材料
        if($type == 1){
            // $query = ShelfMaterials::find();
            // $query->where([
            //     'part_no' => $partNo,
            //     'fettle'  => ShelfMaterials::FETTLE_UP,   //上架状态
            // ]);
            // $time = date('Y-m-d H:i:s', time());
            // $query->andWhere(['in', 'status', [ShelfMaterials::STATUS_INFO,ShelfMaterials::STATUS_PENDING,ShelfMaterials::STATUS_WAIT]]);//入货架 //待领取 //待入架
            // $query->andWhere(['<', 'end_time', $time]); //当前时间大于醒料时间
            // $query->andWhere(['>', 'expire_time', $time]); //当前时间小于过期时间
            // $data['product_number'] = $query->count('id');

            // $data['expire_number']  = 0;

            // 统计
            // $total_all = ShelfMaterials::find();
            // $total_all->select('sm.id,s.status');
            // $total_all->alias('sm');
            // $total_all->leftJoin(Shelf::tableName() . ' s', 'sm.shelf_id = s.id');
            // $total_all->where(['sm.fettle'=>1]);
            // $total_all->where(['sm.part_no'=>$partNo]);
            // $total_all->andFilterWhere(['in','sm.status',[1,2,3,4]]);
            // $total_all->asArray();
            // $dataProvider = $total_all->all();
            // 统计
               
            $total_all = ShelfMaterials::find();
            $total_all->select('sm.id,s.status');
            $total_all->alias('sm');
            $total_all->leftJoin(Shelf::tableName() . ' s', 'sm.shelf_id = s.id');
            $total_all->where(['sm.fettle'=>1]);
            $total_all->andWhere(['sm.part_no'=>$partNo]);
            $total_all->andFilterWhere(['in','sm.status',[1,2,3,4]]);
            $total_all->asArray();
            $dataProvider = $total_all->all();
            $numberData =  [1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0];
            if ($dataProvider){
                foreach ($dataProvider as $value){
                    $numberData[$value['status']] += 1;
                }
            }
           
            $data['product_number'] = ($numberData[1] + $numberData[2] + $numberData[7]) / 2;
            $data['expire_number']  = ($numberData[5]) / 2;
        }else{
            //大材料
            // $query = ShelfMaterialsBig::find();
            // $query->where([
            //     'part_no' => $partNo,
            //     'status'  => ShelfMaterials::STATUS_INFO, //入货架
            //     'fettle'  => ShelfMaterials::FETTLE_UP,   //上架状态
            // ]);
            // $time = date('Y-m-d H:i:s', time());
            // $query->andWhere(['<', 'end_time', $time]); //当前时间大于醒料时间
            // $query->andWhere(['>', 'expire_time', $time]); //当前时间小于过期时间
            // $data['product_number'] = $query->count('id');

            // 统计
            $query = ShelfMaterialsBig::find();
            $query->alias('b');
            $query->select('b.end_time,b.expire_time,b.status,qr.big_select,b.part_no,b.start_time');
            $query->leftJoin(ShelfMaterialsBillQr::tableName() . ' qr', 'b.cont_no = qr.cont_no');
            $query->where(['b.status' => ShelfMaterialsBig::STATUS_INFO]);
            $query->where(['b.part_no' => $partNo]);
            $query->asArray();
            $dataProvider = $query->all();
            $numberData = [1=>0,2=>0,3=>0];
            $nowTime = time();
            if ($dataProvider){
                foreach ($dataProvider as $value){
                    $expire_time_mine = strtotime($value['expire_time']) - 24*3600;
                    if($nowTime > strtotime($value['start_time']) && $nowTime < strtotime($value['end_time'])){
                        $numberData[1] += $value['big_select'];  //醒料中
                    }
                    if($nowTime > strtotime($value['end_time']) && $nowTime < strtotime($value['expire_time']) && $value['status'] == ShelfMaterialsBig::STATUS_INFO){
                        $numberData[2] += $value['big_select'];//可领取
                    }
                    if($nowTime > $expire_time_mine && $nowTime < strtotime($value['expire_time'])){
                        $numberData[3] += $value['big_select'];  //预警中
                    }
                }
            }

            $data['product_number'] = ($numberData[1] + $numberData[2]) / 2;
            $data['expire_number']  = $numberData[3] / 2;
        }
        
        $data['middle_number']  = $partNoNumberData['number'];
        $data['api_partno_id']  = $partNoNumberData['api_partno_id'];
        return $this->json(200, 'SUCCESS',$data);
    }

    /**
     * 生成物料单号
     */
    public function actionSaveModelDetail()
    {
        $post         = Yii::$app->request->post();
        if(isset($post['timing'])){
            if (intval($post['timing']) < -72  || intval($post['timing']) >= 72) {
                return $this->json(100, '调整日期不能超过72小时!');
            }
        }
      
        $modelDataArr = explode('|',$post['model_str']);
        $modelArr     = [];
        foreach($modelDataArr as $modelStr){
            if(empty($modelStr)) continue;
            $modelArr[] = explode(';',$modelStr);
        }
      
        $partNoArr = [];
        $uid  = $this->getUid();
       //  拼接获取中间库物料接口参数
        foreach ($modelArr as $key=>$model) { 
            $apiPartNoModel  = ShelfMaterialsApiPartNo::findOne($model[1]);
            if (!$apiPartNoModel) {
                return $this->json(100, '数据错误!');
            }
            $partNoAr[$key]['part_no'] = $model[2];
            $partNoAr[$key]['qty']     = $apiPartNoModel->unit == '箱' ? $model[6] : $model[6] * $apiPartNoModel->boxbeilv;  
        }
        // 获取中间库的单据号
        $getDocumentNo = $this->getDocumentNo($partNoAr);
        if(!$getDocumentNo['status']){
            return $this->json(100, $getDocumentNo['message']);
        }
     
        $documentNo = $getDocumentNo['document_no'];
        
        //生成单号
        $transaction         = Yii::$app->db->beginTransaction();
        $model               = new ShelfMaterialsBill();
        $model->user_id      = $uid;
        $model->document_no  = $documentNo;
        $model->status       = ShelfMaterialsBill::STATUS_PENDING;
        $model->type         = $this->getUserSection();
        $model->create_time  = date('Y-m-d H:i:s', time());
        $model->update_time  = date('Y-m-d H:i:s', time());
        if (!$model->save()) {
            $transaction->rollBack();
            return $this->json(100, '生成物料单失败!');
        }
        $data   = [];
        $billId = $model->id;
        $time   = date('Y-m-d H:i:s', time());
        foreach ($modelArr as $key=>$model) {
            $insertArr                    = [];
            $insertArr['user_id']         = $uid;
            $insertArr['model_id']        = $model[0];
            $insertArr['bill_id']         = $billId;
            $insertArr['middle_number']   = $model[3];
            $insertArr['product_number']  = $model[5];
            $insertArr['apply_number']    = $model[6];
            $insertArr['abnormal_number'] = 0;
            $insertArr['create_time']     = $time;
            $insertArr['update_time']     = $time;
            $insertArr['timing']          = isset($post['timing']) ?  $post['timing'] : 0;
            $data[]                       = $insertArr;
        }
        //添加物料单对应的物料型号数据
        $insertFlowNode = Yii::$app->db->createCommand()->batchInsert(ShelfMaterialsBillRelation::tableName(), ['user_id', 'model_id', 'bill_id','middle_number','product_number', 'apply_number','abnormal_number','create_time', 'update_time','timing'], $data)->execute();
        if ($insertFlowNode <= 0) {
            $transaction->rollBack();
            return $this->json(100, '生成物料单失败!');
        }
        $transaction->commit();
        return $this->json(200, '物料单生成成功');
    }

    /**
     * 根据物料型号获取中间库库存
     * @param $partNo 物料型号
    */
    public function getPartNoNumber($partNo)
    {
        if(empty($partNo)) return '';
        $url   = "http://10.192.147.226:31113/API/getMar"; 
        $guid  = $this->random_string();
        $param = [
            "GUID" => $guid,
            "Mar"  => trim($partNo),
        ];
       
        //post请求
        $partNoData = $this->http_post_data($url, $param);
        $createData = [
            "param"   => json_encode($param),
            "result"  => $partNoData,
            "url"     => $url,
            "address" => 'getMar'
        ];
        //将提交参数记log
        (new ShelfMaterialsApiLog())->create($createData);
     
        $result = ['status' => false, 'message' => '', 'number' => 0, 'api_partno_id' => 0];
        if(!$partNoData){
            $result['message'] = '接口返回数据错误！';
            return $result;
        }
        $partNoDataArr = json_decode($partNoData,true);
        if($partNoDataArr['MessageCode'] != 400){
            $result['message'] = $partNoDataArr['MessageText'];
            return $result;
        }
        $unit     = '';
        $beilv    = 1;
        $boxBeilv = 0;
        foreach ($partNoDataArr['UnitList'] as $list) {
            if($list['ISMain'] == 'Main' || $list['ISMain'] == 'main'){
                $unit  = $list['Unit'];
                $beilv = $list['BeiLv'];
            }
            if($list['Unit'] == '箱'){
                $boxBeilv = $list['BeiLv'];
            }elseif($list['Unit'] == 'KG'){
                $boxBeilv = $list['BeiLv'];
            }
        }
        if($unit == '箱'){
            $result['status'] = true;
            $result['number'] = floor($partNoDataArr['QTY']);
            return $result;
        }
        if($boxBeilv == 0){
            $result['message'] = '主单位计算错误！';
            return $result;
        }
    
        //将返回的数据保存
        $model            = new ShelfMaterialsApiPartNo();
        $model->part_no   = $partNo;
        $model->guid      = $guid;
        $model->qty       = floor($partNoDataArr['QTY']);
        $model->all_qty   = floor($partNoDataArr['ALL_QTY']);
        $model->unit      = $unit;
        $model->beilv     = $beilv;
        $model->boxbeilv  = $boxBeilv;
        $model->create_time  = date('Y-m-d H:i:s', time());
        if (!$model->save()) {
            $result['message'] = '添加数据错误！';
            return $result;
        }  
        $result['status']        = true;
        $result['api_partno_id'] = $model->id; // 获取上一条插入的数据的ID
        //将主格式转成箱
        $number = floor($partNoDataArr['ALL_QTY']) / $boxBeilv;
        $result['number'] = intval($number * 100) / 100;//保留两位小数不四舍五入
        return $result;
    }

    /**
     * 获取中间库的单据号
     * @param $partNoArr 物料型号
    */
    public function getDocumentNo($partNoArr = [])
    {
        if(empty($partNoArr)) return '';
        $productionDetails    = [];
        foreach ($partNoArr as $key => $value) {
            $listArr             = [];
            $listArr['FromItem'] = $key + 1;
            $listArr['Item']     = $key + 1;
            $listArr['Material'] = $value['part_no'];
            $listArr['QTY']      = $value['qty'];
            $listArr['UseUnit']  = '箱';
            $productionDetails[] = $listArr;
        }
        $url   = "http://10.192.147.226:31113/API/AddProduction_Detail"; 
        $param = [
            "GUID"               => $this->random_string(),
            "production_Main"    => [ 'MaType' => 'Molding Compound' ],
            "production_Details" => $productionDetails,
        ];
        $partNoData = $this->http_post_data($url, $param);
        $createData = [
            "param"   => json_encode($param),
            "result"  => $partNoData,
            "url"     => $url,
            "address" => 'AddProduction_Detail'
        ];
        (new ShelfMaterialsApiLog())->create($createData);

        $result = ['status' => false, 'message' => '', 'document_no' => ''];
        if(!$partNoData){
            $result['message'] = '接口返回数据错误！';
            return $result;
        }
        $partNoDataArr = json_decode($partNoData,true);
        if($partNoDataArr['MessageCode'] != 400){
            $result['message'] = $partNoDataArr['MessageText'];
            return $result;
        }
        $result['status']      = true;
        $result['document_no'] = preg_replace("/插入成功,入库单号:/u", '', $partNoDataArr['MessageText']);
        return $result;  
    }

    /**
     * 获取打印条码图片
     * @param $documentNo 单据号
    */
    public function getProductionPdf($documentNo = '')
    {
        if(empty($documentNo)) return '';
        $url   = "http://10.192.147.226:31113/API/getProductionPDF"; 
        $guid  = $this->random_string();
        $param = [
            "GUID" => $guid,
            "ID"  => trim($documentNo),
        ];
        //post请求
        $documentNoData = $this->http_post_data($url, $param);
        $createData = [
            "param"   => json_encode($param),
            "result"  => $documentNoData,
            "url"     => $url,
            "address" => 'getProductionPDF'
        ];
        //将提交参数记log
        (new ShelfMaterialsApiLog())->create($createData);

        $result = ['status' => false, 'message' => ''];
        if(!$documentNoData){
            $result['message'] = '接口返回数据错误！';
            return $result;
        }
        $partNoDataArr = json_decode($documentNoData,true);
        if($partNoDataArr['MessageCode'] != 400){
            $result['message'] = $partNoDataArr['MessageText'];
            return $result;
        }
        $result['status'] = true;
        $result['message'] = $partNoDataArr['MessageText'];
        return $result;
    }
 
    /**
     * PHP post 发送数据
     * @param $url 请求url
     * @param $param 发送的数组
     * @return array
     */
    public function http_post_data($url, $param = [])
    {
        if(empty($param)) return '';
        $data = json_encode($param);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER,array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length:' . strlen($data),
                'Cache-Control: no-cache',
                'Pragma: no-cache'
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $res     = curl_exec($curl);
        $errorno = curl_errno($curl);
        if ($errorno) {
            return false;
        }
        curl_close($curl);
        return $res;
    }

    /**
     * 生成16位时间戳加随机字符串拼接的字符串
     */
    function random_string(){
        // 随机生成6位字符串
        $randomString = substr(str_shuffle(str_repeat('0123456789ABCDEFGHIJKLMNPQRSTUCWXYZ', 5)), 0, 6);
        return time() . $randomString;
    }
 
    /**
     * 打印
     * @return string
     */
    public function actionPrint()
    {
        $id      = Yii::$app->request->get('id');
        $model   = ShelfMaterialsBill::findOne(['id' => $id]);
        if(!empty($model->image_path)){
            return $this->render('print', ['path' => \Yii::$app->request->hostInfo.$model->image_path, 'baseUrl' => '']);
        }
        $data  = $this->getProductionPdf($model->document_no);
        if(!$data['status']){
            return '<h1 style="color:red;">文件获取失败！</h1>';
        }
        // 将Base64字符串转换为二进制数据
        $imageData = base64_decode($data['message']);
        $path      = 'uploads/materialsbill/' . date('Ymd') . '/pdf/';
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $fileName = time() . '.pdf';
        $filePath = $path . $fileName;
        $fp       = fopen($filePath, 'w');
        fwrite($fp, $imageData);
        fclose($fp);
        $model->image_path = '/' . $filePath;
        $model->image_name = $fileName;
        if (!$model->save()) {
            return '<h1 style="color:red;">文件获取失败！</h1>';
        }

        $baseUrl    = '';
        $path       = '/' . $filePath;
        return $this->render('print', ['path' => $path, 'baseUrl' => $baseUrl]);
    }

    /**
     * 物料型号列表
     */
    public function actionMaterialModel()
    {
        $request            = Yii::$app->request->queryParams;
        $request['type']    = $this->getUserSection();
        $searchModel        = new ShelfMaterialsModelSearch();
        $searchData         = $searchModel->search($request);
        $totalCount         = $searchData->totalCount;
        $dataProvider       = $searchData->getModels();
        $pagination         = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        $data =[
            'request'      => $request,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination
        ];
        return $this->render('material-model', $data);
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
                        if($result['MESOnlineQueryResponse']['rpsData']['__rowCount'] == 0){
                            $status['message'] = '未能查到此型号的DM 12NC';
                            $this->close();
                            return $status;  
                        }
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
    
    //关闭Socket
    public function close()
    {
        socket_close($this->socket);
    }

    /**
     * 获取DM12NC
     * @return string
     */
    public function actionGetDmNc()
    {
        $post  = Yii::$app->request->post();
        if(empty($post['part_no'])){
            return $this->json(100, '请填写PART NO!');  
        }
        // 调用Socket 获取12NC
        $getMaterialpart = $this->getMaterialpart($post['part_no']);
        if(!$getMaterialpart['status']){
            return $this->json(100, $getMaterialpart['message']);
        }
        return $this->json(200, $getMaterialpart['data']['MATERIALPART']);//返回的12NC
     }
     /**
     * 添加物料型号
     * @return string
     */
    public function actionMaterialModelCreate()
    {
        if (Yii::$app->request->isPost) {
            $post  = Yii::$app->request->post();
            $model = ShelfMaterialsModel::findOne(['part_no' => trim($post['part_no']),'dm_nc'=>trim($post['dm_nc'])]);
            if ($model) {
                return $this->json(100, '物料型号已存在!');
            }
            if($post['thawing_time'] <= 0){
                return $this->json(100, '解冻时间要大于零!');
            }
            if($post['expiry_time'] <= 0){
                return $this->json(100, '过期时间要大于零!');
            }
            $post['type']    = $this->getUserSection();
            $post['user_id'] = $this->getUid();
            $shelf = (new ShelfMaterialsModel())->create($post);
            if (!$shelf) {
                return $this->json(100, '添加失败!');
            }
            return $this->json(200, '添加成功!');
        }
        return $this->render('material-model-create');
    }

    /**
     * 更新物料型号
     * @return string
     */
    public function actionMaterialModelUpdate()
    {
        if (Yii::$app->request->isPost) {
            $post       = Yii::$app->request->post();
            $checkModel = ShelfMaterialsModel::findOne($post['id']);
            if (!$checkModel) {
                return $this->json(100, '物料型号不存在!');
            }
            if($post['thawing_time'] <= 0){
                return $this->json(100, '解冻时间要大于零!');
            }
            $model               = ShelfMaterialsModel::findOne($post['id']);
            $model->no_box       = trim($post['no_box']);
            $model->thawing_time = trim($post['thawing_time']);
            $model->expiry_time  = trim($post['expiry_time']);
            if (!$model->save()) {
                return $this->json(100, '修改失败!');
            }
            return $this->json(200, '修改成功!');
        }
        $id     = Yii::$app->request->get('id');
        $model  = ShelfMaterialsModel::findOne($id);
        return $this->render('material-model-update', ['model' => $model]);
    }


    /**
     * 大物料货架当前状况统计
     */
    public function actionTotalbig()
    {
        // 统计未领取的物料
        $query = ShelfMaterialsBig::find();
        $query->alias('b');
        $query->select('b.end_time,b.expire_time,b.status,qr.big_select,b.part_no,b.start_time');
        $query->leftJoin(ShelfMaterialsBillQr::tableName() . ' qr', 'b.cont_no = qr.cont_no');
        $query->where(['b.status' => ShelfMaterialsBig::STATUS_INFO]);
        $query->orderBy(['b.part_no' => SORT_ASC]);
        $query->asArray();
        $dataProvider = $query->all();
        // $dataProvider = ShelfMaterialsBig::find()->where(['status' => ShelfMaterialsBig::STATUS_INFO])->asArray()->all();
        $data = [];
        if ($dataProvider){
            foreach ($dataProvider as $value){
                if(!isset($data[$value['part_no']])){
                    $data[$value['part_no']] = [1=>0,2=>0,3=>0,4=>0];
                }
                $time = time();
                $expire_time_mine = strtotime($value['expire_time']) - 24*3600;
                if($time > strtotime($value['start_time']) && $time < strtotime($value['end_time'])){
                    $data[$value['part_no']][1] += $value['big_select'];  //醒料中
                }
                if($time > strtotime($value['end_time']) && $time < strtotime($value['expire_time']) && $value['status'] == ShelfMaterialsBig::STATUS_INFO){
                    $data[$value['part_no']][2] += $value['big_select'];//可领取
                }
                if($time > $expire_time_mine && $time < strtotime($value['expire_time'])){
                    $data[$value['part_no']][3] += $value['big_select'];  //预警中
                }
                if($time > strtotime($value['expire_time']) && $value['status'] == ShelfMaterialsBig::STATUS_INFO){
                    $data[$value['part_no']][4] += $value['big_select'];  //已过期
                }
               
            }
        }
        return $this->render('totalbig', ['result' => $data]);
    }
}
