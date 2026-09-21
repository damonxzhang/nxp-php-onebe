<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\Json;
use backend\models\User;
use yii\helpers\ArrayHelper;
use backend\models\UserInfo;
use backend\models\LogHandover;
use backend\models\LogHandoverType;
use backend\models\LogHandoverBatch;
use backend\models\LogHandoverUser;
use backend\models\LogHandoverCommentType;
use backend\models\LogHandoverDefault;
use backend\models\LogHandoverDeviceType;
use backend\models\LogHandoverSensitiveWords;


class LogHandoverPadController extends Controller
{

    public $layout=false; //重写这个属性就可以不将layouts公共模板引入进来
    public $enableCsrfValidation = false; //取消CSRF验证
    protected $classesArr = [
        1  => '白班',
        2  => '夜班'
    ];


    /**
     * 获取登陆ID
     * @return int|string
     */
    public function getUid()
    {
        $id= Yii::$app->user->getId();
        $userModel = User::findOne($id);
        return $userModel ? $userModel ->user_id : '';
    }

    /**
     * 白夜班
     * @return int|string
     */
    public function getClasses()
    {
        $classes = '夜班';
        if(time() >= strtotime('today 08:00') && time() <= strtotime('today 20:00')){
            $classes = '白班';
        }elseif(time() > strtotime('today 20:00') && time() <= strtotime('today 24:00')){
            $classes = '夜班';
        }elseif(time() < strtotime('today 08:00')){
            $classes = '夜班';
        }
        return $classes;
    }

