<?php

namespace backend\controllers;

use Yii;
use yii\helpers\Json;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use backend\models\Fccfp;
use backend\models\FccfpLog;
use backend\models\FccfpList;
use backend\models\FccfpBe;
use backend\models\FccfpLogBe;
use backend\models\FccfpControl;


class FccspController extends Controller
{
    protected $fccfpiInsertArr = [
        'containername' =>'',
        'nowspecname'   =>'',
        'fcinsp'=>0,
        'fcinsp_movein'  =>'',
        'fcinsp_trackin' =>'',
        'fcinsp_trackout'=>'',
        'fcinsp_moveout' =>'',
        'subpbake'=>0,
        'subpbake_movein'  =>'',
        'subpbake_trackin' =>'',
        'subpbake_trackout'=>'',
        'subpbake_moveout' =>'',
        'prempls'=>0,
        'prempls_movein' =>'',
        'prempls_trackin' =>'',
        'prempls_trackout' =>'',
        'prempls_moveout' =>'',
        'psswnd'=>0,
        'psswnd_movein' =>'',
        'psswnd_trackin' =>'',
        'psswnd_trackout' =>'',
        'psswnd_moveout' =>'',
        'fmold'=>0,
        'fmold_movein' =>'',
        'fmold_trackin' =>'',
        'fmold_trackout' =>'',
        'fmold_moveout' =>'',
        'nldpmc'=>0,
        'nldpmc_movein' =>'',
        'nldpmc_trackin' =>'',
        'nldpmc_trackout' =>'',
        'nldpmc_moveout' =>'',
        'fcmark'=>0,
        'fcmark_movein' =>'',
        'fcmark_trackin' =>'',
        'fcmark_trackout' =>'',
        'fcmark_moveout' =>'',
        'fpfreflow'=>0,
        'fpfreflow_movein' =>'',
        'fpfreflow_trackin' =>'',
        'fpfreflow_trackout' =>'',
        'fpfreflow_moveout' =>'',
        'fpfclean'=>0,
        'fpfclean_movein' =>'',
        'fpfclean_trackin' =>'',
        'fpfclean_trackout' =>'',
        'fpfclean_moveout' =>'',
        'br'=>0,
        'br_movein' =>'',
        'br_trackin' =>'',
        'br_trackout' =>'',
        'br_moveout' =>'',
        'fluxclean'=>0,
        'fluxclean_movein' =>'',
        'fluxclean_trackin' =>'',
        'fluxclean_trackout' =>'',
        'fluxclean_moveout' =>'',
        'besaw'=>0,
        'besaw_movein' =>'',
        'besaw_trackin' =>'',
        'besaw_trackout' =>'',
        'besaw_moveout' =>'',
    ];

    protected $fccfpKeyArr = [
        'containername',
        'nowspecname',
        'fcinsp',
        'fcinsp_movein',
        'fcinsp_trackin',
        'fcinsp_trackout',
        'fcinsp_moveout',
        'subpbake',
        'subpbake_movein',
        'subpbake_trackin',
        'subpbake_trackout',
        'subpbake_moveout',
        'prempls',
        'prempls_movein',
        'prempls_trackin',
        'prempls_trackout',
        'prempls_moveout',
        'psswnd',
        'psswnd_movein',
        'psswnd_trackin',
        'psswnd_trackout',
        'psswnd_moveout',
        'fmold',
        'fmold_movein',
        'fmold_trackin',
        'fmold_trackout',
        'fmold_moveout',
        'nldpmc',
        'nldpmc_movein',
        'nldpmc_trackin',
        'nldpmc_trackout',
        'nldpmc_moveout',
        'fcmark',
        'fcmark_movein',
        'fcmark_trackin',
        'fcmark_trackout',
        'fcmark_moveout',
        'fpfreflow',
        'fpfreflow_movein',
        'fpfreflow_trackin',
        'fpfreflow_trackout',
        'fpfreflow_moveout',
        'fpfclean',
        'fpfclean_movein',
        'fpfclean_trackin',
        'fpfclean_trackout',
        'fpfclean_moveout',
        'br',
        'br_movein',
        'br_trackin',
        'br_trackout',
        'br_moveout',
        'fluxclean',
        'fluxclean_movein',
        'fluxclean_trackin',
        'fluxclean_trackout',
        'fluxclean_moveout',
        'besaw',
        'besaw_movein',
        'besaw_trackin',
        'besaw_trackout',
        'besaw_moveout',
        'create_time',
    ];

