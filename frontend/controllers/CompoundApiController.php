<?php
namespace frontend\controllers;

use backend\models\ShelfMaterials;
use backend\models\ShelfMaterialTotal;
use backend\models\Facility;
use backend\models\Shelf;
use backend\models\ShelfScattered;
use backend\models\ShelfActionLog;
use backend\models\ShelfActionLogAll;
use backend\models\ShelfApply;
use backend\models\UserInfo;

class CompoundApiController extends FrontendBaseController{


    /**
     * 更新 货架行为日志
     */
    
    public function actionUpdateShelfAction(){
        $updatequery = ShelfActionLogAll::find();
        $updatequery->orderby('log_id desc');
        $updatequeryData = $updatequery->one();


        $query = ShelfActionLog::find();
        if($updatequeryData){
            $query->andWhere(['>','id',$updatequeryData->log_id]);
        }
        $query->asArray();
        $dataquery = $query->all();
        // print_r($dataquery);die;
        foreach($dataquery as $value){

            if($value['action']==5){
                $jsonValue = json_decode($value['value']);
                foreach($jsonValue->materials_ids as $valuematerials){
                    $queryM = ShelfMaterials::findOne($valuematerials);
                    $model = new ShelfActionLogAll();
                    $model->user_id = $value['user_id'];
                    $model->log_id = $value['id'];
                    $model->action = $value['action'];
                    $model->lot_no = $queryM->lot_no;
                    $model->part_no = $queryM->part_no;
                    $model->cont_no = $queryM->cont_no;
                    $model->value = $value['value'];
                    $model->created_time = $value['created_time'];
                    $model->save();
                }
                // $query = ShelfActionLog::findOne(89);
                // print_r($query);die;
            }
            if($value['action']==10){
                $jsonValue = json_decode($value['value']);
                foreach($jsonValue->materials_id as $valuematerials){
                    $queryM = ShelfMaterials::findOne($valuematerials);
                    $model = new ShelfActionLogAll();
                    $model->user_id = $value['user_id'];
                    $model->log_id = $value['id'];
                    $model->action = $value['action'];
                    $model->lot_no = $queryM->lot_no;
                    $model->part_no = $queryM->part_no;
                    $model->cont_no = $queryM->cont_no;
                    $model->value = $value['value'];
                    $model->created_time = $value['created_time'];
                    $model->save();
                }
                // $query = ShelfActionLog::findOne(89);
                // print_r($query);die;
            }
            if($value['action']==2){
                $jsonValue = json_decode($value['value']);
                // 判断对象中是否有 floor
                if(isset($jsonValue->floor)){
                    $contNo = substr($jsonValue->cont_no, 2);
                    $queryM = ShelfMaterials::findOne(['cont_no'=>$contNo]);
                    $model = new ShelfActionLogAll();
                    $model->user_id = $value['user_id'];
                    $model->log_id = $value['id'];
                    $model->action = $value['action'];
                    $model->floor = $jsonValue->floor;
                    $model->wbi_id = $jsonValue->wbi_id;
                    $model->lot_no = $queryM->lot_no;
                    $model->part_no = $queryM->part_no;
                    $model->cont_no = $queryM->cont_no;
                    $model->value = $value['value'];
                    $model->created_time = $value['created_time'];
                    $model->save();
                }else{
                    foreach($jsonValue as $key2=>$value2){

                        // print_r($value2[0]->cont_no);die;
                        // $contNo = substr($jsonValue->cont_no, 2);
                        $queryM = ShelfMaterials::findOne(['cont_no'=>$value2[0]->cont_no]);
                        $model = new ShelfActionLogAll();
                        $model->user_id = $value['user_id'];
                        $model->log_id = $value['id'];
                        $model->action = $value['action'];
                        $model->floor = 1;
                        // $model->wbi_id = $jsonValue->wbi_id;
                        $model->lot_no = $queryM->lot_no;
                        $model->part_no = $queryM->part_no;
                        $model->cont_no = $queryM->cont_no;
                        $model->value = $value['value'];
                        $model->created_time = $value['created_time'];
                        $model->save();
                    }
                   
                }
                
            }
        }

        return 1;
        // print_r($query->value);die;
        // return ShelfActionLogAll::savelog(1,5,[]);
    }
    /**
     *  数据大屏接口
     */
    public function actionChart(){
        // 零散下架
        $queryScattered = ShelfScattered::find();
        $queryScattered->andFilterWhere(['>=','scattered_time' ,date('Y-m-01 00:00:00', time())]);
        $queryScattered->asArray();
        $dataScattered = $queryScattered->all();
        $data = [];
        $data['scattered'] = [];
        for($g = 1;$g <= date('d', time());$g++){
            $data['scattered'][$g] = 0;
        }
        foreach($dataScattered as $value){
            
            // 获取日期
            $date = date("d",strtotime($value['scattered_time']));
            
            // print_r($data['scattered']);die;
            if(array_key_exists((int)$date,$data['scattered'])){
                $data['scattered'][(int)$date] += $value['count'];
            }else{
                $data['scattered'][(int)$date] = 0;
            }
            
            
        }

        // 12小时内过期的
        $query12 = ShelfMaterials::find();
        $query12->andFilterWhere(['<=','expire_time' ,date('Y-m-d H:i:s',strtotime("+12 hours"))]);
        $query12->andFilterWhere(['fettle' => 1]);
        $query12->andFilterWhere(['>','shelf_id' ,0]);
        $query12->andFilterWhere(['in','status' ,[2,3]]);
        $data12 = $query12->all();
        $data['warning'] = [];
        foreach($data12 as $value){
            if(isset($data['warning'][$value['part_no']])){
                $data['warning'][$value['part_no']] = $data['warning'][$value['part_no']] + 1;
            }else{
                $data['warning'][$value['part_no']] = 1;
            }
            
        }


        // 库存 by part_no
        $queryStock = ShelfMaterials::find();
        $queryStock->andFilterWhere(['fettle' => 1]);
        $queryStock->andFilterWhere(['>','shelf_id' ,0]);
        $queryStock->andFilterWhere(['in','status' ,[2,3]]);
        $dataStock = $queryStock->all();
        $data['part_no'] = [];
        foreach($dataStock as $value){
            $part_no = substr($value['part_no'],-6);
            // 醒料
            if(strtotime($value['start_time']) <= time() && strtotime($value['end_time']) >= time() ){
                if(isset($data['part_no'][$part_no]['thaw'])){
                    $data['part_no'][$part_no]['thaw'] = $data['part_no'][$part_no]['thaw'] + 1;
                }else{
                    $data['part_no'][$part_no]['thaw'] = 1;
                }
            }
            
            // 可用
            if(strtotime($value['end_time']) < time() && strtotime($value['expire_time']) > time() ){
                if(isset($data['part_no'][$part_no]['available'])){
                    $data['part_no'][$part_no]['available'] = $data['part_no'][$part_no]['available'] + 1;
                }else{
                    $data['part_no'][$part_no]['available'] = 1;
                }
                
            }
            
        }


        // 库存 by 状态
        $data['stock_status'] = [];
        foreach($dataStock as $value){
            // 醒料
            if(strtotime($value['start_time']) <= time() && strtotime($value['end_time']) >= time() ){
                if(isset($data['stock_status']['解冻中'])){
                    $data['stock_status']['解冻中'] = $data['stock_status']['解冻中'] + 1;
                }else{
                    $data['stock_status']['解冻中'] = 1;
                }
                // if(isset($data['stock_status']['thaw'][$value['part_no']])){
                //     $data['stock_status']['thaw'][$value['part_no']] = $data['stock_status']['thaw'][$value['part_no']] + 1;
                // }else{
                //     $data['stock_status']['thaw'][$value['part_no']] = 1;
                // }
            }
            
            // 可用
            if(strtotime($value['end_time']) < time() && strtotime($value['expire_time']) > time() ){
                if(isset($data['stock_status']['可领取'])){
                    $data['stock_status']['可领取'] = $data['stock_status']['可领取'] + 1;
                }else{
                    $data['stock_status']['可领取'] = 1;
                }
                // if(isset($data['stock_status']['available'][$value['part_no']])){
                //     $data['stock_status']['available'][$value['part_no']] = $data['stock_status']['available'][$value['part_no']] + 1;
                // }else{
                //     $data['stock_status']['available'][$value['part_no']] = 1;
                // }
                
            }
            
        }

        // 5. 以自然月为开始，统计上架情况【单位：磅】【曲线图 或者 柱状图】
        // 初始化日期和数量
        $data['get_up'] = [];
        for($g = 1;$g <= date('d', time());$g++){
            $data['get_up'][$g] = 0;
        }
        $getUp = ShelfActionLogAll::find();
        $getUp->andFilterWhere(['action' => 2]);
        $getUp->andFilterWhere(['>=','created_time' , date('Y-m-01', time())]);
        $getUpData = $getUp->all();
        foreach($getUpData as $getUpValue){
            // 获取日期
            $date = date("d",strtotime($getUpValue->created_time));
            // if(array_key_exists($date,$data['get_up'])){
            //     $data['get_up'][(int)$date] += 11;
            // }else{
            //     $data['get_up'][(int)$date] = 11;
            // }
            $data['get_up'][(int)$date] += 11;

        }

        // 3. 以自然月为开始，统计报废情况【区分人工、过期】【单位：磅】【双曲线图】
        $data['broken'] = [];
        for($g = 1;$g <= date('d', time());$g++){
            $data['broken'][$g] = 0;
        }
        $getUp = ShelfActionLogAll::find();
        $getUp->andFilterWhere(['action' => 10]);
        $getUp->andFilterWhere(['>=','created_time' , date('Y-m-01', time())]);
        $getUpData = $getUp->all();
        foreach($getUpData as $getUpValue){
            // 获取日期
            $date = date("d",strtotime($getUpValue->created_time));
            if(array_key_exists($date,$data['broken'])){
                $data['broken'][(int)$date] += 11;
            }else{
                $data['broken'][(int)$date] = 11;
            }
            

        }

        // print_r($data);die;
        return $this->outputJson(true,'成功',$data);
    }
    