    /**
     * 登陆接口
     * 请求方式 POST
     */
    public function actionLogin()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $userInfo = UserInfo::findOne(['pid' => trim($post['pid'])]);
            if(empty($userInfo)){
                return $this->json(100, 'PID不存在!');
            }
            $user = User::findOne(['user_id' => $userInfo->id]);
            if(empty($user)){
                return $this->json(100, '无此PID用户信息,请联系管理员在Onebe中创建用户!');
            }
            $res = Yii::$app->security->validatePassword($post['password'], $user->password);
            if (!$res) {
                return $this->json(100, '登陆密码不正确!');
            }
            Yii::$app->user->login($user);
            return $this->json(200, '登录成功');
        }
        return $this->render('login');
    }

    /**
     * 登出
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect(['log-handover-pad/login']);
    }

    /**
     * 下拉选择框数据
     */
    public function selectLogHandoverArr()
    {
        $uid      = $this->getUid();
        $userInfo = UserInfo::findOne(['id' => $uid]);
        $logHandoverTypeData        = LogHandoverType::find()->where(['status'=> 1])->andWhere(['section'=>$userInfo->section])->orderBy(['id' => SORT_DESC])->asArray()->all();//物料种类名称
        $logHandoverBatchData       = LogHandoverBatch::find()->where(['status'=> 1])->andWhere(['section'=>$userInfo->section])->orderBy(['id' => SORT_DESC])->asArray()->all();  //批次
        $logHandoverDeviceTypeData  = LogHandoverDeviceType::find()->where(['status'=> 1])->andWhere(['section'=>$userInfo->section])->orderBy(['id' => SORT_DESC])->asArray()->all();//设备类型
        $logHandoverCommentTypeData = LogHandoverCommentType::find()->where(['status'=> 1])->andWhere(['section'=>$userInfo->section])->orderBy(['id' => SORT_DESC])->asArray()->all();//备注
     
        $data['logHandoverTypeArr']        = empty($logHandoverTypeData) ? [] : ArrayHelper::map($logHandoverTypeData,'id','title');
        $data['logHandoverBatchArr']       = empty($logHandoverBatchData) ? [] :  ArrayHelper::map($logHandoverBatchData,'id','title');
        $data['logHandoverDeviceTypeArr']  = empty($logHandoverDeviceTypeData) ? [] : ArrayHelper::map($logHandoverDeviceTypeData,'id','title');
        $data['logHandoverCommentTypeArr'] = empty($logHandoverCommentTypeData) ? [] : ArrayHelper::map($logHandoverCommentTypeData,'id','title');

        return $data;

    }
    
    /**
     * ajax 返回json数据
     * @param int $status  状态码
     * @param string $msg  提示信息
     * @param string $data 返回数据
     * @return string
     */
    public function json($status, $msg, $data = '')
    {
        if ($data) {
            return Json::encode(['status' => $status, 'msg' => $msg, 'data' => $data], JSON_UNESCAPED_UNICODE);
        } else {
            return Json::encode(['status' => $status, 'msg' => $msg, 'data' => $data], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * 清洗日志内容检查是否存在敏感词
    */
    public function brushStr($content)
    {
        $result   = ['content'=> $content,'is_sensitive_words' => 0];
        $userInfo = UserInfo::findOne(['id' => $this->getUid()]);
        $titleArr = LogHandoverSensitiveWords::find()->select('title')->where(['status' => LogHandoverSensitiveWords::STATUS_OPEN,'section'=>$userInfo->section])->column();
        if(empty($titleArr)){
            return $result;
        }
        foreach($titleArr as $title){
            if(stristr($result['content'], $title) !== false){
                $result['content'] = str_replace( $title,"",$result['content']);
                $result['is_sensitive_words'] = 1;
            }
        }
        return $result;
    }
    
    /**
     * 上一个交接班
     */
    public function actionLastonelog()
    {
        $uid = $this->getUid();
        if(!$uid){
            return $this->redirect(['log-handover-pad/login']);
        }
        $classesStr = '白班';
        $classes    = 1;//白班
        $date       = date("Y-m-d");
        //当前时间在白班就需要看昨天夜班的交接
        if(time() >= strtotime('today 08:00') && time() <= strtotime('today 20:00')){
            $classes = 2;
            $classesStr = '夜班';
            $date = date("Y-m-d",strtotime("-1 day"));
        }elseif(time() > strtotime('today 20:00') && time() < strtotime('today 24:00')){
            $classes = 1;
            $classesStr = '白班';
            $date = date("Y-m-d");
		}elseif(time() > strtotime('today 00:00') && time() < strtotime('today 07:00')){
            $classes = 1;
            $classesStr = '白班';
            $date = date("Y-m-d",strtotime("-1 day"));
        }elseif(time() < strtotime('today 08:00')){
            $classes = 1;
            $classesStr = '白班';
            $date = date("Y-m-d");
        }
        $userInfo = UserInfo::findOne(['id' => $uid]);
        $dataProvider = LogHandover::find()->where(['section'=>$userInfo->section,'date'=>$date,'classes' => $classes,'is_del' => 0])->orderBy(['id'=> SORT_DESC])->asArray()->all();
        if($dataProvider && isset($dataProvider[0])){
            $classes  = ArrayHelper::getValue($this->classesArr, $dataProvider[0]['classes'], '');
            $shiftStr = ArrayHelper::getValue(UserInfo::$shiftArr, $dataProvider[0]['shift'], '');
            $userStr  = $classes .' - '. $shiftStr .' - '. $dataProvider[0]['chinese_name'];
        }else{
            $userStr  = $classesStr;
        }
        
        return $this->render('lastonelog', [
            'date'         => $date,
            'userStr'      => $userStr,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * 日志交接列表数据
     * @return string
     */
    public function actionIndex()
    {
        $uid= $this->getUid();
        if(!$uid){
            return $this->redirect(['log-handover-pad/login']);
        }
        $userInfo = UserInfo::findOne(['id' => $uid]);
        $shiftStr = ArrayHelper::getValue(UserInfo::$shiftArr, $userInfo->shift, '');
        $classes  = $this->getClasses();
        $userStr  = $classes .' - '. $shiftStr .' - '.$userInfo->chinese_name;
    
        //用户当前的交接状态 0 创建交接 1已提交交接 2确认交接 3上一个人没有点确认交接
        $userCopulateNow = 0;
        $checkLogHandover = LogHandover::find()->where(['copulate'=> LogHandover::COPULATE_SUBMIT,'user_id'=>$userInfo->id])->one();
        if($checkLogHandover){
            $userCopulateNow = 1; //已经提交交接显示撤销按钮
        }else{
            //同一个区域如果有不是和自己同一个班别的交接日志,并且存在交接数据,那当前就是需要点击确认交接状态
            $countSubmitCopulate = LogHandover::find()->where(['section'=>$userInfo->section,'copulate'=>LogHandover::COPULATE_SUBMIT])->andWhere(['!=', 'shift', $userInfo->shift])->one();
            if($countSubmitCopulate){
                $userCopulateNow = 2;//上一个人已经点了提交交接,当前需要点确认交接
            // }else{
            //     //检查是否点了提交交接但是有延续的日志
            //     $countWaitCopulate = LogHandover::find()->where(['section'=>$userInfo->section,'copulate'=>LogHandover::COPULATE_WAIT])->andWhere(['!=', 'shift', $userInfo->shift])->one();
            //     if($countWaitCopulate){ 
            //         $userCopulateNow = 3;//既没有人点击提交交接按钮,又没有点击确认交接按钮和延续的交接日志,证明上一个人没有点击提交按钮
            //     }
            }
        }

        //默认提交按钮展示
        $showDefault = 1;//不显示默认提交按钮
        $userCopulate = LogHandover::find()->where(['user_id'=>$userInfo->id,'copulate'=>LogHandover::COPULATE_WAIT])->one();
        if(!$userCopulate && $userCopulateNow == 0){
            $showDefault = 0;//显示提交按钮
        }

        $logHandoverUser = LogHandoverUser::findOne(['user_id' => $uid]);
        if(!$logHandoverUser){
             $showDefault = 1;//不显示默认提交按钮
             $userCopulateNow = 3;
        }
       
        $dataProvider = LogHandover::find()->where(['section'=>$userInfo->section])->andWhere(['copulate' => [LogHandover::COPULATE_WAIT,LogHandover::COPULATE_SUBMIT,LogHandover::COPULATE_GO]])->orderBy(['id' => SORT_ASC])->asArray()->all();
        $logHandoverArr = $this->selectLogHandoverArr();
        return $this->render('index', [
            'uid'                       => $uid,
            'userStr'                   => $userStr,
            'userInfo'                  => $userInfo,
            'showDefault'               => $showDefault,
            'dataProvider'              => $dataProvider,
            'userCopulateNow'           => $userCopulateNow,
            'date'                      => date('Y-m-d',time()),
            'logHandoverTypeArr'        => $logHandoverArr['logHandoverTypeArr'],
            'logHandoverBatchArr'       => $logHandoverArr['logHandoverBatchArr'],
            'logHandoverDeviceTypeArr'  => $logHandoverArr['logHandoverDeviceTypeArr'],
            'logHandoverCommentTypeArr' => $logHandoverArr['logHandoverCommentTypeArr']
        ]);
    }
    
    /**
     * 提交白班交接
     * @return string
     */
    public function actionSubmitHandoverWhite()
    {
        $uid = $this->getUid();
        $userInfo         = UserInfo::findOne(['id' => $uid]);
        $checkLogHandover = LogHandover::find()->where(['section'=>$userInfo->section,'status'=>LogHandover::STATUS_OPEN,'copulate'=> LogHandover::COPULATE_GO])->one();
        if($checkLogHandover){
            return $this->json(100, '你有需要补充的交接日志,请补充完交接日志以后再提交!');
        }
        
        $checkLogHandover = LogHandover::find()->where(['copulate'=> LogHandover::COPULATE_WAIT,'user_id'=>$userInfo->id])->one();
        if(!$checkLogHandover){
            return $this->json(100, '请先创建今天的交接班日志再提交!');
        }

        $date       = date("Y-m-d");
      //  $updateData = LogHandover::updateAll(['classes' => 1,'date' => $date,'copulate' => LogHandover::COPULATE_SUBMIT,'submit_time'=> date('Y-m-d H:i:s', time())],['user_id'=> $uid,'copulate'=> LogHandover::COPULATE_WAIT]);
        $updateData = LogHandover::updateAll(['classes' => 1,'date' => $date,'copulate' => LogHandover::COPULATE_SUBMIT,'submit_time'=> date('Y-m-d H:i:s', time())],['section'=>$userInfo->section,'copulate'=> LogHandover::COPULATE_WAIT]);
        if($updateData === false){
            return $this->json(100, '提交交接失败!');
        }
     
        return $this->json(200, '提交交接成功!');
    }

    /**
     * 提交夜班交接
     * @return string
     */
    public function actionSubmitHandoverDark()
    {
        $uid = $this->getUid();
        $userInfo         = UserInfo::findOne(['id' => $uid]);
        $checkLogHandover = LogHandover::find()->where(['section'=>$userInfo->section,'status'=>LogHandover::STATUS_OPEN,'copulate'=> LogHandover::COPULATE_GO])->one();
        if($checkLogHandover){
          
            return $this->json(100, '你有需要补充的交接日志,请补充完交接日志以后再提交!');
        }
        
        $checkLogHandover = LogHandover::find()->where(['copulate'=> LogHandover::COPULATE_WAIT,'user_id'=>$userInfo->id])->one();
        if(!$checkLogHandover){
          
            return $this->json(100, '请先创建今天的交接班日志再提交!');
        }

        $date = date("Y-m-d",strtotime("-1 day"));
        if(time() >= strtotime('today 20:00') && time() <= strtotime('today 24:00')){
            $date = date("Y-m-d");
        }
       // $updateData = LogHandover::updateAll(['classes' => 2,'date' => $date,'copulate' => LogHandover::COPULATE_SUBMIT,'submit_time'=> date('Y-m-d H:i:s', time())],['user_id'=> $uid,'copulate'=> LogHandover::COPULATE_WAIT]);
        $updateData = LogHandover::updateAll(['classes' => 2,'date' => $date,'copulate' => LogHandover::COPULATE_SUBMIT,'submit_time'=> date('Y-m-d H:i:s', time())],['section'=>$userInfo->section,'copulate'=> LogHandover::COPULATE_WAIT]);
        if($updateData === false){
            return $this->json(100, '提交交接失败!');
        }
     
        return $this->json(200, '提交交接成功!');
    }


    /**
     * 撤销交接
     * @return string
     */
    public function actionRetractHandover()
    {
        $uid = $this->getUid();
        $updateData = LogHandover::updateAll(['copulate' => LogHandover::COPULATE_WAIT,'date'=>'','classes'=> 0],['user_id'=> $uid,'copulate'=> LogHandover::COPULATE_SUBMIT]);
        if($updateData === false){
          
            return $this->json(100, '撤销交接失败!');
        }
     
        return $this->json(200, '撤销交接成功!');
    }


    /**
     * 确认交接
     * @return string
     */
    public function actionConfirmedHandover()
    {
        $uid          = $this->getUid();
        $userInfo     = UserInfo::findOne(['id' => $uid]);
      

        $dataProvider = LogHandover::find()->where(['section'=>$userInfo->section,'status' => LogHandover::STATUS_OPEN,'copulate' => LogHandover::COPULATE_SUBMIT])->andWhere(['!=', 'shift', $userInfo->shift])->asArray()->all();
        if($dataProvider){
            foreach ($dataProvider as $value) {
                $model = LogHandover::findOne($value['id']);  
                $model->copulate               = LogHandover::COPULATE_GO;
                $model->copulate_user_id       = $userInfo->id;
                $model->copulate_chinese_name  = $userInfo->chinese_name;
                $model->copulate_pid           = $userInfo->pid;
                $model->copulate_shift         = $userInfo->shift;
                $model->copulate_section       = $userInfo->section;
                $model->copulate_time          = date('Y-m-d H:i:s', time());
                if ($model->classes == 2) {
                    $model->copulate_classes = 1;
                } else {
                    $model->copulate_classes = 2;
                }
                $model->save(false);
            }
        }

        //把所有已完成的更新一下
        $updateProvider = LogHandover::find()->where(['section'=>$userInfo->section,'status' => LogHandover::STATUS_CLOSE])->andWhere(['!=', 'copulate', LogHandover::COPULATE_VERIFY])->asArray()->all();
        if($updateProvider){
            foreach ($updateProvider as $value) {
                if($value['copulate'] == LogHandover::COPULATE_VERIFY){
                    continue;
                }
                if($value['handover_id'] > 0){
                    LogHandover::updateAll(['copulate' => LogHandover::COPULATE_VERIFY],['id'=> $value['handover_id']]);
                    LogHandover::updateAll(['copulate' => LogHandover::COPULATE_VERIFY],['handover_id'=> $value['handover_id']]);
                }
                $model = LogHandover::findOne($value['id']);
                $model->copulate = LogHandover::COPULATE_VERIFY;
                if($value['copulate_user_id'] == 0){
                    $model->copulate_user_id       = $userInfo->id;
                    $model->copulate_chinese_name  = $userInfo->chinese_name;
                    $model->copulate_pid           = $userInfo->pid;
                    $model->copulate_shift         = $userInfo->shift;
                    $model->copulate_section       = $userInfo->section;
                    $model->copulate_time          = date('Y-m-d H:i:s', time());
                    if ($model->classes == 2) {
                        $model->copulate_classes = 1;
                    } else {
                        $model->copulate_classes = 2;
                    }
                }
                $model->save(false);
            }
        }

        return $this->json(200, '确认交接成功!');
    }

    /**
     * 添加物料种类
     * @return string
     */
    public function actionTypeCreate()
    {
        $post  = Yii::$app->request->post();
        $uid                  = $this->getUid();
        $userInfo             = UserInfo::findOne(['id' => $uid]);
        $post['type']         = 1;
        $post['type_name']    = LogHandover::TYPE_MATERIAL;
        $post['user_id']      = $uid;
        $post['chinese_name'] = $userInfo ->chinese_name;
        $post['pid']          = $userInfo ->pid;
        $post['shift']        = $userInfo ->shift;
        $post['section']      = $userInfo ->section;
        $post['type_id']      = intval($post['type_id']);
        $logHandoverType      = LogHandoverType::findOne(['id' => $post['type_id']]);
        $post['type_title']   = $logHandoverType->title;
        $post['batch_id']     = intval($post['batch_id']);
        $logHandoverBatch     = LogHandoverBatch::findOne(['id' => $post['batch_id']]);
        $post['batch_title']  = $logHandoverBatch->title;
        $brushStr             = $this->brushStr($post['content']);
        $post['is_sensitive_words']= $brushStr['is_sensitive_words'];
        $post['content']      = $post['content'];
        if($logHandoverType->is_audit == 2){
            $post['content_audit']  = $brushStr['content'];
            $post['is_audit']       = 1;
        }

        $model = new LogHandover();
        $res   = $model->create($post);

        if ($res['status'] != 200) {
            return $this->json(100, '添加失败!');
        }
        return $this->json(200, '添加成功!');
    }

    /**
    * 默认交接按钮提交的默认物料种类
     * @return string
     */
    public function actionDefaultHandoverMaterialCreate()
    {
        $uid                  = $this->getUid();
        $userInfo             = UserInfo::findOne(['id' => $uid]);
        $logHandoverDefault   = LogHandoverDefault::find()->where(['type' => LogHandoverDefault::TYPE_MATERIAL,'section' => $userInfo ->section,'status'=> LogHandoverDefault::STATUS_OPEN])->one();
        if (!$logHandoverDefault) {
            return $this->json(100, '暂无默认物料交接内容!');
        }
        $post                 = [];
        $post['status']       = 2;
        $post['type']         = 1;
        $post['type_name']    = LogHandover::TYPE_MATERIAL;
        $post['user_id']      = $uid;
        $post['chinese_name'] = $userInfo ->chinese_name;
        $post['pid']          = $userInfo ->pid;
        $post['shift']        = $userInfo ->shift;
        $post['section']      = $userInfo ->section;
        $post['type_id']      = $logHandoverDefault->type_id;
        $logHandoverType      = LogHandoverType::findOne(['id' => $logHandoverDefault->type_id]);
        $post['type_title']   = $logHandoverType->title;
        $post['batch_id']     = $logHandoverDefault->batch_id;
        $logHandoverBatch     = LogHandoverBatch::findOne(['id' => $logHandoverDefault->batch_id]);
        $post['batch_title']  = $logHandoverBatch->title;        
        $post['content']      = $logHandoverDefault->title;
        $brushStr             = $this->brushStr($logHandoverDefault->title);
        $post['is_sensitive_words']= $brushStr['is_sensitive_words'];
        if($logHandoverType->is_audit == 2){
            $post['content_audit']  = $brushStr['content'];
            $post['is_audit']       = 1;
        }
        $model = new LogHandover();
        $res   = $model->create($post);
        if ($res['status'] != 200) {
            return $this->json(100, '添加默认物料交接内容失败!');
        }
       
        return $this->json(200, '添加默认物料交接内容成功!');
    }

    /**
     * 更新物料种类
     * @return string
     */
    public function actionTypeUpdate()
    {
        if (Yii::$app->request->isPost) {
            $post  = Yii::$app->request->post();
          
            $model                 = LogHandover::findOne($post['id']);
            $brushStr              = $this->brushStr(trim($post['content']));
            $model->content        = $post['content'];
            $model->is_sensitive_words= $brushStr['is_sensitive_words'];
            $model->status         = $post['status'];
            $post['type_id']       = intval($post['type_id']);
            $logHandoverType       = LogHandoverType::findOne(['id' => $post['type_id']]);
            $model->type_id        = $post['type_id'];
            $model->type_title     = $logHandoverType->title;
            $logHandoverBatch      = LogHandoverBatch::findOne(['id' => $post['batch_id']]);
            $model->batch_id       = $post['batch_id'];
            $model->batch_title    = $logHandoverBatch->title;
            $model->update_time    = date('Y-m-d H:i:s', time());       
            //同步更新Audit
            if($logHandoverType->is_audit == 2){
                $model->content_audit  = $brushStr['content'];
                $model->is_audit       = 1;
            }else{
                $model->content_audit  = '';
                $model->is_audit       = 0;   
            }
            if (!$model->save(false)) {
                return $this->json(100, '更新失败!');
            }
            return $this->json(200, '更新成功!');
        }
        $id    = Yii::$app->request->get('id');
        $model = LogHandover::find()->where(['id' => $id])->asArray()->one();
        $logHandoverArr = $this->selectLogHandoverArr();
        return $this->render('type-update',[
            'model'                     => $model,
            'logHandoverTypeArr'        => $logHandoverArr['logHandoverTypeArr'],
            'logHandoverBatchArr'       => $logHandoverArr['logHandoverBatchArr']
        ]); 
    }


    /**
     * 添加设备
     * @return string
     */
    public function actionDeviceTypeCreate()
    {
        $post  = Yii::$app->request->post();
        $uid                    = $this->getUid();
        $userInfo               = UserInfo::findOne(['id' => $uid]);
        $post['type']           = 2;
        $post['type_name']      = LogHandover::TYPE_APPLIANCES;
        $post['user_id']        = $uid;
        $post['chinese_name']   = $userInfo ->chinese_name;
        $post['pid']            = $userInfo ->pid;
        $post['shift']          = $userInfo ->shift;
        $post['section']        = $userInfo ->section;
        $post['device_type_id'] = intval($post['device_type_id']);
        $logHandoverDeviceType  = LogHandoverDeviceType::findOne(['id' => $post['device_type_id']]);
        $post['device_type_title']   = $logHandoverDeviceType->title;
        $post['content']      = $post['content'];
        $brushStr             = $this->brushStr($post['content']);
        $post['is_sensitive_words']= $brushStr['is_sensitive_words'];
        //同步Audit
        if($logHandoverDeviceType->is_audit == 2){
            $post['content_audit']  = $brushStr['content'];
            $post['is_audit']       = 1;
        }
        $model = new LogHandover();
        $res   = $model->create($post);

        if ($res['status'] != 200) {
            return $this->json(100, '添加失败!');
        }
        return $this->json(200, '添加成功!');
    }

    /**
     * 添加默认设备日志
     * @return string
     */
    public function actionDefaultHandoverAppliancesCreate()
    {
        $uid                  = $this->getUid();
        $userInfo             = UserInfo::findOne(['id' => $uid]);
        $logHandoverDefault   = LogHandoverDefault::find()->where(['type' => LogHandoverDefault::TYPE_PLANT,'section' => $userInfo ->section,'status'=> LogHandoverDefault::STATUS_OPEN])->one();
        if (!$logHandoverDefault) {
            return $this->json(100, '暂无默认设备交接内容!');
        }
        $post                   = [];
        $brushStr               = $this->brushStr($logHandoverDefault->title);
        $post['is_sensitive_words']= $brushStr['is_sensitive_words'];
        $post['content']        = $logHandoverDefault->title;
        $post['status']         = 2;
        $post['type']           = 2;
        $post['type_name']      = LogHandover::TYPE_APPLIANCES;
        $post['user_id']        = $uid;
        $post['chinese_name']   = $userInfo ->chinese_name;
        $post['pid']            = $userInfo ->pid;
        $post['shift']          = $userInfo ->shift;
        $post['section']        = $userInfo ->section;
        $post['device_type_id'] = $logHandoverDefault->device_type_id;
        $logHandoverDeviceType  = LogHandoverDeviceType::findOne(['id' => $logHandoverDefault->device_type_id]);
        $post['device_type_title'] = $logHandoverDeviceType->title;
        if($logHandoverDeviceType->is_audit == 2){
            $post['content_audit']  = $brushStr['content'];
            $post['is_audit']       = 1;
        }
        $model = new LogHandover();
        $res   = $model->create($post);
        if ($res['status'] != 200) {
            return $this->json(100, '添加默认设备交接内容失败!');
        }
      
        return $this->json(200, '添加默认设备交接内容成功!');
    }

    /**
     * 更新设备
     * @return string
     */
    public function actionDeviceTypeUpdate()
    {
        if (Yii::$app->request->isPost) {
            $post  = Yii::$app->request->post();
          
            $model                    = LogHandover::findOne($post['id']);
            $brushStr                 = $this->brushStr(trim($post['content']));
            $model->content           = $post['content'];
            $model->is_sensitive_words= $brushStr['is_sensitive_words'];
            $model->status            = $post['status'];
            $post['device_type_id']   = intval($post['device_type_id']);
            $logHandoverDeviceType    = LogHandoverDeviceType::findOne(['id' => $post['device_type_id']]);
            $model->device_type_id    = $post['device_type_id'];
            $model->device_type_title = $logHandoverDeviceType->title;
            $model->update_time       = date('Y-m-d H:i:s', time());
            //同步更新Audit
            if($logHandoverDeviceType->is_audit == 2){
                $model->content_audit  = $brushStr['content'];
                $model->is_audit       = 1;
            }else{
                $model->content_audit  = '';
                $model->is_audit       = 0;   
            }
            if (!$model->save(false)) {
                return $this->json(100, '更新失败!');
            }
            return $this->json(200, '更新成功!');
        }
        
        $logHandoverArr = $this->selectLogHandoverArr();
        $id    = Yii::$app->request->get('id');
        $model = LogHandover::find()->where(['id' => $id])->asArray()->one();
        return $this->render('device-type-update',[
            'model' => $model,
            'logHandoverDeviceTypeArr'  => $logHandoverArr['logHandoverDeviceTypeArr']
        ]); 
    }

    /**
     * 添加备注
     * @return string
     */
    public function actionCommentCreate()
    {
        $post  = Yii::$app->request->post();
        $uid                    = $this->getUid();
        $userInfo               = UserInfo::findOne(['id' => $uid]);
        $post['type']           = 3;
        $post['type_name']      = LogHandover::TYPE_COMMENT;
        $post['user_id']        = $uid;
        $post['chinese_name']   = $userInfo ->chinese_name;
        $post['pid']            = $userInfo ->pid;
        $post['shift']          = $userInfo ->shift;
        $post['section']        = $userInfo ->section;
        $post['comment_id']     = intval($post['comment_id']);
        $logHandoverCommentType = LogHandoverCommentType::findOne(['id' => $post['comment_id']]);
        $post['comment_title']  = $logHandoverCommentType->title;
        $brushStr               = $this->brushStr($post['content']);
        $post['is_sensitive_words']= $brushStr['is_sensitive_words'];
        $post['content']        = $post['content'];
        if($logHandoverCommentType->is_audit == 2){
            $post['content_audit']  = $brushStr['content'];
            $post['is_audit']       = 1;
        }

        $model = new LogHandover();
        $res   = $model->create($post);
        if ($res['status'] != 200) {
            return $this->json(100, '添加失败!');
        }
        return $this->json(200, '添加成功!');
    }

    /**
     * 添加默认备注种类
     * @return string
     */
    public function actionDefaultHandoverCommentCreate()
    {
        $uid                  = $this->getUid();
        $userInfo             = UserInfo::findOne(['id' => $uid]);
        $logHandoverDefault   = LogHandoverDefault::find()->where(['type' => LogHandoverDefault::TYPE_COMMENT,'section' => $userInfo ->section,'status'=> LogHandoverDefault::STATUS_OPEN])->one();
        if (!$logHandoverDefault) {
            return $this->json(100, '暂无默认备注交接内容!');
        }
        $post                   = [];
        $brushStr               = $this->brushStr($logHandoverDefault->title);
        $post['content']        = $logHandoverDefault->title;
        $post['is_sensitive_words'] = $brushStr['is_sensitive_words'];
        $post['status']         = 2;
        $post['type']           = 3;
        $post['type_name']      = LogHandover::TYPE_COMMENT;
        $post['user_id']        = $uid;
        $post['chinese_name']   = $userInfo ->chinese_name;
        $post['pid']            = $userInfo ->pid;
        $post['shift']          = $userInfo ->shift;
        $post['section']        = $userInfo ->section;
        $post['comment_id']     = $logHandoverDefault->comment_id;
        $logHandoverCommentType = LogHandoverCommentType::findOne(['id' => $logHandoverDefault->comment_id]);
        $post['comment_title']  = $logHandoverCommentType->title;
        if($logHandoverCommentType->is_audit == 2){
            $post['content_audit']  = $brushStr['content'];
            $post['is_audit']       = 1;
        }
        $model = new LogHandover();
        $res   = $model->create($post);
        if ($res['status'] != 200) {
            return $this->json(100, '添加默认备注交接内容失败!');
        }
       
        return $this->json(200, '添加默认备注交接内容成功!');
    }

    /**
     * 更新备注
     * @return string
     */
    public function actionCommentUpdate()
    {
        if (Yii::$app->request->isPost) {
            $post  = Yii::$app->request->post();
            $model                 = LogHandover::findOne($post['id']);
            $brushStr              = $this->brushStr(trim($post['content']));
            $model->content        = $post['content'];
            $model->is_sensitive_words= $brushStr['is_sensitive_words'];
            $model->status         = $post['status'];
            $post['comment_id']    = intval($post['comment_id']);
            $logHandoverCommentType= LogHandoverCommentType::findOne(['id' => $post['comment_id']]);
            $model->comment_id     = $post['comment_id'];
            $model->comment_title  = $logHandoverCommentType->title;
            $model->update_time    = date('Y-m-d H:i:s', time());
            //同步更新Audit
            if($logHandoverCommentType->is_audit == 2){
                $model->content_audit  = $brushStr['content'];
                $model->is_audit       = 1;
            }else{
                $model->content_audit  = '';
                $model->is_audit       = 0;   
            }
            if (!$model->save(false)) {
                return $this->json(100, '更新失败!');
            }

            return $this->json(200, '更新成功!');
        }
        
        $logHandoverArr = $this->selectLogHandoverArr();
        $id    = Yii::$app->request->get('id');
        $model = LogHandover::find()->where(['id' => $id])->asArray()->one();
        return $this->render('comment-update',[
            'model' => $model,
            'logHandoverCommentTypeArr' => $logHandoverArr['logHandoverCommentTypeArr']
        ]); 
    }

    /**
     * 补充信息
     * @return string
     */
    public function actionAdditionalInformation()
    {
        $isPost = Yii::$app->request->isPost;
        if (!$isPost) {
            $id    = Yii::$app->request->get('id');
            $model = LogHandover::find()->where(['id' => $id])->asArray()->one();
            return $this->render('additional-information',[
                'model' => $model
            ]);     
        }

        $post  = Yii::$app->request->post();
        $uid                    = $this->getUid();
        $userInfo               = UserInfo::findOne(['id' => $uid]);
        $logHandoverModelNew    = $logHandoverModel= LogHandover::findOne($post['id']);
       
        if($logHandoverModel->handover_id > 0){
            $handoverId = $logHandoverModel->handover_id;
        }else{
            $handoverId = $logHandoverModel->id;
        }
        $logHandoverModel->status   = $post['status'];
        $logHandoverModel->copulate = LogHandover::COPULATE_ADDITIONAL;//已经补充完信息了
        if (!$logHandoverModel->save(false)) {
            return $this->json(100, '补充信息失败!');
        }
      
        //新增一条交接日志
        $model                         = new LogHandover();
        $model->type                   = $logHandoverModelNew->type;
        $model->type_name              = $logHandoverModelNew->type_name;
        $brushStr                      = $this->brushStr(trim($post['content']));
        $model->content                = trim($post['content']);
        $model->is_sensitive_words     = $brushStr['is_sensitive_words'];
        $model->user_id                = $userInfo->id;
        $model->chinese_name           = $userInfo->chinese_name;
        $model->pid                    = $userInfo->pid;
        $model->shift                  = $userInfo->shift;       
        $model->section                = $userInfo->section;
        $model->status                 = $post['status'];
        $model->copulate               = LogHandover::COPULATE_WAIT;
        $model->copulate_user_id       = 0;
        $model->copulate_chinese_name  = '';
        $model->copulate_pid           = '';
        $model->copulate_shift         = 0;
        $model->copulate_section       = 0;
        $model->copulate_time          = '';
        $model->type_id                = $logHandoverModelNew->type_id;
        $model->type_title             = $logHandoverModelNew->type_title;
        $model->batch_id               = $logHandoverModelNew->batch_id;
        $model->batch_title            = $logHandoverModelNew->batch_title;
        $model->comment_id             = $logHandoverModelNew->comment_id;
        $model->comment_title          = $logHandoverModelNew->comment_title;
        $model->device_type_id         = $logHandoverModelNew->device_type_id;
        $model->device_type_title      = $logHandoverModelNew->device_type_title;
        $model->date                   = '';
        $model->create_time            = date('Y-m-d H:i:s', time());
        $model->update_time            = date('Y-m-d H:i:s', time());
        $model->is_del                 = 0;
        $model->del_time               = '';
        $model->content_audit          = $brushStr['content'];
        $model->is_audit               = $logHandoverModelNew->is_audit;
        $model->is_del_audit           = 0;
        $model->del_time_audit         = '';
        $model->classes                = 0;
        if($post['status'] == LogHandover::STATUS_OPEN){
            $model->handover_id = $handoverId;
        }else{
            $model->handover_id = 0;
        }
        if (!$model->save(false)) {
            return $this->arrData(100, '补充信息失败!!');
        }
       
        return $this->json(200, '补充信息成功!');
    }

}
