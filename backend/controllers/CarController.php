<?php
namespace backend\controllers;

use Yii;
use yii\db\Exception;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use backend\models\Car;
use backend\models\CarPkgCode;
use backend\models\Role;
use backend\models\UserInfo;
use backend\models\UserStation;
use backend\models\Team;
use backend\models\CarOperator;
use backend\actions\UploadAction;
use backend\behaviors\Upload;
use yii\helpers\Json;
use backend\models\search\CarSearch;
use backend\models\CarOperatorJudge;
use backend\models\CarDefect;
use backend\models\CarJudge;
use backend\models\CarHighLevel;
use backend\models\CarUser;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;


class CarController extends BaseController{

    public function actions(){
        return [
            //文件上传
            'upload' => [
                'class' => UploadAction::className(),
                'type'  => Upload::IMAGE_TYPE,
            ]
        ];
    }

    /**
     * 大小材料人员判断
     * 1为小材料 2为大材料 0 未区分
     */
    public function getUserSection()
    {
        $type          = 0;
        $uid           = $this->getUid();
        $userInfoModel = UserInfo::findOne($uid);
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
     * @return string
     */
    public function actionIndex(){

        $request      = Yii::$app->request->queryParams;
        $searchModel  = new CarSearch();
        $request['type'] = $this->getUserSection();
        $data         = $searchModel->search($request);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        $check        = 0;
        $uid          = $this->getUid();
        $carUserModel = CarUser::findOne(['user_id' => $uid]);
        if ($carUserModel) {
            $check = 1;
        }
        $userInfo     = Yii::$app->user->identity->userInfo ; 
        return $this->render('index', [
            'check'        => $check,
            'userInfo'     => $userInfo,
            'request'      => $request,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination
        ]);
    }

    public function actionView(){
        $uid           = $this->getUid();
        $userInfo      = UserInfo::findOne($uid);
        $id            = Yii::$app->request->get('id');
        $carModel      = Car::findOne($id);
        $carModel->setImagesPath();
        $carModel->setSecondImagesPath();
        $carJudgeModel = CarJudge::find()->where(['car_id' => $id])->asArray()->all();
        $dafectArr     = array_column(CarDefect::find()->where(['status' => CarDefect::STATUS_ON])->asArray()->all(),'defect','id');
      
        return $this->render('view', [
            'userInfo'      => $userInfo,
            'carModel'      => $carModel,
            'dafectArr'     => $dafectArr,
            'carJudgeModel' => $carJudgeModel
        ]);
    }

    public function actionHistory(){
       
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new CarSearch();
        $request['type']   = $this->getUserSection();
        $data         = $searchModel->searchHistory($request);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
     
        $userInfo     = Yii::$app->user->identity->userInfo ; 
        return $this->render('history', [
            'userInfo'     => $userInfo,
            'request'      => $request,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination
        ]);
    }

    /**
     * 导出数据
     * @return string
     */
    public function actionExport()
    {
        $request = Yii::$app->request->queryParams;
        $query = Car::find();
        $type = $this->getUserSection();
        $query->where(['type' => $type]);
        $query->andFilterWhere(['like', 'trace_no', trim($request['trace_no'])]);
        $query->andFilterWhere(['shift' => $request['shift']]);
        $query->andFilterWhere(['like', 'machine_number', trim($request['machine_number'])]);
        $query->andWhere(['approval_status' => Car::APPROVAL_STATUS_PASS]);
        $startTime = ArrayHelper::getValue($request, 'start_time', '');
        $endTime   = ArrayHelper::getValue($request, 'end_time', '');
        $query->andFilterWhere(['>=', 'create_time', $startTime]);
        $query->andFilterWhere(['<=', 'create_time', $endTime]);
        $query->orderBy(['id' => SORT_DESC]);
        $query->asArray();
        $dataProvider = $query->all();
        $dafectArr     = array_column(CarDefect::find()->where(['status' => CarDefect::STATUS_ON])->asArray()->all(),'defect','id');
        $data = [];
        if (!empty($dataProvider)) {
            foreach ($dataProvider as $key => $value) {
                $data[$key][] = $value['station'];
                $data[$key][] = $value['date'];
                $data[$key][] = ArrayHelper::getValue(UserInfo::$shiftArr, $value['shift'],'');
                $data[$key][] = $value['device_model'];
                $data[$key][] = $value['batch_number'];
                $data[$key][] = $value['package'];
                $data[$key][] = $value['machine_number'];
                $data[$key][] = $value['operator'];
                $data[$key][] = (new UserInfo())->getUserName($value['create_id']);
                $data[$key][] = ArrayHelper::getValue($dafectArr, $value['defective_items'], '');
                $data[$key][] = $this->getPointValue($value['responsibility'],$value['defective_items']);
                $data[$key][] = $value['inspector'];
                $data[$key][] = $value['description'];
                $data[$key][] = date('Y-m-d H:i:s', strtotime($value['create_time']));
                $data[$key][] = ArrayHelper::getValue(Car::$statusArr, $value['status'],'');
                $data[$key][] = ArrayHelper::getValue(CarJudge::$resultArr, $value['result'], '');
                $data[$key][] = ArrayHelper::getValue(CarJudge::$responsibilityArr, $value['responsibility'], '');
                $data[$key][] = ArrayHelper::getValue(CarJudge::$dprArr, $value['dpr'], '');
                $data[$key][] = $value['trace_no'];
            }
        }
       
        $fileName = 'DPR已完成列表_' . time() . '.xlsx';
        $header   = ['操作站','日期','班别','器件型号','批号','封装类型','机器号','操作员','创建人','缺陷项目','分值','检查员','描述','创建时间','状态','判定结果','是否记操作员责任','DPR权重','TRACE NO'];
        array_unshift($data, $header); //添加头部标题
        return $this->json(200, $fileName, $data);
    }
    public function getPointValue($responsibility,$defectiveitems)
    {
        if($responsibility == 2) return 0.2;
        $weightArr   = array_column(CarDefect::find()->where(['status' => CarDefect::STATUS_ON])->asArray()->all(),'weight','id');
        $weight      = ArrayHelper::getValue($weightArr, $defectiveitems, 0);
        if($weight > 0){
            return  round($weight/2, 2);
        }
        return 0;
        
    }

    /**
     * 导出数据
     * @return string
     */
    public function actionExportindex()
    {
        $request = Yii::$app->request->queryParams;
        $query = Car::find();
        $type = $this->getUserSection();
        $query->where(['type' => $type]);
        $query->andFilterWhere(['like', 'trace_no', trim($request['trace_no'])]);
        $query->andFilterWhere(['shift' => $request['shift']]);
        $query->andFilterWhere(['like', 'machine_number', trim($request['machine_number'])]);
        $query->andWhere(['approval_status' => Car::APPROVAL_STATUS_GOING]);
        $startTime = ArrayHelper::getValue($request, 'start_time', '');
        $endTime   = ArrayHelper::getValue($request, 'end_time', '');
        $query->andFilterWhere(['>=', 'create_time', $startTime]);
        $query->andFilterWhere(['<=', 'create_time', $endTime]);
        $query->orderBy(['id' => SORT_DESC]);
        $query->asArray();
        $dataProvider = $query->all();
        $dafectArr     = array_column(CarDefect::find()->where(['status' => CarDefect::STATUS_ON])->asArray()->all(),'defect','id');
        $data = [];
        if (!empty($dataProvider)) {
            foreach ($dataProvider as $key => $value) {
                $data[$key][] = $value['station'];
                $data[$key][] = $value['date'];
                $data[$key][] = ArrayHelper::getValue(UserInfo::$shiftArr, $value['shift'],'');
                $data[$key][] = $value['device_model'];
                $data[$key][] = $value['batch_number'];
                $data[$key][] = $value['package'];
                $data[$key][] = $value['machine_number'];
                $data[$key][] = $value['operator'];
                $data[$key][] = (new UserInfo())->getUserName($value['create_id']);
                $data[$key][] = ArrayHelper::getValue($dafectArr, $value['defective_items'], '');
                $data[$key][] = $value['inspector'];
                $data[$key][] = $value['description'];
                $data[$key][] = date('Y-m-d H:i:s', strtotime($value['create_time']));
                $data[$key][] = ArrayHelper::getValue(Car::$statusArr, $value['status'],'');
                $data[$key][] = ArrayHelper::getValue(CarJudge::$resultArr, $value['result'], '');
                $data[$key][] = ArrayHelper::getValue(CarJudge::$responsibilityArr, $value['responsibility'], '');
                $data[$key][] = ArrayHelper::getValue(CarJudge::$dprArr, $value['dpr'], '');
                $data[$key][] = $value['trace_no'];
            }
        }
       
        $fileName = 'DPR未完成列表_' . time() . '.xlsx';
        $header   = ['操作站','日期','班别','器件型号','批号','封装类型','机器号','操作员','创建人','缺陷项目','检查员','描述','创建时间','第一步状态','判定结果','是否记操作员责任','DPR权重','TRACE NO'];
        array_unshift($data, $header); //添加头部标题
        return $this->json(200, $fileName, $data);
    }

    /**
     * 上传文件
     * @return string
     */
    public function actionUpload()
    {
        $result = $this->upload('/car/');
        return Json::encode($result);
    }

    /**
     * 创建
     * 
     * @return string
     */
    public function actionCreate(){
        $type        = $this->getUserSection();
        $uid         = $this->getUid();
      
        $userInfo    = UserInfo::findOne($uid);
        if (Yii::$app->request->isPost) {
            $post  = Yii::$app->request->post();
            if(isset($post['files_mapping']) && is_array($post['files_mapping'])){
                $post['files_mapping'] = json_encode($post['files_mapping']);
            }elseif(!isset($post['files_mapping'])){
                $post['files_mapping'] = '';
            }
            // $checkBatchNumber  = Car::find()->where(['batch_number' => $post['batch_number']])->one();
            // if($checkBatchNumber){
            //    $status = $this->sendBatchNumber($post);
            //    $msg = $status['status'] == true ? '系统检测该批号已出现过已将此次提交<br/>邮件上报并将此批号物料HOLD住' : $status['message'];
            //    return $this->json(400, $msg);
            // }
            $post['type']      = $type;
            $typeHead = '';
            if($type ==1) $typeHead = 'NLD';
            if($type ==2) $typeHead = 'LD';
            $traceNoNumber     = Car::find()->max('trace_no_number');
            $post['trace_no_number']  = intval($traceNoNumber) + 1;
            $post['trace_no']  = $typeHead . str_pad( $post['trace_no_number'], 7, "0", STR_PAD_LEFT);
            $post['shift']     = $userInfo->shift;
            $post['create_id'] = $uid;
            $post['inspector'] = $userInfo->chinese_name;
            $res   = (new Car())->create($post);
            if ($res['status'] != 200) {
                return $this->json(100, $res['msg']);
            }
            $this->carSendEmail($res['msg']);
            return $this->json(200, '添加成功!');
        }
        $dafectArr   = array_column(CarDefect::find()->where(['status' => CarDefect::STATUS_ON,'type' => $type])->asArray()->all(),'defect','id');
  
        return $this->render('create', [
            'userInfo'    => $userInfo,
            'dafectArr'   => $dafectArr,
        ]);
    }



   /**
   * 产品工程师和带班判定
   */
    public function actionOperation(){
        if (Yii::$app->request->isPost) {
            $uid          = $this->getUid();
            $userInfo     = UserInfo::findOne($uid);
            $post         = Yii::$app->request->post();
            $transaction  = Yii::$app->db->beginTransaction();
            //更新当前状态
            $carModel     = Car::findOne($post['car_id']);
            if(isset($post['result']) && $post['result'] == CarJudge::RESULT_PASS){
                $carModel->status = Car::STSTUS_CONFIRMATION_DUTY;//带班二次确认
                $carModel->result = $post['result'];
                $carModel->responsibility = $post['responsibility'];
                $carModel->dpr            = $post['dpr'];
                //第二步操作
                if($post['responsibility'] == CarJudge::RECORD_RESPONSIBILITY && $carModel->second_status == 0){
                    $carModel->second_status = Car::STSTUS_SECOND_OPERATOR;
                }
               
                $this->carSendEmailClass($carModel->id);
            }elseif($carModel->status == Car::STSTUS_FEEDBACK_CLASS){
                //检查带班审批的各个状态
                // if($carModel->number >= 3){
                //     //如果超过三次驳回审批就必须进入到带班二次确认状态
                //     $carModel->status = Car::STSTUS_CONFIRMATION_DUTY;//带班二次确认
                // }else{
                    //带班确认完以后下次是产品工程师审批
                    $carModel->status = Car::STSTUS_PRODUCT_ENGINEER;
                
                // }
                $this->carSendEmailEngineer($carModel->id);
            }elseif($carModel->status == Car::STSTUS_PRODUCT_ENGINEER){
                $carModel->status         = Car::STSTUS_FEEDBACK_CLASS;
                $carModel->number         += 1;  //统计产品工程师审批次数 最多3次
                $carModel->result         = $post['result'];
                $carModel->responsibility = $post['responsibility'];
                $carModel->dpr            = $post['dpr'];
                //第二步操作
                if($post['responsibility'] == CarJudge::RECORD_RESPONSIBILITY && $carModel->second_status == 0){
                    $carModel->second_status = Car::STSTUS_SECOND_OPERATOR;
                }
                $this->carSendEmailClass($carModel->id);
            }elseif($carModel->status == Car::STSTUS_CONFIRMATION_DUTY){
                //带班二次确认完成就算结束了
                $carModel->status = Car::STSTUS_SUCCESS;
                if($carModel->second_status == 0){
                    $carModel->approval_status = Car::APPROVAL_STATUS_PASS;
                }
                $carModel->first_success_time = date('Y-m-d H:i:s', time());
                if (!$carModel->save(false)) {
                    $transaction->rollBack();
                    return $this->json(100, '保存失败!');
                }
                $transaction->commit();
                return $this->json(200, '保存成功!');
            }
            if (!$carModel->save(false)) {
                $transaction->rollBack();
                return $this->json(100, '保存失败!');
            }

            //保存判定结果
            $model                 = new CarJudge();
            $model->car_id         = $post['car_id'];
            $model->role           = $userInfo->position;
            $model->method         = $post['method'];
            $model->result         = isset($post['result']) ? $post['result'] : 0 ;
            $model->responsibility = isset($post['responsibility']) ? $post['responsibility'] : 0 ;
            $model->dpr            = isset($post['dpr']) ? $post['dpr'] : 0 ;
            $model->user_id        = $uid;
            $model->create_time    = date('Y-m-d H:i:s', time());
            if (!$model->save(false)) {
                $transaction->rollBack();
                return $this->json(100, '保存失败!');
            }
            $transaction->commit();
            return $this->json(200, '保存成功!');
        }
        $id            = Yii::$app->request->get('id');
        $car           = Car::findOne($id);
        $car->setImagesPath();
        $car->setSecondImagesPath();
        $carJudgeModel = CarJudge::find()->where(['car_id' => $id])->asArray()->all();
        $dafectArr     = array_column(CarDefect::find()->where(['status' => CarDefect::STATUS_ON])->asArray()->all(),'defect','id');
        return $this->render('operation', [
            'car'           => $car,
            'dafectArr'     => $dafectArr,
            'carJudgeModel' => $carJudgeModel
        ]);
    }

    public function carSendEmailTest(){
        $id = 51;
        $data                 = Car::find()->where(['id' => $id])->asArray()->one();
        $pkgCode              = CarPkgCode::findOne(['case_outline' => $data['package']]);
        $data['pkg_type']     =  $pkgCode ?  $pkgCode->pkg_type : '';
        $data['type_user_id'] =  $pkgCode ?  $pkgCode->type_user_id : '';
        $data['files_mapping_arr'] = json_decode($data['files_mapping'],1);
        $obligation           = UserInfo::findOne(['wbi_code' =>$data['operator']]);
        $userInfo             = UserInfo::findOne($data['type_user_id']);
        $type                 = $this->getUserSection();
        $data['dafectArr']    = array_column(CarDefect::find()->where(['status' => CarDefect::STATUS_ON,'type' => $type])->asArray()->all(),'defect','id');

        if($userInfo && !empty($userInfo['email'])){
            $to      = '894698693@qq.com';
            $createUserInfo = UserInfo::findOne($data['create_id']);
           // $cc = UserInfo::find()->select('email')->where(['section' => $createUserInfo->section,'status' => UserInfo::STATUS_OPEN])->andWhere(['position' => UserInfo::POSITION_PRODUCT])->andWhere(['!=','id',$data['type_user_id']])->column();
            $data['chinese_name']   = $obligation ? $obligation->chinese_name :'';
            $data['shift']          = $obligation ? $obligation->shift :'';
            $subject = 'E-DPR 邮件审批';
            $html =  $this->renderFile('@common/mail/car/car-email-view.php',['data' => $data]);
            $mail = Yii::$app->mailer->compose( );
            $mail->setHtmlBody($html);  //邮件正文
            $mail->setSubject($subject); //邮件主题
            $mail->setTo($to);//收件人
         //   $mail->setCc($cc); //抄送的人
            @$mail->send();
            
        }
        return '';
    }

    /**
     * 发送邮件
     * @param $id
     */
    public function carSendEmail($id = ''){
        $data                 = Car::find()->where(['id' => $id])->asArray()->one();
        $pkgCode              = CarPkgCode::findOne(['case_outline' => $data['package']]);
        $data['pkg_type']     =  $pkgCode ?  $pkgCode->pkg_type : '';
        $data['type_user_id'] =  $pkgCode ?  $pkgCode->type_user_id : '';
        $data['files_mapping_arr'] = json_decode($data['files_mapping'],1);
        $obligation           = UserInfo::findOne(['wbi_code' =>$data['operator']]);
        $userInfo             = UserInfo::findOne($data['type_user_id']);
        $type                 = $this->getUserSection();
        $data['dafectArr']    = array_column(CarDefect::find()->where(['status' => CarDefect::STATUS_ON,'type' => $type])->asArray()->all(),'defect','id');

        if($userInfo && !empty($userInfo['email'])){
            $to      = $userInfo['email'];
            $createUserInfo = UserInfo::findOne($data['create_id']);
            $cc = UserInfo::find()->select('email')->where(['section' => $createUserInfo->section,'status' => UserInfo::STATUS_OPEN])->andWhere(['position' => UserInfo::POSITION_PRODUCT])->andWhere(['!=','id',$data['type_user_id']])->column();
            $data['chinese_name']   = $obligation ? $obligation->chinese_name :'';
            $data['shift']          = $obligation ? $obligation->shift :'';
            $subject = 'E-DPR 邮件审批';
            $html =  $this->renderFile('@common/mail/car/car-email-view.php',['data' => $data]);
            $mail = Yii::$app->mailer->compose( );
            $mail->setHtmlBody($html);  //邮件正文
            $mail->setSubject($subject); //邮件主题
            $mail->setTo($to);//收件人
            $mail->setCc($cc); //抄送的人
            @$mail->send();
            
        }
        return '';
    }

     /**
     * 给带班发送邮件
     * @param $id
     */
    public function carSendEmailClass($id = ''){
      
        $data                 = Car::find()->where(['id' => $id])->asArray()->one();
        $pkgCode              = CarPkgCode::findOne(['case_outline' => $data['package']]);
        $data['pkg_type']     =  $pkgCode ?  $pkgCode->pkg_type : '';
        $data['type_user_id'] =  $pkgCode ?  $pkgCode->type_user_id : '';
        $data['files_mapping_arr'] = json_decode($data['files_mapping'],1);
        $userInfo           = UserInfo::findOne(['wbi_code' =>$data['operator']]);
        if(!$userInfo){
            return '';
        }
        $data['station_id'] = $userInfo ?  $userInfo->station_id : '';
        $leaderModel        = UserInfo::findOne(['id' => $userInfo->leader_uid]);
        $dafectArr          = array_column(CarDefect::find()->where(['status' => CarDefect::STATUS_ON])->asArray()->all(),'defect','id');
        $carJudge = CarJudge::find()->where(['car_id' => $id])
        ->andWhere(['role' => UserInfo::POSITION_PRODUCT])
        ->orderBy(['id' => SORT_DESC])
        ->limit(1)
        ->asArray()
        ->one();
        $subject = 'E-DPR-带班按需处理物料-'.$data['batch_number'];
        if($leaderModel && !empty($leaderModel->email)){
            $to      = $leaderModel->email;
            $cc = UserInfo::find()->select('email')->where(['section' => $userInfo->section,'status' => UserInfo::STATUS_OPEN])->andWhere(['position' => UserInfo::POSITION_TAKE])->andWhere(['!=','id',$leaderModel->id])->column();
 
            $html =  $this->renderFile('@common/mail/car/car-email-class.php',['data' => $data,'dafectArr' => $dafectArr,'carJudge' => $carJudge]);
            $mail = Yii::$app->mailer->compose( );
            $mail->setHtmlBody($html);  //邮件正文
            $mail->setSubject($subject); //邮件主题
            $mail->setTo($to);//收件人
            $mail->setCc($cc); //抄送的人
            @$mail->send();
        }
        return '';
    }

     /**
     * 给产品工程师发送邮件
     * @param $id
     */
    public function carSendEmailEngineer($id = ''){
     
        $data                 = Car::find()->where(['id' => $id])->asArray()->one();
        $pkgCode              = CarPkgCode::findOne(['case_outline' => $data['package']]);
        $data['pkg_type']     =  $pkgCode ?  $pkgCode->pkg_type : '';
        $data['type_user_id'] =  $pkgCode ?  $pkgCode->type_user_id : '';
        $data['files_mapping_arr'] = json_decode($data['files_mapping'],1);
        $userInfo           = UserInfo::findOne(['wbi_code' =>$data['operator']]);
        $data['station_id'] =  $userInfo ?  $userInfo->station_id : '';
    
        $dafectArr     = array_column(CarDefect::find()->where(['status' => CarDefect::STATUS_ON])->asArray()->all(),'defect','id');
       
        $subject = 'E-DPR-工程师确认-'.$data['batch_number'];
        $carJudge = CarJudge::find()->where(['car_id' => $id])
        ->andWhere(['role' => UserInfo::POSITION_TAKE])
        ->orderBy(['id' => SORT_DESC])
        ->limit(1)
        ->asArray()
        ->one();
      
        $engineerModel    = UserInfo::findOne($data['type_user_id']);
      
        if($engineerModel && !empty($engineerModel->email)){
            $to = $engineerModel->email;
            if(!$userInfo){
                $cc = '';
            }else{
                $cc = UserInfo::find()->select('email')->where(['section' => $userInfo->section,'status' => UserInfo::STATUS_OPEN])->andWhere(['position' => UserInfo::POSITION_PRODUCT])->andWhere(['!=','id',$data['type_user_id']])->column();
            }
            $html =  $this->renderFile('@common/mail/car/car-email-engineer.php',['data' => $data,'dafectArr' => $dafectArr,'carJudge' => $carJudge]);
            $mail = Yii::$app->mailer->compose( );
            $mail->setHtmlBody($html);  //邮件正文
            $mail->setSubject($subject); //邮件主题
            $mail->setTo($to);//收件人
            $mail->setCc($cc); //抄送的人
            @$mail->send();
        }

        return '';
    }

    
    /**
     * 操作员填写
     * @return string
     */
    public function actionOperator(){

        $uid = $this->getUid();
        if (Yii::$app->request->isPost) {
            $post         = Yii::$app->request->post();
            if(isset($post['second_files_mapping']) && is_array($post['second_files_mapping'])){
                $post['second_files_mapping'] = json_encode($post['second_files_mapping']);
            }elseif(!isset($post['second_files_mapping'])){
                $post['second_files_mapping'] = '';
            }
            $transaction  = Yii::$app->db->beginTransaction();
            //更新当前状态
            $carModel                       = Car::findOne($post['car_id']);
            $carModel->second_status        = Car::STSTUS_SECOND_TAKE;
            $carModel->reasons_defects      = $post['reasons_defects'];
            $carModel->corrective_action    = $post['corrective_action'];
            $carModel->second_files_mapping = $post['second_files_mapping'];
            if (!$carModel->save(false)) {
                $transaction->rollBack();
                return $this->json(100, '保存失败!');
            }
            
            //保存判定结果
            $model                    = new CarOperator();
            $model->car_id            = $post['car_id'];
            $model->trace_no          = $carModel->trace_no;
            $model->reasons_defects   = $post['reasons_defects'];
            $model->corrective_action = $post['corrective_action'];
            $model->second_files_mapping = $post['second_files_mapping'];
            $model->user_id           = $uid;
            $model->date              = date('Y-m-d', time());
            $model->create_time       = date('Y-m-d H:i:s', time());
            if (!$model->save(false)) {
                $transaction->rollBack();
                return $this->json(100, '保存失败!');
            }
            $transaction->commit();
            return $this->json(200, '保存成功!');
        }
        $id       = Yii::$app->request->get('id');
        $userInfo = UserInfo::findOne($uid);
        $carModel = Car::findOne($id);
        $carModel->setSecondImagesPath();
        $carOperatorJudgeTake = CarOperatorJudge::find()->where(['car_id'=>$id,'role'=>2,'result' => 2])->orderBy(['id' => SORT_DESC])->limit(1)->asArray()->one();
        $carOperatorJudgeHighlevel = CarOperatorJudge::find()->where(['car_id'=>$id,'role'=>3,'result' => 2])->orderBy(['id' => SORT_DESC])->limit(1)->asArray()->one();
       
        return $this->render('operator', [
            'userInfo' => $userInfo,
            'carModel' => $carModel,
            'carOperatorJudgeTake' => $carOperatorJudgeTake,
            'carOperatorJudgeHighlevel' => $carOperatorJudgeHighlevel,
        ]);
    }

    /**
     * 带班填写
     */
    public function actionTake(){
        $uid = $this->getUid();
        if (Yii::$app->request->isPost) {
            $post         = Yii::$app->request->post();
            $transaction  = Yii::$app->db->beginTransaction();
            //更新当前状态
            $carModel     = Car::findOne($post['car_id']);
            if($post['result'] == 1 ){
                $carModel->second_status = Car::STSTUS_SECOND_HIGH_LEVEL;
            }else{
                $carModel->second_status = Car::STSTUS_SECOND_OPERATOR;
            }
            if (!$carModel->save(false)) {
                $transaction->rollBack();
                return $this->json(100, '保存失败!');
            }
           
            //保存判定结果
            $model                 = new CarOperatorJudge();
            $model->car_id         = $post['car_id'];
            $model->role           = 2; //带班
            $model->result         = $post['result'];
            $model->user_id        = $uid;
            $model->reasons_dismissal = $post['reasons_dismissal'];
            $model->create_time    = date('Y-m-d H:i:s', time());
            if (!$model->save(false)) {
                $transaction->rollBack();
                return $this->json(100, '保存失败!');
            }
            $transaction->commit();
            return $this->json(200, '保存成功!');
        }
        $userInfo    = UserInfo::findOne($uid);
        $id          = Yii::$app->request->get('id');
        $carModel    = Car::findOne($id);
        $carModel->setSecondImagesPath();

        return $this->render('take', [
            'userInfo' => $userInfo,
            'carModel' => $carModel
        ]);
    }

    /**
     * 
     * @return string
     */
    public function actionJudgeHighLevel(){
        $uid = $this->getUid();
        if (Yii::$app->request->isPost) {
            $post         = Yii::$app->request->post();
            $transaction  = Yii::$app->db->beginTransaction();
            //更新当前状态
            $carModel     = Car::findOne($post['car_id']);
            if($post['result'] == 1 ){
                $carModel->second_status = Car::STSTUS_SECOND_SUCCESS;
                $carModel->approval_status = Car::APPROVAL_STATUS_PASS;
                $carModel->second_success_time = date('Y-m-d H:i:s', time());
            }else{
                $carModel->second_status = Car::STSTUS_SECOND_OPERATOR;
            }
            if (!$carModel->save(false)) {
                $transaction->rollBack();
                return $this->json(100, '保存失败!');
            }
            //保存判定结果
            $model                 = new CarOperatorJudge();
            $model->car_id         = $post['car_id'];
            $model->role           = 3; 
            $model->result         = $post['result'];
            $model->user_id        = $uid;
            $model->reasons_dismissal = $post['reasons_dismissal'];
            $model->create_time    = date('Y-m-d H:i:s', time());
            if (!$model->save(false)) {
                $transaction->rollBack();
                return $this->json(100, '保存失败!');
            }
            $transaction->commit();
            return $this->json(200, '保存成功!');
        }

        $userInfo      = UserInfo::findOne($uid);
        $id            = Yii::$app->request->get('id');
        $carModel      = Car::findOne($id);
        $carModel->setImagesPath();
        $carModel->setSecondImagesPath();
        $carJudgeModel = CarJudge::find()->where(['car_id' => $id])->asArray()->all();
        $dafectArr     = array_column(CarDefect::find()->where(['status' => CarDefect::STATUS_ON])->asArray()->all(),'defect','id');
    
        return $this->render('judge-high-level', [
            'userInfo'      => $userInfo,
            'carModel'      => $carModel,
            'dafectArr'     => $dafectArr,
            'carJudgeModel' => $carJudgeModel
        ]);
    }

    /**
     * 列表
    */
    public function actionHighLevel()
    {
        $searchModel = new CarHighLevel();
        $params = Yii::$app->request->queryParams;
        $params['type'] = $this->getUserSection();
        $dataProvider = $searchModel->search($params);
        return $this->render('high-level', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 添加
     */
    public function actionHighLevelCreate()
    {
        $model = new CarHighLevel();
        if (Yii::$app->request->isPost) {
            $post  = Yii::$app->request->post();
         
            if ($model->load($post) && $model->save(false)) {
                return $this->redirect( 'high-level' );
            }
        }

        return $this->render('high-level-create', [
            'model' => $model,
        ]);
    }

    /**
     * 更新
     */
    public function actionHighLevelUpdate($id)
    {
        $model = CarHighLevel::findOne($id);
        $post  = Yii::$app->request->post();
        if ($model->load($post) && $model->save()) {
            return $this->redirect(['high-level', 'id' => $model->id]);
        }
        return $this->render('high-level-update', [
            'model' => $model,
        ]);
    }

     /**
     * 列表
    */
    public function actionPkgcode()
    {
        $searchModel = new CarPkgCode();
        $params = Yii::$app->request->queryParams;
        $params['type'] = $this->getUserSection();
        $data         = $searchModel->search($params);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        return $this->render('pkgcode', [
          
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination
        ]);
    }

    /**
     * 添加
     */
    public function actionPkgcodeCreate()
    {
        if (Yii::$app->request->isPost) {
            $post        = Yii::$app->request->post();
            $caseOutline = strtoupper($post['case_outline']);
            $check       = CarPkgCode::findOne(['case_outline' => $caseOutline]);
            if($check){
                return $this->json(100, '此CaseOutline已存在！');
            }
            $model                = new CarPkgCode();
            $model->case_outline  = $caseOutline;
            $model->pkg_code      = strtoupper($post['pkg_code']);
            $model->pkg_type      = strtoupper($post['pkg_type']);
            $model->type          = $post['type'];
            $model->type_user_id  = $post['CarPkgCode']['type_user_id'];
            if(!$model->save(false)){
                return $this->json(100, '添加失败！');
            }
            return $this->json(200, 'Success!');
        }
        $model = new CarPkgCode();
        return $this->render('pkgcode-create', [
            'model' => $model,
        ]);
    }
 
    /**
     * 更新
     */
    public function actionPkgcodeUpdate()
    {
        if (Yii::$app->request->isPost) {
            $post  = Yii::$app->request->post();
            $model = new CarPkgCode();
            $check = $model->check($post);
            if (!$check) {
                return $this->json(100, '此CaseOutline已存在！');
            }
            $post['type_user_id'] = $post['CarPkgCode']['type_user_id'];
            $res = $model->edit($post);
            if ($res['status'] != 200) {
                return $this->json(100, $res['msg']);
            }
            return $this->json(200, $res['msg']);
        }
        $id    = Yii::$app->request->get('id');
        $model = CarPkgCode::findOne($id);
        return $this->render('pkgcode-update', [
            'model' => $model,
        ]);
    }

    /**
     * 列表
     * @return mixed
     */
    public function actionDefect()
    {
        $searchModel = new CarDefect();
        $params = Yii::$app->request->queryParams;
        $params['type'] = $this->getUserSection();
        $dataProvider = $searchModel->search($params);
        return $this->render('defect', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     *添加
     */
    public function actionDefectCreate()
    {
        $model = new CarDefect();
        $post  = Yii::$app->request->post();
        if ($model->load($post) && $model->save()) {
            return $this->redirect( 'defect' );
        }

        return $this->render('defect-create', [
            'model' => $model,
        ]);
    }

    /**
     * 更新
     */
    public function actionDefectUpdate($id)
    {
        $model = CarDefect::findOne($id);
        $post  = Yii::$app->request->post();
        if ($model->load($post) && $model->save()) {
            return $this->redirect(['defect', 'id' => $model->id]);
        }

        return $this->render('defect-update', [
            'model' => $model,
        ]);
    }

    //调用接口获取对应信息

    public function actionGetParameters(){
        $post  = Yii::$app->request->post();
        if(empty($post['station'])){
            return $this->json(100, '请选择操作站!');  
        }
        if(empty($post['batch_number'])){
            return $this->json(100, '请填写批号!');  
        }
        $batchNumber = trim($post['batch_number']);
        $station     = $post['station'];

        $data = ['device_model' =>'','package' =>'','machine_number' =>'','operator' =>''];
        $getlegacyPartNumber = $this->getlegacyPartNumber(strtoupper($batchNumber));//获取器件号
        if(!$getlegacyPartNumber['status']){
            return $this->json(100, $getlegacyPartNumber['message']);
        }
        $data['device_model'] = $getlegacyPartNumber['data']['legacyPartNumber']; 
        $data['package']      = $getlegacyPartNumber['data']['packageOutlineName']; 
        
        $getRpsData = $this->getRpsData(strtoupper($batchNumber));
        if(!$getRpsData['status']){
            return $this->json(100, $getRpsData['message']);
        }
        
        if(is_array($getRpsData['data'])){
            foreach ($getRpsData['data'] as $value) {
                if ($value['SPECNAME'] == $station) {
                    $data['machine_number'] = $value['EQUIPMENTLIST'];
                    $data['operator']       = $value['MOVEOUTUSERNAME'];
                }
            }
        }
        if(empty($data['operator'])){
            return $this->json(100, '此操作站无操作员信息！');
        }
        $userInfo = UserInfo::findOne(['wbi_code' => $data['operator']]);
        if(!$userInfo){
            return $this->json(100, $data['operator'].'员工信息未找到，请联系工程师配置员工信息后提交!');
        }
        return $this->json(200, '获取成功！',$data);
    }

     //调用接口获取对应信息

     public function actionGetStation(){
        $post  = Yii::$app->request->post();
        if(empty($post['batch_number'])){
            return $this->json(100, '请填写批号!');  
        }
        $batchNumber = trim($post['batch_number']);

        $getRpsData = $this->getRpsData(strtoupper($batchNumber));
        if(!$getRpsData['status']){
            return $this->json(100, $getRpsData['message']);
        }
        $stationArr = [];
        if(is_array($getRpsData['data'])){
            $stationArr = array_unique(array_column($getRpsData['data'], 'SPECNAME'));
        }
        return $this->json(200, '获取成功！',$stationArr);
    }

    
    
    //Socket
    private $socket;


    /**
     * 通过Socket HOLD接口
     */
    public function sendBatchNumber($data)
    {
        $status = ['status' => false, 'message' => ''];
        error_reporting(E_ALL);
        set_time_limit(0);
        $param = [
            "subject" => "ATTJ.REQ.MES.SET_LOT_HOLD.*",
            "eqpName" => "",
            "appName" => "MFGBE1",
            "data"    => [
                "SetLotHoldRequest" => [
                    "appName"               => "MFGBE1",
                    "operator"              => 'srv_CMS4TFSB',
                    "lotId"                 => $data['batch_number'],
                    "holdReasonName"        => "AS03",
                    "holdLocationName"      => "",
                    "holdTypeName"          => "",
                    "holdTicket"            => "",
                    "hrtHoldKey"            => "",
                    "holdUsername"          => "",
                    "comment"               => "出现同一批号出现过",
                    "storageArea"           => "",
                    "storageCategory"       => "",
                    "storageLocation"       => "",
                    "expectedHoldDays"      => "",
                    "shelfLocation"         => "",
                    "applyToScheduleLots"   => "false",
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
                    if ($result['SetLotHoldResponse']['errorCode'] == 0) {
                        $status['status']  = true;
                        $status['message'] = 'Success';
                        $this->close();
                        return $status;
                    } else {
                        $status['message'] = $result['SetLotHoldResponse']['errorText'];
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
     * 通过Socket 获取器件号
     */
    public function getlegacyPartNumber($batchNumber = '')
    {
        $status = ['status' => false, 'message' => '', 'data' => []];
        if ($batchNumber == '') return $status;
        error_reporting(E_ALL);
        set_time_limit(0);
        $param = [
            "subject" => "ATTJ.REQ.MES.LOT_INFO.*",
            "eqpName" => "",
            "appName" => "MFGBE1",
            "data"    => [
                "MESLotInfoRequest" => [
                    "appName"          => "MFGBE1",
                    "attributes"       => ["packageOutlineName","legacyPartNumber","productName"],
                    "onlineQuerySetup" => "EI_IsMaterialInfoExist",
                    "eqpName"          => "",
                    "lotId"            => strtoupper($batchNumber),
                    "wipFlagSelection" => "NA"
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
                    $outresult = Json::decode($out);
                    if ($outresult['MESLotInfoResponse']['errorCode'] == 0) {
                        $status['status']  = true;
                        $status['message'] = 'Success';
                        $status['data']    = [
                            'packageOutlineName'    => $outresult['MESLotInfoResponse']['lotData'][0]['packageOutlineName'],
                            'waferScribeNumberList' => $outresult['MESLotInfoResponse']['lotData'][0]['waferScribeNumberList'],
                            'lotId'                 => $outresult['MESLotInfoResponse']['lotData'][0]['lotId'],
                            'legacyPartNumber'      => $outresult['MESLotInfoResponse']['lotData'][0]['legacyPartNumber'],
                            'productName'           => $outresult['MESLotInfoResponse']['lotData'][0]['productName'],
                        ];
                        $this->close();
                        return $status;
                    } else {
                        $status['message'] = $outresult['MESLotInfoResponse']['errorText'];
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
     * 通过Socket 获取机器号和做料人
     */
    public function getRpsData($batchNumber = '')
    {
        $status = ['status' => true, 'message' => 'Success', 'data' => []];
        if (empty($batchNumber)) return $status;
        error_reporting(E_ALL);
        set_time_limit(0);
    
        $param = [
            "subject" => "ATTJ.REQ.MES.ONLINE_QUERY.*",
            "eqpName" => "",
            "appName" => "MFGBE1",
            "data" => [
                "MESOnlineQueryRequest" => [
                    "appName"              => "MFGBE1",
                    "operator"             => "srv_CMS4TFSB",
                    "operatorPassword"     => "",
                    "requestType"          => "byParms",
                    "parameters"           => [
                        "LOTID"  => strtoupper($batchNumber)
                    ],
                    "onlineQuerySetup"     => "EI_GetLotSpecTranHistory"
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
                $resultStr = '';
                while ($out = socket_read($this->socket, 10240)) {
                    $resultStr .= $out;
                }
                $outresult = Json::decode($resultStr);
                if ($outresult['MESOnlineQueryResponse']['errorText'] == '') {
                    $status['status']  = true;
                    $status['message'] = 'Success';
                    $status['data']    = $outresult['MESOnlineQueryResponse']['rpsData']['__row'];
                    $this->close();
                    return $status;
                } else {
                    $status['message'] = $outresult['MESOnlineQueryResponse']['errorText'];
                    $this->close();
                    return $status;
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



    public function actionUserList()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new CarUser();
        $data         = $searchModel->search($request);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount
        ]);
        return $this->render('user-list', [
            'request'      => $request,
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination
        ]);
    }

    /**
     * @return string
     */
    public function actionUserCreate()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $checkModel = CarUser::findOne(['user_id' => $post['CarUser']['user_id']]);
            if($checkModel){
                return $this->json(100, '用户已存在！');
            }
            $model = new CarUser();
            $model->user_id = $post['CarUser']['user_id'];
            $model->create_time = date('Y-m-d H:i:s', time());
            if (!$model->save(false)) {
                return $this->json(100, '添加失败！');
            }
            return $this->json(200, '添加成功！');
        }
        $model = new CarUser();
      
        return $this->render('user-create', ['model' => $model]);
    }


    /**
     * @return string
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionUserdel()
    {
        $id = (int)Yii::$app->request->get('id');
        $model = CarUser::findOne($id);
        $res = $model->delete();
        if (!$res) {
            return $this->json(100, '删除失败');
        }
        return $this->json(200, '删除成功');
    }


}
