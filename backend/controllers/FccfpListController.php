<?php
namespace backend\controllers;

use Yii;
use yii\data\Pagination;
use backend\models\FccfpExpireMethod;
use backend\models\FccfpList;
use backend\models\search\FccfpListSearch;
use backend\models\search\FccfpExpireMethodSearch;
use backend\models\FccfpControl;

use yii\helpers\Json;
use backend\models\UserInfo;
use backend\models\FccfpAdhocwipData;

class FccfpListController extends BaseController
{

    //数据展示页面
    public function actionIndex()
    {
        $colorArr = [
            0=>'white',
            1=>'red',
            2=>'yellow',
            3=>'orange',
            4=>'gray',
            5=>'green'
        ];
        $dataProvider =  FccfpList::find()->where(['!=', 'status', FccfpList::STATUS_PASS])->orderBy(['status' => SORT_DESC])->asArray()->all();
        return $this->render('index', [
            'colorArr' => $colorArr,
            'dataProvider' => $dataProvider
        ]);
    }


    /**
     * 
     * @return string
     */
    public function actionList()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new FccfpListSearch();
        $data         = $searchModel->search($request);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        return $this->render('list', [
            'request'      => $request,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination
        ]);
    }

    
    /**
     * 详情
     * @return string
     */
    public function actionListDetail()
    {
        $id      = Yii::$app->request->get('id');
        $model   = FccfpList::findOne(['id' => $id]);
        return $this->render('list-detail', [
            'model' => $model
        ]);
    }

    /**
     * 编辑
    */
    public function actionListUpdate(){

        if (Yii::$app->request->isPost) {
            $post  = Yii::$app->request->post();
            $model = FccfpList::findOne($post['id']);
            $model->status = $post['status'];
            if (!$model->save(false)) {
                return $this->json(100, '修改失败!');
            }
            return $this->json(200, '修改成功!');
        }
        $id    = Yii::$app->request->get('id');
        $model = FccfpList::findOne($id);
        return $this->render('list-update', [
            'model' => $model
        ]);
    }

    /**
     * 已完成
     * @return string
     */
    public function actionHistory()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new FccfpListSearch();
        $data         = $searchModel->history($request);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        return $this->render('history', [
            'request'      => $request,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination
        ]);
    }


    public function actionExpire()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new FccfpExpireMethodSearch();
        $data         = $searchModel->search($request);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        return $this->render('expire', [
            'request'      => $request,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination
        ]);
    }

    
    /**
     * 创建
     * @return string
     */
    public function actionExpireCreate()
    {
        if (Yii::$app->request->isPost) {
            $post                = Yii::$app->request->post();
            $model               = FccfpExpireMethod::findOne(['nowspecname' => trim($post['nowspecname'])]);
            if(!$model){
                $model               = new FccfpExpireMethod();
                $model->nowspecname  = trim($post['nowspecname']);
                $model->status       = $post['status'];
                $model->method       = $post['method'];
                if (!$model->save()) {
                    return $this->json(100, '添加失败!');
                }
                return $this->json(200, '添加成功!');
            }else{
                return $this->json(100, '已存在此工序!');
            }

        }
        return $this->render('expire-create');
    }

    /**
     * 更新
     * @return string
     */
    public function actionExpireUpdate()
    {
        if (Yii::$app->request->isPost) {
           
            $post                = Yii::$app->request->post();
            $model               = FccfpExpireMethod::findOne(['id' => $post['id']]);
            $model->status       = $post['status'];
            $model->method       = $post['method'];
            if (!$model->save()) {
                return $this->json(100, '更新失败!');
            }
            return $this->json(200, '更新成功!');
        }
        $id     = Yii::$app->request->get('id');
        $model  = FccfpExpireMethod::findOne(['id' => $id]);
        return $this->render('expire-update', [
            'model' =>$model
        ]);
    }

    /**
     * 第一个框和第二个框的物料就是处在FC_INSP已经track out 但BAKE还没有track in;
     * 第三个框就是SUB_PBAKE已经track in, 但还没有track out
     * 第四个框就是SUB_PBAKE已经track out, 但PREM_PLS还没有track in
     * @return string
     */
    public function actionControl()
    {
        $fcinspover      = FccfpControl::find()->select('containername')->where(['not', ['fcinsp_trackout' => null]])->andWhere(['subpbake_trackin' => null])->andWhere(['drag_drop_bake'=>FccfpControl::DRAG_DROP_BAKE_WATE])->andWhere(['in' , 'nowspecname' , ['SUB_PBAKE','FC_INSP']])->column();
        $waitBakeTrackInArr = FccfpControl::find()->select(['containername','input_drop_bake_time'])->where(['not', ['fcinsp_trackout' => null]])->andWhere(['subpbake_trackin' => null])->andWhere(['drag_drop_bake'=>FccfpControl::DRAG_DROP_BAKE_SUCCESS])->andWhere(['in' , 'nowspecname' , ['SUB_PBAKE','FC_INSP']])->asArray()->all();
        $bakeTrackIn     = FccfpControl::find()->select(['containername'])->where(['not', ['subpbake_trackin' => null]])->andWhere(['subpbake_trackout' => null])->andWhere(['nowspecname'=>'SUB_PBAKE'])->column();
        $bakeTrackOut    = FccfpControl::find()->select(['containername'])->where(['fmold_trackout' => null])->andWhere(['not', ['subpbake_trackout' => null]])->andWhere(['in' , 'nowspecname' , ['SUB_PBAKE','PREM_PLS','FMOLD','PSSWND']])->column();
        $waitBakeTrackIn = [];
        if(!empty($waitBakeTrackInArr)){
            foreach($waitBakeTrackInArr as $value){
                $waitBakeTrackIn[] = $value['containername'] . '--预计Bake时间'. date('Y-m-d H:i:s',strtotime($value['input_drop_bake_time']));
            }
        }
        
        return $this->render('control', [
            'fcinspover'      => $fcinspover,
            'waitBakeTrackIn' => $waitBakeTrackIn,
            'bakeTrackIn'     => $bakeTrackIn,
            'bakeTrackOut'    => $bakeTrackOut
        ]);
    }
    /**
     * 撤回Bake 物料
     */
    public function actionMovedropbake()
    {
		$wbiCode = '';
		$userInfo = UserInfo::findOne($this->getUid());
        if($userInfo){
            $wbiCode = $userInfo->wbi_code;
        }
        $post = Yii::$app->request->post();
        foreach($post['date'] as $value){
            $containernameArr = explode("--",$value['name']);
            if(isset($containernameArr[0])){
				
                $containername = $containernameArr[0];
                $model = FccfpControl::findOne(['containername' => $containername,'drag_drop_bake'=>FccfpControl::DRAG_DROP_BAKE_SUCCESS]);
                if($model){
                    $model->input_drop_bake_time = '';
                    $model->drag_drop_bake       = FccfpControl::DRAG_DROP_BAKE_WATE;
                    $model->drag_drop_bake_time  = '';
                    $model->save(false);
                }
				
				$resultData = $this->adhocwipdata($containername,'N',$wbiCode);
				$adhocModel               = new FccfpAdhocwipData();
				$adhocModel->nowspecname  = trim($containername);
				$adhocModel->status       = $resultData['status'];
				$adhocModel->message      = $resultData['message'];
				$adhocModel->wbi_code     = $wbiCode;
				$adhocModel->datavalue    = 'N';
				$adhocModel->create_time  = date('Y-m-d H:i:s',time());
				$adhocModel->save(false);
				
            }
        }

        return $this->json(200, '撤回BAKE物料成功!');
    }

    /**
     * 拖拽后发信
     */
    public function actionDropbake()
    {
		$wbiCode = '';
		$userInfo = UserInfo::findOne($this->getUid());
        if($userInfo){
            $wbiCode = $userInfo->wbi_code;
        }
        $post = Yii::$app->request->post();
        $containernameArr = [];
        foreach($post['date'] as $value){
			if (strpos($value['name'], '--') !== false) {
                $dataArr = explode("--",$value['name']);
                if(isset($dataArr[0])){
                    $value['name'] = $dataArr[0];
                }
            }
			
            $model = FccfpControl::findOne(['containername' => $value['name'],'drag_drop_bake'=>FccfpControl::DRAG_DROP_BAKE_WATE]);
            if($model){
                $model->input_drop_bake_time = $post['input_drop_bake_time'];
                $model->drag_drop_bake       = FccfpControl::DRAG_DROP_BAKE_SUCCESS;
                $model->drag_drop_bake_time  = date('Y-m-d H:i:s', time());
                $model->save(false);
                $containernameArr[] = $value['name'];
            }
			
			$resultData = $this->adhocwipdata($value['name'],'Y',$wbiCode);
            $adhocModel               = new FccfpAdhocwipData();
            $adhocModel->nowspecname  = trim($value['name']);
            $adhocModel->status       = $resultData['status'];
            $adhocModel->message      = $resultData['message'];
            $adhocModel->wbi_code     = $wbiCode;
            $adhocModel->datavalue    = 'Y';
            $adhocModel->create_time  = date('Y-m-d H:i:s',time());
            $adhocModel->save(false);
			
        }

        if(!empty($containernameArr)){
            $data['number']   = count($containernameArr);
            $data['str']      = implode(';', $containernameArr);
            $data['bake_time']= $post['input_drop_bake_time'];
           
            $subject = 'FCCSP物料BAKE交接系统-待BAKE物料';
            $html =  $this->renderFile('@common/mail/fccfp/fcinspend-drop-bake-email.php',['data' => $data]);
            $mail = Yii::$app->mailer->compose();
            $mail->setHtmlBody($html);  //邮件正文
            $mail->setSubject($subject); //邮件主题
            $mail->setTo($this->frontLeadArr);//收件人 前线带班账号
            @$mail->send();
        }
      
        return $this->json(200, '拖拽到BAKE成功!');
    }
	
    /**
     * 通过Socket ADHOC_WIPDATA
     * 其中lotId是批号, 
     * operator是工号, 对应onebe中人员信息的WBI Code, 
     * dataValue在1->2时传递Y, 2->1时传递N
     */
    public function adhocwipdata($lotId = '',$dataValue='',$wbicode='')
    {
        $status = ['status' => false, 'message' => ''];
        if ($lotId == '') return $status;

        $param = [
            "SetAdHocWIPDataRequest" => [
				"appName"    => "MFGBE1",
				"lotId"      => $lotId,
				"objectType" => "Lot",
				"operator"   => "NXA23015",
				"type"       => "Add",
				"wipDataDetails" => [
					[
						"dataName"  => "FE_BakeAction",
						"dataValue" => $dataValue
					]
				],
				"wipDataSetupName" => "FE_BakeAction",
			]
        ];
		$status['message'] = $this->http_post_data('http://fsbrestful-attj-at-f5.wbi.nxp.com:38556/REQ.MES.SET_ADHOC_WIPDATA/', $param);
        $status['status']  = true;
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
        //if ($errorno) {
        //    return false;
        //}
        curl_close($curl);
        return $res;
    }


    
    //前线带班邮箱
    protected $frontLeadArr = ['shen.zhang@nxp.com','jianxin.li_1@nxp.com','chen.sun@nxp.com','haibin.guo@nxp.com'];
  
    //前线产品工程师邮箱
    protected $frontProductArr = ['xin.chen@nxp.com','naiyu.yun@nxp.com'];

    //后线带班邮箱
    protected $backlineLeadArr = ['zhibin.sun@nxp.com','quansheng.ge@nxp.com','qi.li_2@nxp.com','lei.ma_2@nxp.com'];

    //后线产品工程师邮箱
    protected $backlineProductArr = ['gang.zhao@nxp.com','qiang.fan@nxp.com','jianguo.zhao@nxp.com'];
    
    /**
     * 1. 物料FC_INSP结束发送邮件
     * 触发条件: FC_INSP track out结束(结束一个全部发一次)
     * 后线带班账号
     */
    public function fcInspEndSendEmail(){

        $fccfpControlArr = FccfpControl::find()->where(['wait_bake' => FccfpControl::WAIT_BAKE_WATE,'drag_drop_bake' => FccfpControl::DRAG_DROP_BAKE_WATE])->asArray()->all();
        if(empty($fccfpControlArr)){
            return 1;
        }
       
        $timestamp1 = time();//当前时间
        foreach($fccfpControlArr as $key => $value){
            $timestamp2 = strtotime($value['fcinsp_trackout']);
            $diff       = $timestamp1 - $timestamp2;
            $hours      = floor($diff / 3600);
            $minutes    = floor(($diff % 3600) / 60);
            $fccfpControlArr[$key]['usetime'] = $hours.'h'.$minutes.'m';//已用时间

            $model = FccfpControl::findOne(['id' => $value['id']]);
            $model->wait_bake      = FccfpControl::WAIT_BAKE_SUCCESS;
            $model->wait_bake_email_time = date('Y-m-d H:i:s', time());
            $model->save(false);
        }

        $subject = 'FCCSP物料BAKE交接系统-FC_INSP后的物料等待BAKE';
        $html =  $this->renderFile('@common/mail/fccfp/fcinspend-email.php',['data' => $fccfpControlArr]);
        $mail = Yii::$app->mailer->compose();
        $mail->setHtmlBody($html);  //邮件正文
        $mail->setSubject($subject); //邮件主题
        $mail->setTo($this->backlineLeadArr);//收件人 后线带班账号
        @$mail->send();
        return 1;
    }

    /**
     * 2. 超时还未从FC_INSP拖拽进入SUB_PBAKE发信
     * FC_INSP track out结束 24h 还未被拖入待BAKE区域
     * 后线带班账号
     * 产品工程师
     */
    public function dragDropBakeSendEmail(){

        $fccfpControlArr = FccfpControl::find()->where(['wait_bake' => FccfpControl::WAIT_BAKE_SUCCESS,'drag_drop_bake' => FccfpControl::DRAG_DROP_BAKE_WATE])->asArray()->all();
        if(empty($fccfpControlArr)){
            return 1;
        }
       
        $data = [];
        $timestamp1 = time();//当前时间
        foreach($fccfpControlArr as $value){
            $timestamp2 = strtotime($value['fcinsp_trackout']);
            $diff       = $timestamp1 - $timestamp2;
            $hours      = floor($diff / 3600);
            if($hours >= 24){
                continue;
            }
            $minutes    = floor(($diff % 3600) / 60);
            $usetime = $hours.'h'.$minutes.'m';//已用时间
            $data[] = ['usetime'=> $usetime,'containername'=> $value['containername'],'fcinsp_trackout'=> $value['fcinsp_trackout']];
        }
        if(empty($data)){
            return 1;
        }

        $subject = 'FCCSP物料BAKE交接系统-FC_INSP后的物料未处理-超时24h';
        $html =  $this->renderFile('@common/mail/fccfp/drag-drop-bake-email.php',['data' => $data]);
        $mail = Yii::$app->mailer->compose();
        $mail->setHtmlBody($html);  //邮件正文
        $mail->setSubject($subject); //邮件主题
        $mail->setTo($this->backlineLeadArr);//收件人 后线带班账号
        $mail->setCc($this->backlineProductArr); //抄送的人 后线产品工程师
        @$mail->send();

        return 1;
    }


    /**
     * 3. FC_INSP拖拽进入SUB_PBAKE发信
     * 有FC_INSP结束的物料被拖入SUB_PBAKE区域, 每天凌晨四点之前(3:30)整体发一封信
     * 前线带班账号
     */
    public function fcInspDropBakeSendEmail(){

        $fccfpControlArr = FccfpControl::find()->where(['drag_drop_bake' => FccfpControl::DRAG_DROP_BAKE_WATE])->asArray()->all();
        if(empty($fccfpControlArr)){
            return 1;
        }
        $data = [];
        $containernameArr = array_column($fccfpControlArr,'containername');
        $data['number']   = count($containernameArr);
        $data['str']      = implode(';', $containernameArr);

        foreach($fccfpControlArr as $value){

            $model = FccfpControl::findOne(['id' => $value['id']]);
            $model->wait_bake            = FccfpControl::WAIT_BAKE_SUCCESS;
            $model->wait_bake_email_time = date('Y-m-d H:i:s', time());
            $model->drag_drop_bake       = FccfpControl::DRAG_DROP_BAKE_SUCCESS;
            $model->drag_drop_bake_time  = date('Y-m-d H:i:s', time());
            $model->save(false);
        }

        $subject = 'FCCSP物料BAKE交接系统-待BAKE物料';
        $html =  $this->renderFile('@common/mail/fccfp/fcinspend-drop-bake-email.php',['data' => $data]);
        $mail = Yii::$app->mailer->compose();
        $mail->setHtmlBody($html);  //邮件正文
        $mail->setSubject($subject); //邮件主题
        $mail->setTo($this->frontLeadArr);//收件人 前线带班账号
        @$mail->send();
        return 1;
    }

    /**
     * 4. SUB_PBAKE结束发信
     *  SUB_PBAKE track out结束
     * 后线带班账号
     * 
     */
    public function subBakeCloseSendEmail(){

        $fccfpControlArr = FccfpControl::find()->where(['subpbake_status' => FccfpControl::SUBPBAKE_STATUS_WATE])->asArray()->all();
        if(empty($fccfpControlArr)){
            return 1;
        }
       
        $data = [];
        $timestamp1 = time();//当前时间
        foreach($fccfpControlArr as $value){
            $timestamp2 = strtotime($value['subpbake_trackout']);
            $diff       = $timestamp1 - $timestamp2;
            $hours      = floor($diff / 3600);
            if($hours >= 24){
                continue;
            }
            $minutes    = floor(($diff % 3600) / 60);
            $usetime = $hours.'h'.$minutes.'m';//已用时间
            $data[] = ['usetime'=> $usetime,'containername'=> $value['containername'],'subpbake_trackout'=> $value['subpbake_trackout']];

            $model = FccfpControl::findOne(['id' => $value['id']]);
            $model->subpbake_status     = FccfpControl::SUBPBAKE_STATUS_SUCCESS;
            $model->subpbake_email_time = date('Y-m-d H:i:s', time());
            $model->save(false);
        }
        if(empty($data)){
            return 1;
        }

        $subject = 'FCCSP物料BAKE交接系统-SUB_PBAKE后的物料未处理';
        $html =  $this->renderFile('@common/mail/fccfp/sub-bake-close-email.php',['data' => $data]);
        $mail = Yii::$app->mailer->compose();
        $mail->setHtmlBody($html);  //邮件正文
        $mail->setSubject($subject); //邮件主题
        $mail->setTo($this->backlineLeadArr);//收件人 后线带班账号
        @$mail->send();

        return 1;
    }
    
    /**
     * 5. SUB_PBAKE结束, 超时还未PLASMA发信
     *  SUB_PBAKE track out结束12h, 还未track in PREM_PLS
     * 后线带班账号
     * 产品工程师
     */
    public function subBakeTimeoutSendEmail(){
        
        $fccfpControlArr = FccfpControl::find()->where(['prempls_trackin' => '','prempls_email_time'=>''])->andWhere(['>','subpbake_trackout',0])->asArray()->all();
        if(empty($fccfpControlArr)){
            return 1;
        }
       
        $data = [];
        $timestamp1 = time();//当前时间
        foreach($fccfpControlArr as $value){
            $timestamp2 = strtotime($value['subpbake_trackout']);
            $diff       = $timestamp1 - $timestamp2;
            $hours      = floor($diff / 3600);
            if($hours >= 12){
                continue;
            }
            $minutes    = floor(($diff % 3600) / 60);
            $usetime    = $hours.'h'.$minutes.'m';//已用时间
            $data[]     = ['usetime'=> $usetime,'containername'=> $value['containername'],'subpbake_trackout'=> $value['subpbake_trackout']];

            $model = FccfpControl::findOne(['id' => $value['id']]);
            $model->prempls_email_time = date('Y-m-d H:i:s', time());
            $model->save(false);
        }
        if(empty($data)){
            return 1;
        }

        $subject = 'FCCSP物料BAKE交接系统-SUB_PBAKE后的物料未处理-超时12h';
        $html =  $this->renderFile('@common/mail/fccfp/sub-bake-timeout-email.php',['data' => $data]);
        $mail = Yii::$app->mailer->compose();
        $mail->setHtmlBody($html);  //邮件正文
        $mail->setSubject($subject); //邮件主题
        $mail->setTo($this->backlineLeadArr);//收件人 后线带班账号
        $mail->setCc($this->backlineProductArr); //抄送的人 后线产品工程师
        @$mail->send();

        return 1;
    }


}
