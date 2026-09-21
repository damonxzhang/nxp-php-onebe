<?php
namespace backend\controllers;


use backend\actions\UploadAction;
use backend\behaviors\Upload;
use backend\models\Appeal;
use backend\models\Ir;
use backend\models\IrData;
use backend\models\IrSendMsg;
use backend\models\IrUser;
use backend\models\search\IrSearch;
use backend\models\Team;
use backend\models\Train;
use backend\models\UserInfo;
use backend\modules\Appeal\models\AppealFactory;
use Yii;
use yii\data\Sort;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

class IrController extends BaseController{

    public function actions(){

        return [
            //文件上传
            'upload' => [
                'class' => UploadAction::className(),
                'type' => Upload::IMAGE_TYPE,
            ]
        ];
    }

    /**
     * @return string
     */
    public function actionIndex(){
        $model = new IrSearch();
        $dataProvider = $model->search(Yii::$app->request->queryParams);

        return $this->render('index',[
            'model' => $model,
            'data'  => $dataProvider
        ]);
    }

    /**
     *  历史ir
     * @return string
     */
    public function actionHistory(){

        $model = new IrSearch();
        $dataProvider = $model->searchHistory(Yii::$app->request->queryParams);

        return $this->render('history',[
            'model' => $model,
            'data'  => $dataProvider
        ]);

    }

    /**
     *  逾期ir
     * @return string
     */
    public function actionOverdue(){
        $model = new IrSearch();
        $dataProvider = $model->overdueSearch(Yii::$app->request->queryParams);
        return $this->render('overdue',[
            'model' => $model,
            'data'  => $dataProvider
        ]);
    }
    
    /**
     * 与我有关的
     * @return string
     */
    public function actionCorrelation(){
        $model = new IrSearch();
        $dataProvider = $model->searchCorrelation(Yii::$app->request->queryParams);

        // $userInfo = \Yii::$app->user->identity->userInfo;
        // print_r($userInfo);die;
        return $this->render('correlation',[
            'model' => $model,
            'data'  => $dataProvider
        ]);
    }
    /**
     * 详情
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id){
        $model = $this->findModel($id);
        //拿到文件数据
        $model->getFilePath();
        //查询申诉数据
        $model->getAppealModel();

        $appeal_params = $model->getAlllAppealParams();

        return $this->render('view',[
            'model' => $model,
            'userInfo' =>  Yii::$app->user->identity->userInfo,
            'appeal_params' => !empty($appeal_params ) ? ArrayHelper::index($appeal_params,'type') :$appeal_params
        ]);

    }

    // /**
    //  * 以评估ir实施情况
    //  * @return string
    //  */
    // public function actionAssessList(){
    //    $model = new IrSearch();
    //     $data = $model->searchToAssess();
    //     $params = [];
    //     if (!empty($data)) {
    //         $assessUserArr  = ArrayHelper::getColumn($data, 'assess_user');
    //         $userId         = Yii::$app->user->identity->user_id;
    //         $idArr          = UserInfo::find()->select('id')->where(['leader_uid' => $userId])->column();
    //         $intersectionId = array_intersect($idArr, array_unique($assessUserArr));
    //         $users          = UserInfo::find()->select('id,position,chinese_name as name')->asArray()->where(['id' => $intersectionId])->all();
    //         $users          = ArrayHelper::index($users, 'id');
    //         foreach ((array)$data as $value) {
    //             if (in_array($value['assess_user'], $intersectionId)) {
    //                 $value = array_merge($value, $users[$value['assess_user']]);
    //                 $stick = $value['stick'] !== null ? : 0;
                
    //                 if (isset($params[$value['position']][$value['name']][$value['steps']][$stick])) {
                    
    //                     $params[$value['position']][$value['name']][$value['steps']][$stick] += 1;
    //                 } else {
    //                     $params[$value['position']][$value['name']][$value['steps']][$stick] = 1;
    //                 }
    //             }
    //         }
    //         if (!empty($params)) {
    //             foreach ($params as &$v) {
    //                 foreach ($v as &$va) {
    //                     asort($va);
    //                 }
    //             }
    //         }
    //     }