    /**
     * compound屋子内数据大屏
     */
    public function actionCheckCar(){

        // 返回当前哪个车需要挪走
        $querySS = ShelfMaterials::find();
        $querySS->select('sm.end_time,s.car_number');
        $querySS->alias('sm');
        $querySS->leftJoin(Shelf::tableName() . ' s', 'sm.shelf_id = s.id');
        $querySS->andFilterWhere(['sm.fettle' => 1]);
        $querySS->andFilterWhere(['>','sm.shelf_id' ,0]);
        $querySS->andFilterWhere(['in','sm.status' ,[2,3]]);
        $querySS->andFilterWhere(['in','s.region' ,['B']]);
        $querySS->asArray();
        $dataSS = $querySS->all();
        $car = [];
        // 标记所有的车,判断物料是否解冻完成
        foreach($dataSS as $value){
            $car[$value['car_number']] = 0;
        }
        foreach($dataSS as $value){
            if($value['end_time'] <= date('Y-m-d H:i:s',time())){
                $car[$value['car_number']] += 1;
            }
           
        }

        // 发邮件

        
        print_r($car);die;
    }

    /**
     * compound屋子内数据大屏
     */
    public function actionCompoundShowArea(){
        // 返回所有车的位置以及编号
        $shelfModel = Shelf::find();
        $shelfModel->andWhere(['floor' => 1]);
        $shelfModel->orderBy('column asc');
        $shelfModel->asArray();
        $shelfModelData = $shelfModel->all();
        // print_r($shelfModelData);die;
        $data = [];
        $data['B'] = [];
        $data['N'] = [];
        $data['Y'] = [];
        $data['G'] = [];
        $ib = 1;
        $in = 1;
        $iy = 0;

        // print_r($shelfModelData);die;
        foreach($shelfModelData as $value){
            if($value['region'] == 'B'){
                $carNum = 'CP '.$value['car_number'];
                if (!in_array($carNum , $data[$value['region']]))
                    $data[$value['region']][$ib++] = $carNum ;
            }
            if($value['region'] == 'N'){
                $carNum = 'CP '.$value['car_number'];
                if (!in_array($carNum , $data[$value['region']]))
                    $data[$value['region']][$in++] = $carNum ;
            }
            if($value['region'] == 'G'){
                $carNum = 'CP '.$value['car_number'];
                if (!in_array($carNum , $data[$value['region']]))
                    $data[$value['region']][$value['column']] = $carNum ;
            }
            if($value['region'] == 'Y'){
                $carNum = 'CP '.$value['car_number'];
                if (!in_array($carNum , $data[$value['region']]))
                    $data[$value['region']][1] = $carNum ;
            }
            
        }
        $data['B'] = (Object)$data['B'];
        $data['N'] = (Object)$data['N'];
        $data['G'] = (Object)$data['G'];
        $data['Y'] = (Object)$data['Y'];
        
        return $this->outputJson(true,'成功',$data);
        
    }