    //页面
    /**
     * 1:red
     * 2:yellow
     * 3:orange
     * 4:gray
     * 5:green
     */
    public function actionIndex()
    {
        $colorArr = [
            0=>'#FFFFFF',
            1=>'#CC0000',
            2=>'#FFFF66',
            3=>'#FF9933',
            4=>'#AAAAAA',
            5=>'#33CC66'
        ];

        $dataProvider =  FccfpList::find()->where(['!=', 'status', FccfpList::STATUS_PASS])->orderBy(['status' => SORT_DESC])->asArray()->all();
        return $this->render('index', [
            'colorArr' => $colorArr,
            'dataProvider' => $dataProvider
        ]);
    }

        /**
     * 第一个框和第二个框的物料就是处在FC_INSP已经track out 但BAKE还没有track in;
     * 第三个框就是SUB_PBAKE已经track in, 但还没有track out
     * 第四个框就是SUB_PBAKE已经track out, 但PREM_PLS还没有track in
     * @return string
     */
    public function actionControlshow()
    {
        $fcinspover      = FccfpControl::find()->select('containername')->where(['not', ['fcinsp_trackout' => null]])->andWhere(['subpbake_trackin' => null])->andWhere(['drag_drop_bake'=>FccfpControl::DRAG_DROP_BAKE_WATE])->andWhere(['in' , 'nowspecname' , ['SUB_PBAKE','FC_INSP']])->column();
        $waitBakeTrackInArr = FccfpControl::find()->select(['containername','input_drop_bake_time'])->where(['not', ['fcinsp_trackout' => null]])->andWhere(['subpbake_trackin' => null])->andWhere(['drag_drop_bake'=>FccfpControl::DRAG_DROP_BAKE_SUCCESS])->andWhere(['in' , 'nowspecname' , ['SUB_PBAKE','FC_INSP']])->asArray()->all();
        $bakeTrackIn     = FccfpControl::find()->select('containername')->where(['not', ['subpbake_trackin' => null]])->andWhere(['subpbake_trackout' => null])->andWhere(['nowspecname'=>'SUB_PBAKE'])->column();
        $bakeTrackOut    = FccfpControl::find()->select('containername')->where(['fmold_trackout' => null])->andWhere(['not', ['subpbake_trackout' => null]])->andWhere(['in' , 'nowspecname' , ['SUB_PBAKE','PREM_PLS','FMOLD','PSSWND']])->column();
  
        $timeOutArr      = FccfpControl::find()->select('containername')->where(['not', ['prempls_email_time' => null]])->andWhere(['in' , 'nowspecname' , ['SUB_PBAKE','PREM_PLS']])->column();
        $waitBakeTrackIn = [];
        if(!empty($waitBakeTrackInArr)){
            foreach($waitBakeTrackInArr as $value){
                $waitBakeTrackIn[] = $value['containername'] . '--预计Bake时间'. date('Y-m-d H:i:s',strtotime($value['input_drop_bake_time']));
             }
        }

        return $this->render('controlshow', [
            'fcinspover'      => $fcinspover,
            'waitBakeTrackIn' => $waitBakeTrackIn,
            'bakeTrackIn'     => $bakeTrackIn,
            'bakeTrackOut'    => $bakeTrackOut,
            'timeOutArr'     => $timeOutArr
        ]);
    }

    /**
     * GET方式获取数据
     */
    public function curlGet($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }

    //绝对值
    function absolute_value($number) {
        return $number < 0 ? -$number : $number;
    }