    //     return $this->render('assess-list',['params' => $params]);
    // }
 /**
     * 以评估ir实施情况
     * @return string
     */
    public function actionAssessList(){
        $userId         = Yii::$app->user->identity->user_id;
        $userInfoModel  = UserInfo::find()->select('id,chinese_name as name')->asArray()->where(['leader_uid' => $userId])->all();
        $userInfoData   =  ArrayHelper::map($userInfoModel, 'id', 'name');
        $result = [];
        if (!empty($userInfoData)) {
                 foreach ($userInfoData as $uid => $value) {
                $result[$uid]['name'] = $value;
                $param['id']          = $uid;
                $param['type']        = 'go';
                //主管审核
                $result[$uid]['leader']['go']      = (new IrSearch())->searchLeaderData($param); //进行中
                $param['type']                     = 'overdue';
                $result[$uid]['leader']['overdue'] = (new IrSearch())->searchLeaderData($param); //逾期

                //负责人
                $param['type']                   = 'go';
                $result[$uid]['head']['go']      = (new IrSearch())->searchHeadData($param); //进行中
                $param['type']                   = 'overdue';
                $result[$uid]['head']['overdue'] = (new IrSearch())->searchHeadData($param); //逾期
                //实施人
                $param['type']                   = 'go';
                $result[$uid]['work']['go']      = (new IrSearch())->searchWorkData($param); //未开始
                $param['type']                   = 'overdue';
                $result[$uid]['work']['overdue'] = (new IrSearch())->searchWorkData($param); //逾期
                $param['type']                   = 'go';
                $result[$uid]['work_new']['go']      = (new IrSearch())->searchWorkDataNew($param); //进行中
                $param['type']                   = 'overdue';
                $result[$uid]['work_new']['overdue'] = (new IrSearch())->searchWorkDataNew($param); //逾期
                //结果确认
                $param['type']                      = 'go';
                $result[$uid]['confirm']['go']      = (new IrSearch())->searchConfirmData($param); //进行中
                $param['type']                      = 'overdue';
                $result[$uid]['confirm']['overdue'] = (new IrSearch())->searchConfirmData($param);//逾期
                //总数统计
                $result[$uid]['all']['go']      = $result[$uid]['leader']['go'] + $result[$uid]['head']['go']+$result[$uid]['work_new']['go']+$result[$uid]['confirm']['go'];//进行中
                $result[$uid]['all']['overdue'] = $result[$uid]['leader']['overdue'] + $result[$uid]['head']['overdue']+$result[$uid]['work']['overdue']+$result[$uid]['confirm']['overdue']; //逾期
            }
        }
        return $this->render('assess-list',['result' => $result]);
    }

    /**
     * 创建IR
     * @return string
     */
    public function actionCreate(){

        $model = new IrData();
        $model->setScenario($model::SUB_IR_SCE);

        if($model->load(Yii::$app->request->post())){
            if($model->old_images){
                $model->old_images = implode(',',$model->old_images);
            }
            $model->create_time = time();
            if($model->save()){
                if($this->sendEmailByStepe($model)){
                    return $this->json(200,'保存成功！');
                }else{
                    return $this->json(200,'邮件发送失败！');
                }
            }else{

                return $this->json(100,$model->errorToString());
            }
        }

        return $this->render('create',[
            'model' => $model,
            'view' => '_form',
            'params' => ['userInfo'=>Yii::$app->user->identity->userInfo]
        ]);
    }

    /**
     * 草稿箱
     * @return string
     */
    public function actionDraft(){

        $model = new IrSearch;
        $dataProvider = $model->draftSearch();

        return $this->render('draft',[
            'model' => $model,
            'data'  => $dataProvider
        ]);
    }