    /**
     *  新服务调用
     */
    public function actionLotNoSocket(){
        $params = $this->validateRequest();
        $facility_id = $params['facility_id'];
        $facility_info = Facility::findOne(['id'=>$facility_id]);

        $data = [];

        if($facility_info == null){
            return $this->outputJsonApp('false','无此设备',[
                'data'     => $data
            ]);
        }

        
        $body = "{\"appName\":\"MFGBE1\",\"data\":{\"MESLotInfoRequest\":{\"appName\":\"MFGBE1\",\"attributes\":[\"packageOutlineName\"],\"eqpName\":\"".$facility_id."\",\"wipFlagSelection\":\"NA\"}},\"eqpName\":\"".$facility_id."\",\"subject\":\"ATTJ.REQ.MES.LOT_INFO.[EqpName]\"}";

        
        $headers = ['Content-Type' => 'application/json'];
         $socket = stream_socket_client('tcp://10.192.130.252:27520', $errno, $errstr, 30);
         if(!$socket)
         {
            return $this->outputJsonApp('false','获取批号失败',[
                'data'     => $data
            ]);
         }
         else
         {
            fwrite($socket, $body);

            $response = fread($socket, 1024);
            $data = [];
            $i = 0;
            $temp = json_decode($response);
            print_r($temp);
            if($temp->MESLotInfoResponse->errorCode == 1){

                return $this->outputJson('true','请求成功！',[
                    'data'     => $temp
                ]);
            }
            // print_r($temp);die;
            $temp1 = $temp->MESLotInfoResponse->lotData;
            
            foreach($temp1 as $value){
            $data[$i] = $value->lotId;
            $i++;
            }
            return $this->outputJson('true','请求成功！',[
                'data'     => $data
            ]);
            
            fclose($socket);
         }
    }


