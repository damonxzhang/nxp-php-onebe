<?php
namespace frontend\controllers;

use backend\models\ShelfMaterials;
use backend\models\Facility;

class CompoundController extends FrontendBaseController{


    /**
     *  查询是否有未二次确认
     */
    public function actionTwoConfirm(){
        $data = ShelfMaterials::getNoTwoConfirm();
        if($data){
            return $this->outputJson(false,'有未确认数据',$data);
        }else{
            return $this->outputJson(true,'没有未确认数据');
        }
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

        
        $body = "{\"appName\":\"MFGBE1\",\"data\":{\"MESLotInfoRequest\":{\"appName\":\"MFGBE1\",\"attributes\":[\"packageOutlineName\"],\"eqpName\":\"".$facility_info->number."\",\"wipFlagSelection\":\"NA\"}},\"eqpName\":\"".$facility_info->number."\",\"subject\":\"ATTJ.REQ.MES.LOT_INFO.[EqpName]\"}";
        // print_r($body);die;
        $headers = ['Content-Type' => 'application/json'];
         $socket = stream_socket_client('tcp://fsbgengateway-attj-f5.wbi.nxp.com:28555', $errno, $errstr, 30);
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
            // print_r($temp);die;

            if(isset($temp->MESLotInfoResponse->lotData)){

                    // return $this->outputJson('true','请求成功！',[
                    //     'data'     => $data
                    // ]);
                
                // print_r($temp);die;
                $temp1 = $temp->MESLotInfoResponse->lotData;
                
                foreach($temp1 as $value){
                    $data[$i] = $value->lotId;
                    $i++;
                }
                return $this->outputJson('true','请求成功！',[
                    'data'     => $data
                ]);
            }

            return $this->outputJson('false','没有对应了LOT');
            
            fclose($socket);
         }
    }




}