    /**
     * 删除草稿
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id){
        $this->findModel($id)->delete();
        $model = new IrSearch;
        $dataProvider = $model->draftSearch();
        return $this->render('draft',[
            'model' => $model,
            'data'  => $dataProvider
        ]);
    }
    /**
     * 草稿添加
     * @return string
     */
    public function actionAddDraft(){

        $model = new IrData();
        $model->setScenario($model::SUB_IR_SCE);

        if($model->load(Yii::$app->request->post())){
            if($model->old_images){
                $model->old_images = implode($model->old_images,',');
            }
            $model->create_time = time();
            $model->is_draft = 1;
            if($model->save()){

                return $this->json(200,'保存成功！');

            }else{

                return $this->json(100,$model->errorToString());
            }
        }

        return $this->render('create',[
            'model' => $model,
            'view' => '_form',
            'params' => ['userInfo'=>Yii::$app->user->identity->userInfo]
        ]);

    }

    /**
     * 判断是否有权限修改
     * @param $id
     * @return string
     */
    public function actionBeforUpdate(){
        $id = Yii::$app->request->post('id');
        $model = IrData::findOne($id);
        if(empty($model)){
           return $this->json(100,'服务器错误！请稍后尝试！');
        }
        $steps = $model->getSteps();
        // return $this->json(100,$steps);
        if($steps){
            $redirect_url = Yii::$app->request->post('redirect_url');
            if(!empty($redirect_url)){
                Yii::$app->session->set('redirect_url',$redirect_url);
            }
            $url = Url::to(['update','id' => $id,'steps' => $steps]);
            return $this->json(200,'请求成功！',['url' => $url]);
        }else{
            return $this->json(100,'没有操作权限！');
        }
    }

    /**
     * 修改建议
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id,$steps){

        $model = $this->findModel($id);
        $model->steps = $steps;
        $model->setScenarioBySteps();

        if($model->load(Yii::$app->request->post())){
            if($model->status == Ir::STATUS_OK){
                return $this->json(100,'Ir已完成，无法继续操作！');
            }
            if(!$model->validateUser()){
                return $this->json(100,'没有权限！');
            }

            $try = Yii::$app->db->beginTransaction();
            try{
                $model->setSelectArray();
                if( $model->setAttrBySteps() && $model->save() ){
                    if(empty($model->work_status) || $model->work_status == IrData::IMPLEMENT_STATUS_DONE){
                        //发送邮件
                        $this->sendEmailByStepe($model);
                    }
                    $try->commit();
                    if(!empty($model->master_work_users) && $model->steps == Ir::HEAD_STEPS){
                        $master_head_user = IrUser::findMasterUserId($model->id,IrUser::HEAD_TYPE);
                        if($master_head_user->user_id == $model->master_work_users){
                            return $this->redirect(['update','id' => $model->id,'steps' => Ir::WORK_STEPS]);
                        }
                    }
                    $redirect_url = Yii::$app->session->get('redirect_url');
                    if(!empty($redirect_url)){
                        Yii::$app->session->remove('redirect_url');

                        $this->redirect(urldecode($redirect_url));
                    }else{

                        return $this->json(200,'保存成功！');
                    }
                }else{

                   throw new Exception($model->errorToString());
                }
            }catch (\Exception $e){
                $try->rollBack();
                return $this->json(100,$e->getMessage());
            }

        }
        //拿到文件数据
        $model->getFilePath();
        //查询申诉数据
        $model->getAppealModel();
        return $this->render('update',[
            'model' => $model,
            'view' =>IrData::$formNames[$model->steps],
            'params' => [
                'userInfo' => Yii::$app->user->identity->userInfo
            ]
        ]);
    }

    /**
     * 申诉
     * @return string
     */
    public function actionSubmitAppeal(){
        $post = Yii::$app->request->post();
        if(empty($post['content']) || empty($post['user_id'])){
            return $this->json(100,'缺少必填参数！');
        }
        $irModel = IrData::findOne($post['ir_id']);
        if(!empty($irModel) && !$irModel->validateAppealUser()){
            return $this->json(100,'没有权限！');
        }
        $model = AppealFactory::create(Appeal::IR_MODEL);

        $model->setScenario($model::SCEN_CREATE);
        $model->setAttributes([
            'type' => $post['type'],
            'content' => $post['content'],
            'appeal_user' => $post['user_id'],
            'relation_id' => $post['ir_id']
        ]);
        if(!$model->validate()){
            return $this->json(100,$model->errorToString());
        }
        $newModel = $model->addAppeal();
        if($newModel){
            $this->sendEmailByStepe($newModel);
            return $this->json(200,'操作成功！');
        }else{
            return $this->json(100,$model->errorToString());
        }
    }
    /**
     * 申诉审核
     * @return string
     */
    public function actionSubmitAppealAudit(){
        $post = Yii::$app->request->post();

        // if( empty($post['appeal_id']) || Yii::$app->user->identity->user_id != Ir::APPEAL_USER){
        //     return $this->json(100,'非法请求！');
        // }
        // print_r(in_array(Yii::$app->user->identity->user_id ,Ir::$appeal_list ));die;
        if( empty($post['appeal_id']) || !in_array(Yii::$app->user->identity->user_id ,Ir::$appeal_list )){
            return $this->json(100,'非法请求！');
        }
        $model = AppealFactory::create(Appeal::IR_MODEL,$post['appeal_id']);
        $model->setScenario($model::SCEN_AUDIT);
        $model->setAttributes($post);
        $model->complete_date = strtotime( $model->complete_date );
        if(!$model->validate()){
            return $this->json(100,$model->errorToString());
        }
        $newModel = $model->auditAppeal();

        if($newModel){
            $this->sendEmailByStepe($newModel);

            return $this->json(200,'操作成功！');
        }else{
            return $this->json(100,$model->errorToString());
        }
    }