     /**
     * fe-be数据
     */
    public function getFebeData()
    {
        $this->fcInspEndSendEmail();
        $this->dragDropBakeSendEmail();
        $this->subBakeCloseSendEmail();
        $this->subBakeTimeoutSendEmail();

        //FE-BE接口
        $febeurl = 'http://10.193.226.115:8080/portDataSystem/history/get/front';
                
        //GET请求
        $getfebeData = $this->curlGet($febeurl);
        if($getfebeData['code'] != 200){
            return 1;
        }
        if(empty($getfebeData['data'])){
            return 1;
        }

        $addfebeLog = ["param" => json_encode($getfebeData['data'])];
        (new FccfpLogBe())->create($addfebeLog);
        
        $febedata   = [];
        if(!empty($getfebeData['data'])){
            $createTime = date('Y-m-d H:i:s', time());
            foreach($getfebeData['data'] as $value){
                $insertfebeArr = $this->fccfpiInsertArr;
                $insertfebeArr['containername'] = strtoupper($value['containerName']);
                $insertfebeArr['nowspecname']   = $value['nowSpecName'];
                foreach($value['fccspSpecTime'] as $key => $val){
                    if(array_key_exists(strtolower($key),$this->fccfpiInsertArr)){
                        $insertfebeArr[strtolower($key)] = $val;
                    }
                }
                $insertfebeArr['create_time'] = $createTime;
                $febedata[] = $insertfebeArr;
            }
            //添加数据
            $insertfebeFlowNode = Yii::$app->db->createCommand()->batchInsert(FccfpBe::tableName(),$this->fccfpKeyArr, $febedata)->execute();
        }

        if(empty($febedata)){
            return 1;
        }

        foreach($febedata as $value){
            $fccfpControl = FccfpControl::findOne(['containername' => $value['containername']]);
            if($fccfpControl){
                $fccfpControl->nowspecname           = $value['nowspecname'];
                $fccfpControl->fcinsp                = $value['fcinsp'];
                $fccfpControl->fcinsp_movein         = $value['fcinsp_movein'];
                $fccfpControl->fcinsp_trackin        = $value['fcinsp_trackin'];
                $fccfpControl->fcinsp_trackout       = $value['fcinsp_trackout'];
                $fccfpControl->fcinsp_moveout        = $value['fcinsp_moveout'];
                $fccfpControl->subpbake              = $value['subpbake'];
                $fccfpControl->subpbake_movein       = $value['subpbake_movein'];
                $fccfpControl->subpbake_trackin      = $value['subpbake_trackin'];
                $fccfpControl->subpbake_trackout     = $value['subpbake_trackout'];
                $fccfpControl->subpbake_moveout      = $value['subpbake_moveout'];             
                $fccfpControl->prempls               = $value['prempls'];
                $fccfpControl->prempls_movein        = $value['prempls_movein'];
                $fccfpControl->prempls_trackin       = $value['prempls_trackin'];
                $fccfpControl->prempls_trackout      = $value['prempls_trackout'];
                $fccfpControl->prempls_moveout       = $value['prempls_moveout'];
                $fccfpControl->fmold                 = $value['fmold'];
                $fccfpControl->fmold_movein          = $value['fmold_movein'];
                $fccfpControl->fmold_trackin         = $value['fmold_trackin'];
                $fccfpControl->fmold_trackout        = $value['fmold_trackout'];
                $fccfpControl->fmold_moveout         = $value['fmold_moveout'];
                $fccfpControl->save(false);
            }else{
                $fccfpControlModel                        = new FccfpControl();
                $fccfpControlModel->containername         = $value['containername'];
                $fccfpControlModel->nowspecname           = $value['nowspecname'];
                $fccfpControlModel->fcinsp                = $value['fcinsp'];
                $fccfpControlModel->fcinsp_movein         = $value['fcinsp_movein'];
                $fccfpControlModel->fcinsp_trackin        = $value['fcinsp_trackin'];
                $fccfpControlModel->fcinsp_trackout       = $value['fcinsp_trackout'];
                $fccfpControlModel->fcinsp_moveout        = $value['fcinsp_moveout'];
                $fccfpControlModel->subpbake              = $value['subpbake'];
                $fccfpControlModel->subpbake_movein       = $value['subpbake_movein'];
                $fccfpControlModel->subpbake_trackin      = $value['subpbake_trackin'];
                $fccfpControlModel->subpbake_trackout     = $value['subpbake_trackout'];
                $fccfpControlModel->subpbake_moveout      = $value['subpbake_moveout'];             
                $fccfpControlModel->prempls               = $value['prempls'];
                $fccfpControlModel->prempls_movein        = $value['prempls_movein'];
                $fccfpControlModel->prempls_trackin       = $value['prempls_trackin'];
                $fccfpControlModel->prempls_trackout      = $value['prempls_trackout'];
                $fccfpControlModel->prempls_moveout       = $value['prempls_moveout'];
                $fccfpControlModel->fmold                 = $value['fmold'];
                $fccfpControlModel->fmold_movein          = $value['fmold_movein'];
                $fccfpControlModel->fmold_trackin         = $value['fmold_trackin'];
                $fccfpControlModel->fmold_trackout        = $value['fmold_trackout'];
                $fccfpControlModel->fmold_moveout         = $value['fmold_moveout'];
                $fccfpControlModel->wait_bake             = empty($value['subpbake_trackin']) ? 0 : 1;
                $fccfpControlModel->wait_bake_email_time  = '';
                $fccfpControlModel->drag_drop_bake        = empty($value['subpbake_trackin']) ? 0 : 2;
                $fccfpControlModel->drag_drop_bake_time   = '';
                $fccfpControlModel->subpbake_status       = empty($value['subpbake_trackout']) ? 0 : 3;
                $fccfpControlModel->subpbake_email_time   = '';
                $fccfpControlModel->prempls_email_time    = '';
                $fccfpControlModel->create_time           = date('Y-m-d H:i:s', time());
                $fccfpControlModel->save(false);
            }

        }


        return 1;

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

        $fccfpControlArr = FccfpControl::find()->where(['not', ['fcinsp_trackout' => null]])->andWhere(['subpbake_trackin' => null])->andWhere(['wait_bake' => FccfpControl::WAIT_BAKE_WATE])->andWhere(['in' , 'nowspecname' , ['SUB_PBAKE','FC_INSP']])->asArray()->all();
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

        // $subject = 'FCCSP物料BAKE交接系统-FC_INSP后的物料等待BAKE';
        // $html =  $this->renderFile('@common/mail/fccfp/fcinspend-email.php',['data' => $fccfpControlArr]);
        // $mail = Yii::$app->mailer->compose();
        // $mail->setHtmlBody($html);  //邮件正文
        // $mail->setSubject($subject); //邮件主题
        // $mail->setTo($this->backlineLeadArr);//收件人 后线带班账号
        // @$mail->send();
        return 1;
    }

