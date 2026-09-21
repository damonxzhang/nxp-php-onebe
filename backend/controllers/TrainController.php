<?php

namespace backend\controllers;

use backend\models\TrainCourses;
use backend\models\TrainUser;
use backend\models\UserInfo;
use Yii;
use backend\models\Train;
use backend\models\search\TrainSearch;
use backend\controllers\BaseController;
use yii\db\Exception;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TrainController implements the CRUD actions for Train model.
 */
class TrainController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST','GET'],
                ],
            ],
        ];
    }

    /**
     * Lists all Train models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrainSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $searchModel->start_time && $searchModel->start_time = date('Y-m-d', $searchModel->start_time);
        $searchModel->end_time   && $searchModel->end_time   = date('Y-m-d', $searchModel->end_time);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Train model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $model->coursesInfo->setFilePathById();

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     *  学习
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionStudy($id){
        $model = $this->findModel($id);
        $trainUser = TrainUser::findOne(['train_id' => $model->id,'user_id' => Yii::$app->user->identity->user_id]);

        if(empty($trainUser)){
            throw new Exception('未参与该课程！无法学习，请点击查看');
        }
        //学习
        if($trainUser->sign_in == TrainUser::SIGN_IN_YES){

            $type= TrainUser::STUDY_TYPE;
        }else{
            $time = time();
            //自习
            if($model->start_time <= $time && $time <= $model->end_date){
                $type = TrainUser::REVIEW_TYPE;
            //补课
            }elseif($time > $model->end_date){
                $type = TrainUser::LESSONS_TYPE;
            }
        }
        $model->coursesInfo->setFilePathById();

        return $this->render('study', [
            'model' => $model,
            'trainUser' => $trainUser,
            'type' => $type
        ]);
    }

    /**
     * 学习
     * @param $id
     * @return string
     */
    public function actionBeforStudy($id){
        $model = Train::findOne($id);
        if(time() < $model->start_time){
            return $this->json(100,'还未到开课时间,无法参与学习!');
        }
        $exists = TrainUser::find()->where(['train_id' => $id,'user_id' => Yii::$app->user->identity->user_id])->exists();
        if($exists){
            return $this->json(200,'操作成功');
        }else{
            return $this->json(100,'不是当前课程的学员！无法参与学习');
        }
    }

    /**
     * 保存补课或自习
     * @return string
     */
    public function actionSaveStudy(){
        $post = Yii::$app->request->post();
        $trainUser = TrainUser::findOne(['id' => $post['id'],'user_id' => Yii::$app->user->identity->user_id]);

        if(empty($trainUser)){
            return $this->json(100,'保存失败！请刷新后重试');
        }
        if(empty($trainUser->study_start_time)){
            return $this->json(100,'请阅读或观看相关课件！');
        }
        $courses = TrainCourses::findOne($trainUser->courses_id);
        //计算从当前时间到开始时间过去多久
        $studyTime = time()- $trainUser->study_start_time;

        //时间戳传唤为小时
        $endTime =  round($studyTime/60,1);
        if($studyTime <= 0 || $endTime < $courses->duration){
            return $this->json(100,'未达到规定学习时长！请继续学习');
        }
        $trainUser->study_end_time = time();
        $trainUser->study_date = round($endTime,1);
        $trainUser->sign_in = TrainUser::SIGN_IN_YES;
        $trainUser->promise = 1;
        $train = Train::findOne($trainUser->train_id);

        if($trainUser->save() && $train->updateProgress()){

            return $this->json(200,'学习完成！');
        }else{
            return $this->json(100,'保存失败！请刷新后重试！');
        }
    }

    /**
     * 保存开始学习时间
     * @return string
     */
    public function actionStartStudy(){
        $post = Yii::$app->request->post();
        $trainUser = TrainUser::findOne(['id' => $post['tuId'],'user_id' => Yii::$app->user->identity->user_id]);

        if(empty($trainUser)){
            return $this->json(100,'不是该课程学员无法学习！');
        }
        $trainUser->study_start_time = time();
        $trainUser->study_type = $post['type'];
        if($trainUser->save()){
            return $this->json(200,'保存成功！',$trainUser->study_start_time);
        }else{
            return $this->json(10,'数据错误！请刷新后重试');
        }

    }

    /**
     * Creates a new Train model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Train();
        $model->setScenario($model::SCENARIOS_DEFAULT);
        if ($model->load(Yii::$app->request->post())) {
            !empty($model->lecturer) && $model->lecturer = implode(',',$model->lecturer);
            !empty($model->director) && $model->director = implode(',',$model->director);

            if(!empty( $model->start_time )){
                $model->end_date = strtotime( $model->start_time ." + $model->end_time day");
                $model->start_time = strtotime($model->start_time);
            }
            $trn = Yii::$app->db->beginTransaction();
            try{
                if($model->save()){
                    $directorIds = explode(',',$model->director);
                    $directors = UserInfo::find()->where(['id' => $directorIds])->select('chinese_name,email')->asArray()->all();

                    if(!$this->sendEmailByDirector($model,$directors)){
                        throw new Exception('邮件发送失败！');
                    }
                    $trn->commit();
                    return $this->json(200,'保存成功！');
                }else{

                    throw new Exception($model->errorToString());
                }

            }catch (\Exception $e){
                $trn->rollBack();
                return $this->json(100,$e->getMessage());
            }
        }

        $coursesList = TrainCourses::CoursesList();
        $userInfo = Yii::$app->user->identity->userInfo;
        $section     = ['id'=>$userInfo['section'],'value' =>  UserInfo::$sectionArr[$userInfo['section']]];

        return $this->render('create', [
            'model' => $model,
            'coursesList' => $coursesList,
            'section' => $section
        ]);
    }

    /**
     * Updates an existing Train model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario($model::SCENARIOS_DEFAULT);

        if ($model->load(Yii::$app->request->post())) {

            if(!empty( $model->start_time )){
                $model->end_date = strtotime( $model->start_time ." + $model->end_time day");
                $model->start_time = strtotime($model->start_time);
            }
            !empty($model->lecturer) && $model->lecturer = implode(',',$model->lecturer);
            !empty($model->director) && $model->director = implode(',',$model->director);
            $trn = Yii::$app->db->beginTransaction();
            try{
                if($model->save()){

                    $trn->commit();
                    return $this->json(200,'保存成功！');

                }else{

                    throw new Exception($model->errorToString());
                }

            }catch (\Exception $e){
                $trn->rollBack();
                return $this->json(100,$e->getMessage());
            }
        }
        $model->setUserIds();
        $coursesList =  TrainCourses::CoursesList();

        $model->start_time = date('Y-m-d',$model->start_time);

        $userInfo = Yii::$app->user->identity->userInfo;
        $section     = ['id'=>$userInfo['section'],'value' =>  UserInfo::$sectionArr[$userInfo['section']]];
        return $this->render('update', [
            'model' => $model,
            'coursesList' => $coursesList,
            'section' => $section
        ]);
    }

    /**
     * 批量发送提示邮件
     * @param $directors
     * @return bool
     */
    public function sendEmailByDirector(Train $model,$directors){

        if(empty($directors) || count($directors) <= 0){
            return false;
        }
        foreach ($directors as $val){
            if(!empty($val['email'])){

                $emails[] = $val['email'];
            }
        }
        if(empty($emails)){
           return false;
        }

        $emailObj = Yii::$app->mailer->compose('@backend/views/train/email_view.php',['model' => $model]);
        $result   = $emailObj->setTo($emails)//设置收件人的地址
        ->setSubject('新的培训课程')//设置邮件主题
        ->send();//发送方法
        return $result;
    }

    /**
     * Deletes an existing Train model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * 导出excel
     * @return string
     */
    public function actionExport(){

        $post = Yii::$app->request->post();
        if(empty($post['export_type'])){
            return $this->json(100,'请选择导出类型');
        }
        $idlist = !empty($post['idlist']) ? $post['idlist'] :null;
        $model = new TrainSearch();
        $data = $model->getExportData($post['export_type'],$idlist);

        if($data){
            list($fileName,$newdata) = $this->byTypeSetContent($data,$post['export_type']);
            if($newdata){
                return $this->json(200, $fileName, $newdata);

            }
        }
        return $this->json(100,'数据异常！请稍后重试');
    }

    public function byTypeSetContent($data,$type){

        if(in_array($type,array_keys(Train::$exportNames))){
            $name = Train::$exportNames[$type].'_';
        }else{
            return false;
        }
        if(in_array($type,[Train::EXPORT_TYPE_YES,Train::EXPORT_TYPE_NO])){
            $fileName = $name . time() . '.xlsx';
            return [$fileName,$this->setTrainList($data)];
        }elseif(in_array($type,[Train::EXPORT_TYPE_USER_YES,Train::EXPORT_TYPE_USER_NO])){
            $fileName = $name . time() . '.xlsx';
            return [$fileName,$this->setTrainUserList($data)];
        }else{
            $fileName = $name . time() . '.xlsx';
            return [$fileName,$this->setTrainUserList($data)];
        }
    }

    public function setTrainList($data){
        $newData = [];
        foreach ($data as $key => $val){
            $newData[$key][] = $val['id'];
            $newData[$key][] = $val['name'];
            $newData[$key][] = $val['chinese_name'];
            $newData[$key][] = $this->getLecturerList($val['lecturer']);
            $newData[$key][] = $val['progress'].'%';
            $newData[$key][] = date('Y-m-d',$val['start_time']);
            $newData[$key][] = date('Y-m-d',$val['end_date']);
        }

        $header   = ['id', '课程名称','创建人', '讲师', '完成率', '开始时间', '结束时间'];
        array_unshift($newData, $header); //添加头部标题
        return $newData;
    }


    public function setTrainUserList($data){
        $newData = [];
        foreach ($data as $key => $val){
            $newData[$key][] = $val['tid'];
            $newData[$key][] = $val['name'];
            $newData[$key][] = $val['create_user_name'];
            $newData[$key][] = $val['chinese_name'];
            $newData[$key][] = $this->getLecturerList($val['lecturer']);
            $newData[$key][] = !empty(UserInfo::$shiftArr[$val['shift']])?UserInfo::$shiftArr[$val['shift']]:'';
            $newData[$key][] = $val['study_date'].'分钟';
            $newData[$key][] = !empty(TrainUser::$studyTypes[$val['study_type']])?TrainUser::$studyTypes[$val['study_type']]:'';
            $newData[$key][] = $val['sign_in'] == TrainUser::SIGN_IN_YES ? '已签到' : '未签到';
            $newData[$key][] = TrainUser::$statusArr[$val['status']];
            if(!empty($val['update_time']) && $val['sign_in'] == TrainUser::SIGN_IN_YES){

                $newData[$key][] = date('Y-m-d H:i:s',$val['update_time']);
            }else{
                $newData[$key][] = '';
            }
        }
        $header   = ['培训ID', '课程名称', '创建人', '学员名称', '讲师', '班别','学习时长','学习类型', '是否签到', '状态','签到时间'];
        array_unshift($newData, $header); //添加头部标题
        return $newData;

    }

    /**
     * 查询讲师名称
     * @param $idStr
     * @return string
     */
    public function getLecturerList($idStr){
        $id = explode(',',$idStr);

        $lecturerName = UserInfo::find()->select('chinese_name')->where(['id' => $id])->column();
        if(!empty($lecturerName)){
            return implode(',',$lecturerName);
        }
        return '';
    }

    /**
     * Finds the Train model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Train the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Train::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