    /**
     * 搜索课程title
     * @return string
     *
     */
    public function actionSearchIrTitle(){
        $model = new IrSearch();
        $data = $model->searchTitle(Yii::$app->request->get('keyword'));

        return json_encode(['results' => $data]);
    }

    /**
     * 导出excel
     * @param $idlist
     */
    public function actionExprotExcel($idlist = null,$type=null){

        $model = new IrSearch();
        if ($type == 'history'){
            $dataProvider = $model->searchHistory(Yii::$app->request->queryParams);
        }else{
            $dataProvider = $model->search(Yii::$app->request->queryParams);
        }
        if(!empty($idlist)){
            $ids = explode(',',$idlist);
            $dataProvider->query->andWhere(['i.id' => $ids]);

            if(!empty($ids)){
                $dataProvider->query->andWhere(['i.id' => $ids]);
            }
        }
        $dataProvider->query->leftJoin(['t' => Team::tableName()],'u.team = t.id');

        $dataProvider->query
            ->select('i.*,u.shift,u.chinese_name,u.department_number,u.pid,u.area,t.teamName')
            ->orderBy('id desc');

        $irModel = $dataProvider->query->asArray()->all();
        $file_name  = "Ir-".date("Y-m-d H:i:s",time());
        $file_suffix = "xls";
        header("Content-type:application/vnd.ms-excel;charset=utf8");
        header("Content-Disposition:attachment;filename=$file_name.$file_suffix");
	
	echo $this->render('excel_view',['data' => $irModel]);
    }

    /**
     * 发送邮件
     * @param Ir $model
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function sendEmailByStepe(Ir $model){
        $msgModel = Yii::createObject([
            'class' => IrSendMsg::className(),
            'irModel' => $model
        ]);
        if($msgModel->sendEmail()){
            return true;
        }
        return false;
    }

    /**
     * 查询并返回模型
     * @param $id
     * @return IrData|null
     * @throws NotFoundHttpException
     */
    public function findModel($id){
        $model = IrData::findOne($id);
        if(empty($model)){
            throw new NotFoundHttpException('page no found');
        }
        return $model;

    }

}