    /**
     * 2. 超时还未从FC_INSP拖拽进入SUB_PBAKE发信
     * FC_INSP track out结束 24h 还未被拖入待BAKE区域
     * 后线带班账号
     * 产品工程师
     */
    public function dragDropBakeSendEmail(){

        $fccfpControlArr = FccfpControl::find()->where(['not', ['fcinsp_trackout' => null]])->andWhere(['subpbake_trackin' => null])->andWhere(['drag_drop_bake_time' => null])->andWhere(['in' , 'nowspecname' , ['SUB_PBAKE','FC_INSP']])->asArray()->all();
        if(empty($fccfpControlArr)){
            return 1;
        }
       
        $data = [];
        $timestamp1 = time();//当前时间
        foreach($fccfpControlArr as $value){
            $timestamp2 = strtotime($value['fcinsp_trackout']);
            $diff       = $timestamp1 - $timestamp2;
            $hours      = floor($diff / 3600);
            if($hours < 24){
                continue;
            }
            if(!empty($value['drag_drop_email_time'])){
                continue;
            }
            $model = FccfpControl::findOne(['id' => $value['id']]);
            $model->drag_drop_email_time = date('Y-m-d H:i:s', time());
            $model->save(false);

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

        $fccfpControlArr = FccfpControl::find()->where(['not', ['subpbake_trackout' => null]])->andWhere(['prempls_trackin' => null])->andWhere(['subpbake_email_time' => null])->andWhere(['in' , 'nowspecname' , ['SUB_PBAKE','PREM_PLS']])->asArray()->all();
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
        
        $fccfpControlArr = FccfpControl::find()->where(['not', ['subpbake_trackout' => null]])->andWhere(['prempls_trackin' => null])->andWhere(['prempls_email_time' => null])->andWhere(['in' , 'nowspecname' , ['SUB_PBAKE','PREM_PLS']])->asArray()->all();
        if(empty($fccfpControlArr)){
            return 1;
        }

        $data = [];
        $timestamp1 = time();//当前时间
        foreach($fccfpControlArr as $value){
            $timestamp2 = strtotime($value['subpbake_trackout']);
            $diff       = $timestamp1 - $timestamp2;
            $hours      = floor($diff / 3600);
            if($hours < 12){
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



    /**
     * 工序数据
     */
    public function actionTimeControl()
    {
        
        $this->getFebeData();

        //Staging Time Control接口
        $url = 'http://10.193.226.115:8080/portDataSystem/history/get/back';

        //GET请求
        $getData = $this->curlGet($url);
        if($getData['code'] != 200){
            return Json::encode(['status' => 200, 'msg' => 'success'], JSON_UNESCAPED_UNICODE);
        }
        if(empty($getData['data'])){
            return Json::encode(['status' => 200, 'msg' => 'success'], JSON_UNESCAPED_UNICODE);
        }


        $addLog = ["param" => json_encode($getData['data'])];
        (new FccfpLog())->create($addLog);

        //添加到记录表中
        $data   = [];
        $createTime = date('Y-m-d H:i:s', time());
        foreach($getData['data'] as $value){
            $insertArr = $this->fccfpiInsertArr;
            $insertArr['containername'] = strtoupper($value['containerName']);
            $insertArr['nowspecname']   = $value['nowSpecName'];
            foreach($value['fccspSpecTime'] as $key => $val){
                if(array_key_exists(strtolower($key),$this->fccfpiInsertArr)){
                    $insertArr[strtolower($key)] = $val;
                }
            }
            $insertArr['create_time'] = $createTime;
            $data[] = $insertArr;
        }
        //添加数据
        $insertFlowNode = Yii::$app->db->createCommand()->batchInsert(Fccfp::tableName(),$this->fccfpKeyArr, $data)->execute();
        if ($insertFlowNode <= 0) {
            return Json::encode(['status' => 200, 'msg' => 'success'], JSON_UNESCAPED_UNICODE);
        }


        //将之前已有的物料数据补充到接口获取的数据中，接口中获取的数据是只有物料在做更新时候才会获取到，所以需要将之前已经更新的数据进行匹配更新一下
        $fccfpListArr = FccfpList::find()->where(['!=','status', FccfpList::STATUS_PASS])->orderBy(['id' => SORT_DESC])->asArray()->all();
      
        if ($fccfpListArr) {
            $containernameArr = ArrayHelper::getColumn($data,'containername');
           
            foreach($fccfpListArr as $fccfpkey => $fccfpvalue){
                if(!in_array($fccfpvalue['containername'],$containernameArr)){
                    $addData = [];
                    $addData['nowspecname']         = $fccfpvalue['nowspecname']; 
                    $addData['containername']       = $fccfpvalue['containername']; 
                    $addData['nldpmc_trackout']     = $fccfpvalue['nldpmc_trackout']; 
                    $addData['fcmark_trackin']      = $fccfpvalue['fcmark_clean_start_time']; 
                    $addData['fcmark_trackout']     = $fccfpvalue['fcmark_clean_end_time']; 
                    $addData['fpfreflow_trackin']   = $fccfpvalue['fpfreflow_start_time']; 
                    $addData['fpfreflow_trackout']  = $fccfpvalue['fpfreflow_end_time']; 
                    $addData['fpfclean_trackin']    = $fccfpvalue['fpfclean_start_time']; 
                    $addData['fpfclean_trackout']   = $fccfpvalue['fpfclean_end_time']; 
                    $addData['br_trackin']          = $fccfpvalue['br_start_time']; 
                    $addData['br_trackout']         = $fccfpvalue['br_end_time']; 
                    $addData['fluxclean_trackin']   = $fccfpvalue['fluxclean_start_time']; 
                    $addData['fluxclean_trackout']  = $fccfpvalue['fluxclean_end_time'];
                    $data[] = $addData;
                }
            }
        }

        // PMC-->Marking-->Preflux&reflow-->Preflux&clean-->BallAttach-->Fluxclean
        $nowspeArr = ['FCMARK','FPF_REFLOW','FPF_CLEAN','BR','FLUX_CLEAN','BE_SAW'];
        //添加到数据展示表中
        foreach($data as $value){
            $nowspecname = strtoupper($value['nowspecname']);
            if(!in_array($nowspecname,$nowspeArr)){
                continue;
            }

            $order  = 0; //第几个倒计时
            $status = 0; //当前批号所处状态 0执行中 1已完成 2预警 3过期
            $timestamp1 = time();//当前时间
            $calibration_time = ''; //列表展示时间
            $fcmark_clean_use_date  = $fpfreflow_use_date  = $fpfclean_use_date  = $br_use_date  = $fluxclean_use_date  = '';//已用时间
            $fcmark_clean_date_left = $fpfreflow_date_left = $fpfclean_date_left = $br_date_left = $fluxclean_date_left = '';//剩余时间
            $fcmark_clean_color     = $fpfreflow_color     = $fpfclean_color     = $br_color     = $fluxclean_color     = $calibration_color = 5; //显示颜色   1:red2:yellow3:orange4:gray5:green
            $fcmark_clean_expiration_date = $fpfreflow_expiration_date =  $fpfclean_expiration_date = $br_expiration_date = $fluxclean_expiration_date = ''; //过期时间
      
            switch ($nowspecname) {
                case "FCMARK":
                    $order  = 1;
                    $timestamp2 = strtotime($value['nldpmc_trackout']);
                    $diff = $timestamp1 - $timestamp2;
                    $hours   = floor($diff / 3600);
                    $minutes = floor(($diff % 3600) / 60);
                    $fcmark_clean_use_date = $hours.'h'.$minutes.'m';//已用时间

                    //剩余时间和过期时间
                    $expirationtime = $timestamp2 + 144000;//过期时间
                    $timeleft = $expirationtime - $timestamp1;//用过期时间点减去当前时间就是剩余时间
                    $difftimeleft = $this->absolute_value($timeleft);
                    $hours    = floor($difftimeleft / 3600);
                    $minutes  = floor(($difftimeleft % 3600) / 60);
                    $calibration_time = $hours.'h'.$minutes.'m';
                   
                    //超时
                    if($timeleft < 0){
                        $status = 3;
                        $fcmark_clean_color = $calibration_color = 4;
                        $fcmark_clean_expiration_date = $calibration_time;
                    }else{
                        if($difftimeleft <= 21600){
                            //小于6小时变橘
                            $status = 2;
                            $fcmark_clean_color = $calibration_color = 3;
                        }elseif($difftimeleft <= 43200){
                            //小于12小时变黄
                            $status = 2;
                            $fcmark_clean_color = $calibration_color = 2;
                        }
                        $fcmark_clean_date_left = $calibration_time;
                    }
                    break;

                case "FPF_REFLOW":
                    $order  = 1;
                    $fcmark_clean_color = 0;
                    $timestamp2 = strtotime($value['nldpmc_trackout']);
                    $diff = $timestamp1 - $timestamp2;
                    $hours   = floor($diff / 3600);
                    $minutes = floor(($diff % 3600) / 60);
                    $fpfreflow_use_date = $hours.'h'.$minutes.'m';//已用时间

                    //剩余时间和过期时间
                    $expirationtime = $timestamp2 + 144000;//过期时间
                    $timeleft = $expirationtime - $timestamp1;//用过期时间点减去当前时间就是剩余时间
                    $difftimeleft = $this->absolute_value($timeleft);
                    $hours    = floor($difftimeleft / 3600);
                    $minutes  = floor(($difftimeleft % 3600) / 60);
                    $calibration_time = $hours.'h'.$minutes.'m';
                    //超时
                    if($timeleft < 0){
                        $status = 3;
                        $fcmark_clean_color = $fpfreflow_color = $fpfclean_color =  $calibration_color = 4;
                        $fpfreflow_expiration_date = $calibration_time;
                    }else{
                        // 当40hrs 倒计时到小于8hrs时，但物料仍未完成FPF_reflow，那么未完成工序（Marking，FPF_Reflow& FPF_Clean ）都显示为橘色
                        // 40hrs 和8hrs 叠加取最小值显示，例如当物料完成FPF_Reflow 后，比较40hrs剩余时间和8hrs大小，
                        // 如果剩余时间大于8hrs，按照8hrs 逻辑执行，执行8hrs倒计时；
                        // 如果剩余时间小于8hrs，则按照剩余时间执行倒计时；变色逻辑参考8hrs
                        //小于8小时
                        if($difftimeleft <= 28800){
                            $status = 2;
                            $fcmark_clean_color = $fpfreflow_color = $fpfclean_color =  $calibration_color = 3;
                        }elseif($difftimeleft <= 43200){
                            //小于12小时变黄
                            $status = 2;
                            $fpfreflow_color = $calibration_color = 2;
                        }
                        $fpfreflow_date_left = $calibration_time;
                    }

                    break;
                case "FPF_CLEAN":
                    $order  = 2;
                    $fcmark_clean_color = $fpfreflow_color = 0;
                    $timestamp2 = strtotime($value['fpfreflow_trackout']);
                    $diff = $timestamp1 - $timestamp2;
                    $hours   = floor($diff / 3600);
                    $minutes = floor(($diff % 3600) / 60);
                    $fpfclean_use_date = $hours.'h'.$minutes.'m';//已用时间

                    //剩余时间和过期时间
                    $expirationtime = $timestamp2 + 28800;//过期时间
                    $timeleft = $expirationtime - $timestamp1;//用过期时间点减去当前时间就是剩余时间
                    $difftimeleft = $this->absolute_value($timeleft);
                    $hours    = floor($difftimeleft / 3600);
                    $minutes  = floor(($difftimeleft % 3600) / 60);
                    $calibration_time = $hours.'h'.$minutes.'m';
                    //超时
                    if($timeleft < 0){
                        $status = 3;
                        $fcmark_clean_color = $fpfreflow_color = $fpfclean_color =  $calibration_color = 4;
                        $fpfclean_expiration_date = $calibration_time;
                    }else{
                        //小于2小时
                        if($difftimeleft <= 7200){
                            $status = 2;
                            $fpfclean_color = $calibration_color =  3;
                        }elseif($difftimeleft <= 14400){
                            //小于4小时变黄
                            $status = 2;
                            $fpfclean_color = $calibration_color = 2;
                        }
                        $fpfclean_date_left = $calibration_time;
                    }

                    break;
                case "BR":
                    
                    $order  = 3;
                    $fcmark_clean_color = $fpfreflow_color = $fpfclean_color = 0;
                    $timestamp2 = strtotime($value['fpfclean_trackout']);
                    $diff = $timestamp1 - $timestamp2;
                    $hours   = floor($diff / 3600);
                    $minutes = floor(($diff % 3600) / 60);
                    $br_use_date = $hours.'h'.$minutes.'m';//已用时间

                    //剩余时间和过期时间
                    $expirationtime = $timestamp2 + 43200;//过期时间12小时
                    $timeleft = $expirationtime - $timestamp1;//用过期时间点减去当前时间就是剩余时间
                    $difftimeleft = $this->absolute_value($timeleft);
                    $hours    = floor($difftimeleft / 3600);
                    $minutes  = floor(($difftimeleft % 3600) / 60);
                    $calibration_time = $hours.'h'.$minutes.'m';
                    //超时
                    if($timeleft < 0){
                        $status = 3;
                        $br_color = $calibration_color = 4;
                        $br_expiration_date = $calibration_time;
                    }else{
                        //小于2小时
                        if($difftimeleft <= 7200){
                            $status = 2;
                            $br_color = $calibration_color =  3;
                        }elseif($difftimeleft <= 14400){
                            //小于4小时变黄
                            $status = 2;
                            $br_color = $calibration_color = 2;
                        }
                        $br_date_left = $calibration_time;
                    }

                    break;
                case "FLUX_CLEAN":
                    $order  = 4;
                    $fcmark_clean_color = $fpfreflow_color = $fpfclean_color = $br_color = 0;
                    $timestamp2 = strtotime($value['br_trackout']);
                    $diff = $timestamp1 - $timestamp2;
                    $hours   = floor($diff / 3600);
                    $minutes = floor(($diff % 3600) / 60);
                    $fluxclean_use_date = $hours.'h'.$minutes.'m';//已用时间

                    //剩余时间和过期时间
                    $expirationtime = $timestamp2 + 216000;//过期时间60小时
                    $timeleft = $expirationtime - $timestamp1;//用过期时间点减去当前时间就是剩余时间
                    $difftimeleft = $this->absolute_value($timeleft);
                    $hours    = floor($difftimeleft / 3600);
                    $minutes  = floor(($difftimeleft % 3600) / 60);
                    $calibration_time = $hours.'h'.$minutes.'m';
                 
                    //超时
                    if($timeleft < 0){
                        $status = 3;
                        $fluxclean_color = $calibration_color = 4;
                        $fluxclean_expiration_date = $calibration_time;
                    }else{
                        //小于6小时
                        if($difftimeleft <= 21600){
                            $status = 2;
                            $fluxclean_color = $calibration_color =  3;
                        }elseif($difftimeleft <= 43200){
                            //小于12小时变黄
                            $status = 2;
                            $fluxclean_color = $calibration_color = 2;
                        }
                        $fluxclean_date_left = $calibration_time;
                    }

                    break;
                case "BE_SAW":
                    $order  = 0;
                    $status = 1;
                    $fcmark_clean_color = $fpfreflow_color = $fpfclean_color = $br_color = $fluxclean_color= $calibration_color = 0; //显示颜色   1:red2:yellow3:orange4:gray5:green
                    break;
                default:
                    $status = 0;
            }


            $fccfpList = FccfpList::findOne(['containername' => $value['containername']]);
            if($fccfpList){
                $fccfpList->nowspecname                 = $value['nowspecname'];
                $fccfpList->nldpmc_trackout             = $value['nldpmc_trackout'];
                $fccfpList->fcmark_clean_date_left      = $fcmark_clean_date_left;
                $fccfpList->fcmark_clean_color          = $fcmark_clean_color;
                $fccfpList->fcmark_clean_start_time     = $value['fcmark_trackin'];
                $fccfpList->fcmark_clean_end_time       = $value['fcmark_trackout'];
                $fccfpList->fcmark_clean_use_date       = $fcmark_clean_use_date;
                $fccfpList->fcmark_clean_expiration_date= $fcmark_clean_expiration_date;
                $fccfpList->fpfreflow_date_left         = $fpfreflow_date_left;
                $fccfpList->fpfreflow_color             = $fpfreflow_color;
                $fccfpList->fpfreflow_start_time        = $value['fpfreflow_trackin'];
                $fccfpList->fpfreflow_end_time          = $value['fpfreflow_trackout'];
                $fccfpList->fpfreflow_use_date          = $fpfreflow_use_date;
                $fccfpList->fpfreflow_expiration_date   = $fpfreflow_expiration_date;
                $fccfpList->fpfclean_date_left          = $fpfclean_date_left;
                $fccfpList->fpfclean_color              = $fpfclean_color;
                $fccfpList->fpfclean_start_time         = $value['fpfreflow_trackin'];
                $fccfpList->fpfclean_end_time           = $value['fpfclean_trackout'];
                $fccfpList->fpfclean_use_date           = $fpfclean_use_date;
                $fccfpList->fpfclean_expiration_date    = $fpfclean_expiration_date;
                $fccfpList->br_date_left                = $br_date_left;
                $fccfpList->br_color                    = $br_color;
                $fccfpList->br_start_time               = $value['br_trackin'];
                $fccfpList->br_end_time                 = $value['br_trackout'];
                $fccfpList->br_use_date                 = $br_use_date;
                $fccfpList->br_expiration_date          = $br_expiration_date;
                $fccfpList->fluxclean_date_left         = $fluxclean_date_left;
                $fccfpList->fluxclean_color             = $fluxclean_color;
                $fccfpList->fluxclean_start_time        = $value['fluxclean_trackin'];
                $fccfpList->fluxclean_end_time          = $value['fluxclean_trackout'];
                $fccfpList->fluxclean_use_date          = $fluxclean_use_date;
                $fccfpList->fluxclean_expiration_date   = $fluxclean_expiration_date;
                $fccfpList->update_time                 = date('Y-m-d H:i:s',time());
                $fccfpList->status                      = $status;
                $fccfpList->order                       = $order;
                $fccfpList->calibration_time            = $calibration_time;
                $fccfpList->calibration_color           = $calibration_color;
                $fccfpList->save(false);
            }else{
                $model                              = new FccfpList();
                $model->containername               = $value['containername'];
                $model->nowspecname                 = $value['nowspecname'];
                $model->nldpmc_trackout             = $value['nldpmc_trackout'];
                $model->fcmark_clean_date_left      = $fcmark_clean_date_left;
                $model->fcmark_clean_color          = $fcmark_clean_color;
                $model->fcmark_clean_time_limit     = '40hrs';
                $model->fcmark_clean_start_process  = 'PMC';
                $model->fcmark_clean_end_process    = 'Marking';
                $model->fcmark_clean_start_time     = $value['fcmark_trackin'];
                $model->fcmark_clean_end_time       = $value['fcmark_trackout'];
                $model->fcmark_clean_use_date       = $fcmark_clean_use_date;
                $model->fcmark_clean_expiration_date= $fcmark_clean_expiration_date;              
                $model->fpfreflow_date_left         = $fpfreflow_date_left;
                $model->fpfreflow_color             = $fpfreflow_color;
                $model->fpfreflow_time_limit        = '40hrs';
                $model->fpfreflow_start_process     = 'Marking';
                $model->fpfreflow_end_process       = 'Preflux&reflow';
                $model->fpfreflow_start_time        = $value['fpfreflow_trackin'];
                $model->fpfreflow_end_time          = $value['fpfreflow_trackout'];
                $model->fpfreflow_use_date          = $fpfreflow_use_date;
                $model->fpfreflow_expiration_date   = $fpfreflow_expiration_date;
                $model->fpfclean_date_left          = $fpfclean_date_left;
                $model->fpfclean_color              = $fpfclean_color;
                $model->fpfclean_time_limit         = '8hrs';
                $model->fpfclean_start_process      = 'Preflux&reflow';
                $model->fpfclean_end_process        = 'Preflux&clean';
                $model->fpfclean_start_time         = $value['fpfreflow_trackin'];
                $model->fpfclean_end_time           = $value['fpfclean_trackout'];
                $model->fpfclean_use_date           = $fpfclean_use_date;
                $model->fpfclean_expiration_date    = $fpfclean_expiration_date;
                $model->br_date_left                = $br_date_left;
                $model->br_color                    = $br_color;
                $model->br_time_limit               = '12hrs';
                $model->br_start_process            = 'Preflux&clean';
                $model->br_end_process              = 'BallAttach';
                $model->br_start_time               = $value['br_trackin'];
                $model->br_end_time                 = $value['br_trackout'];
                $model->br_use_date                 = $br_use_date;
                $model->br_expiration_date          = $br_expiration_date;
                $model->fluxclean_date_left         = $fluxclean_date_left;
                $model->fluxclean_color             = $fluxclean_color;
                $model->fluxclean_time_limit        = '60hrs';
                $model->fluxclean_start_process     = 'BallAttach';
                $model->fluxclean_end_process       = 'Fluxclean';
                $model->fluxclean_start_time        = $value['fluxclean_trackin'];
                $model->fluxclean_end_time          = $value['fluxclean_trackout'];
                $model->fluxclean_use_date          = $fluxclean_use_date;
                $model->fluxclean_expiration_date   = $fluxclean_expiration_date;
                $model->update_time                 = date('Y-m-d H:i:s',time());
                $model->status                      = $status;
                $model->order                       = $order;
                $model->calibration_time            = $calibration_time;
                $model->calibration_color           = $calibration_color;
                $model->create_time                 = date('Y-m-d H:i:s', time());
                $model->save(false);
            }
        }
        
        return Json::encode(['status' => 200, 'msg' => 'success'], JSON_UNESCAPED_UNICODE);
    }
    
}