    /**
     * 每日物料盘点
     */
    public function actionMaterialTotal(){
         // 统计所有
         $total_all = ShelfMaterials::find();
         $total_all->select('sm.part_no,s.status');
         $total_all->alias('sm');
         $total_all->leftJoin(Shelf::tableName() . ' s', 'sm.shelf_id = s.id');
         $total_all->where(['sm.fettle'=>1]);
         $total_all->andFilterWhere(['in','sm.status',[1,2,3,4]]);
         $total_all->asArray();
 
         $dataProvider = $total_all->all();
 
         $data = [];
 
         if ($dataProvider){
             foreach ($dataProvider as $key=>$value){
                 if(isset($data[$value['part_no']][$value['status']])){
                     $data[$value['part_no']][$value['status']] += 1;
                 }else{
                     $data[$value['part_no']] = [1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0];
                     $data[$value['part_no']][$value['status']] = 1;
                 }
             }
         }
         foreach($data as $key=>$v){
            // 保存单位为 磅
                $model = new ShelfMaterialTotal();
                $model->part_no = $key;
                $model->PROGRESS = $v[2]*5;
                $model->RECEIVE = $v[1]*5;
                $model->RECEIVED = $v[7]*5;
                $model->WARNING = $v[5]*5;
                $model->EXPIRE = $v[6]*5;
                $model->total = ($v[2] + $v[1] + $v[7] + $v[5] + $v[6])*5;
                $model->create_time = date('Y-m-d H:i:s');
                $model->save();
         }
         return $this->outputJson(true,'成功');
    }


