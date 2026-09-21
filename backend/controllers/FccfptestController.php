<?php
namespace backend\controllers;

use Yii;
use yii\data\Pagination;
use backend\models\FccfpExpireMethod;
use backend\models\FccfpList;
use backend\models\search\FccfpListSearch;
use backend\models\search\FccfpExpireMethodSearch;
use backend\models\FccfpControl;
use yii\web\Controller;
use yii\helpers\Json;
use backend\models\UserInfo;
use backend\models\FccfpAdhocwipData;

class FccfptestController extends Controller
{

    //数据展示页面  http://onebe.ap.freescale.net:9001/fccfptest/index
    public function actionIndex()
    {
        $resultData = $this->adhocwipdata('TJPE42CAX020','Y','NXG04351');
    }


    

    
	
    /**
     * 通过Socket ADHOC_WIPDATA
     * 其中lotId是批号, 
     * operator是工号, 对应onebe中人员信息的WBI Code, 
     * dataValue在1->2时传递Y, 2->1时传递N
     */
    public function adhocwipdata($lotId = '',$dataValue='',$wbicode='')
    {
       // $status = ['status' => false, 'message' => ''];return $status;
       // if ($lotId == '') return $status;

        $param = [
				"SetAdHocWIPDataRequest" => [
				"appName"    => "MFGBE1",
				"lotId"      => 'TJPE42CAX020',
				"objectType" => "Lot",
				"operator"   => 'NXG09413',
				"type"       => "Add",
				"wipDataDetails" => [
					[
						"dataName"  => "FE_BakeAction",
						"dataValue" => 'N'
					]
				],
				"wipDataSetupName" => "FE_BakeAction",
			]
        ];
    
	    $documentNoData = $this->http_post_data('http://fsbrestful-attj-at-f5.wbi.nxp.com:38556/REQ.MES.SET_ADHOC_WIPDATA/', $param);
		  // $documentNoData = $this->http_post_data('http://10.192.130.156:37520/REQ.MES.SET_ADHOC_WIPDATA/', $param);
		var_dump($documentNoData);die;
        if(!$documentNoData){
            $status['message'] = '接口返回数据错误！';
            return $status;
        }
        $status['status']  = true;
        $status['message'] = 'Success';
        return $status;
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
	
	// http://onebe.ap.freescale.net:9001/fccfptest/sockettest
	public function actionSockettest()
    {
        $resultData = $this->getMaterialpart('TJPD34ECZ000');
		var_dump($resultData);die;
    }
	
	private $socket;

     /**
     * 通过Socket 接口获取12NC
     */
    public function getMaterialpart($partNo = '')
    {
        $status = ['status' => false, 'message' => '', 'data' => []];

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
                        "PARAM" => 'TJPD34ECZ000'
                    ]
                ]
            ]
        ];

        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $result       = socket_connect($this->socket, '10.192.130.252', '27520');
        if ($result >= 0) {
            $msg =  Json::encode($param);
            if (!socket_write($this->socket, $msg, strlen($msg))) {
                $this->close();
                $status['message'] = '数据获取失败！';
                return $status;
            } else {
                while ($out = socket_read($this->socket, 8192)) {
                    $result = Json::decode($out);
                    if ($result['MESOnlineQueryResponse']['mesErrorCode'] == '') {$this->close();var_dump($result);die;
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


}