    /**
     * 检查当前系统是否存在未完成的二次确认
     */
    public function actionCheckDoubleConfirm(){

        $queryStock = ShelfMaterials::find();
        $queryStock->andFilterWhere(['fettle' => 1]);
        $queryStock->andFilterWhere(['in','status' ,[3]]);
        $dataStock = $queryStock->all();

        if(empty($dataStock)){

            return $this->outputJson(true,'物料正常');
        }else{
            // print_r($dataStock);die;
            return $this->outputJson(false,'当前系统存在未完成的二次确认');
        }


    }
    /**
     * 判断当前用户是否有未完成的二次确认
     */
    public function actionCheckDoubleConfirmTwo(){

        $params = $this->validateRequest();
        $facility_id = $params['wbi_id'];

        $queryStock = ShelfMaterials::find();
        $queryStock->andFilterWhere(['fettle' => 1]);
        $queryStock->andFilterWhere(['in','status' ,[3]]);
        $dataStock = $queryStock->all();

        // 查看这个未完成的是不是当前用户
        foreach($dataStock as $value){
            if(!empty($value->apply_id)){
                $queryApply = ShelfApply::findOne($value->apply_id);
                if(!empty($queryApply)){
                    $queryUser = UserInfo::findOne($queryApply->user_id);
                    if(!empty($queryUser)){
                        if($queryUser->badge_code == $params['wbi_id']){
                            return $this->outputJson(false,'您有未完成二次确认的物料，请联系代班');
                        }
                    }
                }
            }
        }
        return $this->outputJson(true,'正常');

        if(empty($dataStock)){

            return $this->outputJson(true,'物料正常');
        }else{
            // print_r($dataStock);die;
            return $this->outputJson(false,'当前系统存在未完成的二次确认');
        }

    }
    
    /**
     * 判断当前用户是否有未完成的二次确认
     */
    public function actionCarChange(){
        // 筛选N和B区域的车
        $queryCar = Shelf::find();
        $queryCar->select('car_number');
        $queryCar->andWhere(['floor'=>1]);
        $queryCar->andWhere(['in','region',['B','N']]);
        $queryCar->andWhere(['!=','status',3]);
        $queryCar->groupby('car_number');
        $queryCar->asArray();
        $queryCarData = $queryCar->all();


        $outputData = [];

        foreach($queryCarData as $valueCar){
            
            $queryCarNum = Shelf::find();
            $queryCarNum->select('id');
            $queryCarNum->andWhere(['car_number'=>$valueCar['car_number']]);
            $queryCarNum->asArray();
            $queryCarNumData = $queryCarNum->all();
            // 输出为数组
            $dataCarList = [];
            foreach($queryCarNumData as $valueCarNum){
                $dataCarList[] = $valueCarNum['id'];
            }

            // 获取物料情况，判断是否有物料的 end_time 大于当前时间的
            $queryShelfMer = ShelfMaterials::find();
            $queryShelfMer->andFilterWhere(['fettle' => 1]);
            $queryShelfMer->andFilterWhere(['status' => 2]);
            $queryShelfMer->andFilterWhere(['in','shelf_id' ,$dataCarList]);
            $queryShelfMer->andFilterWhere(['>','end_time' ,date('Y-m-d H:i:s')]);
            $queryShelfMer->asArray();
            $queryShelfMerData = $queryShelfMer->all();

            if(!$queryShelfMerData){
                $outputData[] = 'CP '.$valueCar['car_number'];
            }

        }

        return $this->outputJson(true,'成功',$outputData);

    }


}