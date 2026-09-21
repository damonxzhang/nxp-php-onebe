<?php

namespace backend\controllers;

use backend\models\Facility;
use backend\models\MaintainFeedback;
use backend\models\MaintainReportRecord;
use backend\models\MaintainType;
use backend\models\search\BossMaintainSearch;
use backend\models\service\MaintainService;
use backend\models\UserInfo;
use backend\modules\MaintainApi\models\SendEmail;
use xj\qrcode\widgets\Email;
use Yii;
use backend\models\Maintain;
use backend\models\search\MaintainSearch;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\i18n\I18N;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MaintainController implements the CRUD actions for MaintainApi model.
 */
class MaintainController extends BaseController
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
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all MaintainApi models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MaintainSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $typeList = MaintainType::getTypeList();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'typeList' => $typeList,
        ]);
    }

    /**
     * Lists all MaintainApi models.
     * @return mixed
     */
    public function actionBossIndex()
    {
        $searchModel = new MaintainSearch();
        $dataProvider = $searchModel->searchBoss(Yii::$app->request->queryParams);
        $typeList = MaintainType::getTypeList();
        return $this->render('boss_index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'typeList' => $typeList,
        ]);
    }

    /**
     * Lists all MaintainApi models.
     * @return mixed
     */
    public function actionFinish()
    {
        $searchModel = new MaintainSearch();
        $dataProvider = $searchModel->searchFinish(Yii::$app->request->queryParams);
        $typeList = MaintainType::getTypeList();
        return $this->render('finish', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'typeList' => $typeList,
        ]);
    }

    /**
     * Displays a single MaintainApi model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $userInfo = UserInfo::findOne($model->create_user);
        $facility = (new Facility)->findFacilityInfo($model->f_id);
        $typeList = MaintainType::getTypeList();
        return $this->render('view', [
            'model' => $model,
            'userInfo' => $userInfo,
            'facility' => $facility,
            'typeList' => $typeList,
        ]);
    }

    /**
     * Creates a new MaintainApi model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($boss = null)
    {
        $model = new MaintainService();
        $userInfo = Yii::$app->user->identity->userInfo;
        $model->number = Maintain::getNumber();
        $model->setScenario(MaintainService::CREATE_SCENARIOS);
         if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $model->things = $post['things'];
            if ($model->load($post)) {
                $model->material_number = implode('--',$post['material_number']);
                if ($boss == Maintain::BOSS_YES) {
                    $model->boss = Maintain::BOSS_YES;
                }
                $tra = Yii::$app->db->beginTransaction();
                try{
                    if ($model->newMaintain()) {
                        $this->sendRemindByEmail($model);
                        $tra->commit();
                        return $this->json(200, '保存成功！');
                    } else {
                        throw new Exception($model->errorToString());
                    }
                }catch (Exception $e){
                    $tra->rollBack();
                    return $this->json(100,$e->getMessage());
                }
            }
        }
        return $this->render('create', [
            'model' => $model,
            'userInfo' => $userInfo
        ]);
    }

    /**
     * 查询是否有权限审核
     * @param $id
     * @return string
     */
    public function actionVerifyAccessUser($id)
    {
        $model = $this->findModel($id);
        if (!$model->verifyUserLeader($id)) {
            return $this->json(100, '没有权限！');
        }
        if (!empty($model->access_user)) {
            return $this->json(100, '已经审核！');
        }
        return $this->json(200, '操作成功！');
    }

    /**
     * 验证反馈用户
     * @param $id
     * @return string
     */
    public function actionVerifyFeedbackUser($id)
    {
        $model = $this->findModel($id);

        if ( $model->status == Maintain::STATUS_FINISH) {
            return $this->json(100, '异常以及完成！');
        }

        if (empty($model->boss) && (empty($model->access_status) || $model->access_status == Maintain::SELECT_NO)) {
            return $this->json(100, '审核未通过！');
        }
        $user_id = Yii::$app->user->identity->user_id;
        if ($model->feedback_user != $user_id && $model->other_feedback_user != $user_id) {
            return $this->json(100, '没有权限！');
        }
        $result = MaintainFeedback::isFinishFeedback($model);
        if($result){
            return $this->json(100, $result);
        }

        return $this->json(200, '操作成功！');
    }

    /**
     * 主管审核
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionAccess($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(MaintainService::ACCESS_SCENARIOS);
        if ($model->load(Yii::$app->request->post())) {
            if (!$model->verifyUserLeader()) {
                return $this->json(100, '没有权限！');
            }
        
            $model->access_advice = $model::$accessStatusList[$model->access_status];
            $model->access_user = Yii::$app->user->identity->user_id;
            if ($model->access_status == Maintain::SELECT_NO) {
                $model->status = Maintain::STATUS_DIE;
            }

            if ($model->save()) {
                return $this->json(200, '保存成功！');
            } else {
                return $this->json(100, $model->errorToString());
            }
        }

        $userInfo = UserInfo::findOne($model->create_user);
        $model->setScenario(MaintainService::CREATE_SCENARIOS);
        $facility = (new Facility)->findFacilityInfo($model->f_id);
        return $this->render('access', [
            'model' => $model,
            'userInfo' => $userInfo,
            'facility' => $facility
        ]);

    }

    /**
     * 次品数量
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionRejects($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(MaintainService::REJECTS_SCENARIOS);
        if ($model->load(Yii::$app->request->post())) {

            if ($model->save()) {
                return $this->json(200, '保存成功！');
            } else {
                return $this->json(100, $model->errorToString());
            }
        }
        $userInfo = UserInfo::findOne($model->create_user);
        $model->setScenario(MaintainService::CREATE_SCENARIOS);
        $facility = (new Facility)->findFacilityInfo($model->f_id);
        return $this->render('rejects', [
            'model' => $model,
            'userInfo' => $userInfo,
            'facility' => $facility
        ]);

    }

    /**
     * boss直捡报告
     * @return string
     */
    public function actionReport()
    {
        $model = new BossMaintainSearch();
        $recortModel = new MaintainReportRecord();

        //查询柱状图数据
        $barResult = $model->searchBarInfo(Yii::$app->request->queryParams);

        $barData = [];
        $barCategory = [];
        if ($barResult) {
            $this->setBarData($barResult, $barData, $barCategory);
        }

        //查询折线图数据
        $lineResult = $model->searchLineInfo(Yii::$app->request->queryParams);
        $lineData = [];
        $lineCategory = [];

        if ($barResult) {
            $this->setBarData($lineResult, $lineData, $lineCategory, 'type');
        }

        $recortData = $recortModel::find()->where(['create_user' => Yii::$app->user->identity->user_id])->asArray()->orderBy('id desc')->all();

        return $this->render('report', [
            'model' => $model,
            'barCategory' => $barCategory,
            'barData' => $barData,
            'lineCategory' => $lineCategory,
            'lineData' => $lineData,
            'recortData' => $recortData
        ]);
    }

    public function actionReportForms()
    {
        $model = new MaintainSearch();
        $params = Yii::$app->request->queryParams;
        //输入设备号就让其它选项为空
        if (isset($params['MaintainSearch']['f_id']) && !empty($params['MaintainSearch']['f_id'])) {
            $params['MaintainSearch']['type']       = '';
            $params['MaintainSearch']['process']    = '';
            $params['MaintainSearch']['area']       = '';
            $params['MaintainSearch']['start_time'] = '';
            $params['MaintainSearch']['end_time']   = '';
        }
        //查询柱状图数据
        list($data, $list) = $model->getDataByRequest($params);

        return $this->render('report_forms', [
            'model' => $model,
            'data' => $data,
            'list' => $list
        ]);
    }

    /**
     *
     * @param Maintain $maintain
     */
    public function sendRemindByEmail(Maintain $maintain){
        $user = new UserInfo();
        $ids = $user->getLeaderUids($maintain->create_user);
        if(empty($ids[0])){
            throw new Exception('当前用户没有直属领导！请检在用户管理中添加后再进行此操作！');
        }
        $maintain->feedback_user        && $ids[] = $maintain->feedback_user;
        $maintain->other_feedback_user  && $ids[] = $maintain->other_feedback_user;
        //查询用户的名称和邮箱
        $userEmail = $user->getUserEmail($ids);
        if(!empty($userEmail)){
            $params = $maintain->attributes;
            foreach ($userEmail as $key => $val){
                $params['name'] = $val['chinese_name'];
                if (empty($val['email'])){
                    throw new Exception('用户'.$params['name'].'未输入邮箱！请完善用户邮箱！');
                }

                if(!$this->sendRemindEmail($val['email'],$params)){
                    throw new Exception('用户'.$params['name'].'邮件发送失败！请检查邮箱的有效性！');
                }
            }
        }
        return false;
    }

    /**
     * 发送邮件
     * @param $email
     * @param $params
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function sendRemindEmail($email,$params){

        return Yii::createObject([
            'class' => SendEmail::className(),
            'to' => trim($email,"\r\n"),
            'view' => SendEmail::CREATE_EMAIL_VIEW,
            'subject' => 'New Anomaly Record',
            'renderParams' => $params
        ])->send();
    }

    /**
     * 设置参数
     * @param $result
     * @param $data
     * @param $category
     */
    public function setBarData($result, &$data, &$category, $type = 'area')
    {
        $params = [];
        foreach ($result as $v) {
            $date = date('m月', $v['create_time']);

            $params[$date][$v[$type]][] = $v['id'];
        }

        $category = array_keys($params);

        foreach ($params as $key => &$va) {

            if ($type == 'area') {
                foreach (UserInfo::$areaArr as $ke => $v) {
                    if (empty($va[$ke])) {
                        $va[$ke] = [];
                    }
                }
            } elseif ($type == 'type') {
                foreach (Maintain::$problem as $ke => $v) {
                    if (empty($va[$ke])) {
                        $va[$ke] = [];
                    }
                }
            }
            ksort($va);

            foreach ($va as $k => $val) {

                $data[$k][] = count($val);
            }
        }
    }

    /**
     * 导出反馈excel
     * @return string
     */
    public function actionExportFeedbackExcel()
    {
        $searchModel = new MaintainSearch();
        $dataProvider = $searchModel->exportSearch(Yii::$app->request->queryParams);

        return $this->setAttrByFeedbackExcel($dataProvider,'未完成异常反馈_');
    }


    /**
     * 已完成导出
     * @return string
     */
    public function actionExportFinishFeedbackExcel(){
        $searchModel = new MaintainSearch();
        $dataProvider = $searchModel->exportFinishSearch(Yii::$app->request->queryParams);
        return $this->setAttrByFeedbackExcel($dataProvider,'已完成设备异常反馈_');
    }

    /**
     * @param $dataProvider
     * @param $name
     * @return string
     */
    public function setAttrByFeedbackExcel($dataProvider,$name){

        if ($dataProvider){
            $data = [];
            $header = [
                '异常编号',
                '反馈用户',
                '反馈类型',
                '其他反馈类型',
                '反馈行动',
                '其他行动',
                '完成状态',
                '预计完成时间',
                '实际完成时间',
                '反馈时间',
            ];
            static $k = 0;
            foreach ($dataProvider as $value){
                $feedback = MaintainFeedback::findAll(['m_id' => $value['id']]);
                if(!empty($feedback)){
                    foreach ($feedback as $val){
                        $data[$k][] = $value['number'];
                        $data[$k][] = (new UserInfo())->getUserName($val['feedback_user']);
                        $data[$k][] = ArrayHelper::getValue(Maintain::$problem,$val['feedback_type']);
                        $data[$k][] = $val['other_feedback'];
                        $data[$k][] = ArrayHelper::getValue(MaintainFeedback::$actionList,$val['action']);
                        $data[$k][] = $val['other_action'];
                        $data[$k][] = ArrayHelper::getValue(MaintainFeedback::$solveStatusList,$val['solve_status']);
                        $data[$k][] = date('Y-m-d',$val['predict_date']);
                        $data[$k][] = !empty($val['complete_date']) ? date('Y-m-d',$val['complete_date']):'';
                        $data[$k][] = date('Y-m-d',$val['create_time']);
                        $k ++ ;
                    }
                }
                continue;
            }
            $fileName = $name . time() . '.xlsx';

            array_unshift($data, $header); //添加头部标题
            return $this->json(200, $fileName, $data);
        }
    }


    /**
     * 导出excel
     * @return string
     */
    public function actionExportExcel()
    {
        $searchModel = new MaintainSearch();
        $dataProvider = $searchModel->exportSearch(Yii::$app->request->queryParams);
        return $this->setAttrByExcel($dataProvider,'未完成设备异常_');
    }

    /**
     * 已完成导出
     * @return string
     */
    public function actionExportFinishExcel(){
        $dataProvider = $this->exportFinishData(Yii::$app->request->queryParams);
        return $this->setAttrByExcel($dataProvider,'已完成设备异常_');
    }
/**
     * 搜索已完成的导出
     * @param $params
     * @return array
     */
    public function exportFinishData($params){
        $query = Maintain::find();
        $query->alias('m')
            ->leftJoin(['f' => Facility::tableName()], 'm.f_id = f.id')
            ->innerJoin(['u' => UserInfo::tableName()],'m.create_user = u.id');
        $query->andWhere(['!=','m.status', Maintain::STATUS_MARCH]);
        if(!empty($params['MaintainSearch']['type']))
            $query->andFilterWhere([ 'm.type' => $params['MaintainSearch']['type']]);
        if(!empty($params['MaintainSearch']['area']))
            $query->andFilterWhere([ 'm.area' => $params['MaintainSearch']['area']]);
        if(!empty($params['MaintainSearch']['shift']))
            $query->andFilterWhere([ 'f.shift' => $params['MaintainSearch']['shift']]);
        if(!empty($params['MaintainSearch']['process']))
            $query->andFilterWhere([ 'f.process' => $params['MaintainSearch']['process']]);
        if(!empty($params['MaintainSearch']['start_time'])){
            $start_time = strtotime($params['MaintainSearch']['start_time']);
            $query->andFilterWhere(['>','m.create_time',$start_time]);
        }
        if(!empty($params['MaintainSearch']['end_time'])){
            $end_time = strtotime($params['MaintainSearch']['end_time']);
            $query->andFilterWhere(['<','m.create_time',$end_time]);
        }
        $idList  = ArrayHelper::getValue($params, 'idlist', []);
        if (!empty($idList)) {
            $query->andWhere(['m.id' => $idList]);
        }
        $query->orderBy('id desc');
        return $query->all();
    }
    /**
     * 设置email属性
     * @param $dataProvider
     * @return string
     */
    public function setAttrByExcel($dataProvider,$name){
        $data = [];
        $thingsList =  MaintainType::getTypeList();
        if (!empty($dataProvider)) {
            foreach ($dataProvider as $key => $value) {
                $create_user = UserInfo::findOne($value['create_user']);
                if(empty($create_user)){
                    continue;
                }
               

                $data[$key][] = $value['number'];
                $data[$key][] = date('Y-m-d',$value['create_time']);
                $data[$key][] = $create_user->chinese_name;
                $data[$key][] = UserInfo::$shiftArr[$create_user->shift];
                $data[$key][] = UserInfo::$areaArr[$create_user->area];
                $data[$key][] = !empty($value->facility->process) ? Facility::$processList[$value->facility->process]:'';
                $data[$key][] = (new UserInfo())->getUserName($create_user->leader_uid);
                $data[$key][] = !empty($value->f_id) ?$value->facility->number : '' ;
                $data[$key][] = $value['material_number'];
                 
                $things = '';
                $things = MaintainType::findOne($value['things']);
                if(empty($things)){
                    $data[$key][] = '';
                }
                else{
                    $data[$key][] = $things->name;
                }
                    
                $data[$key][] = !empty($value['other_things']) ?$value['other_things']:'';
                
                if (empty($value['type']) || $value['type']>5)
                    $data[$key][] = Maintain::$problem[5];
                else
                    $data[$key][] = Maintain::$problem[$value['type']];

                $data[$key][] = ArrayHelper::getValue($value,'other_type');
                $data[$key][] = !empty($value->feedback_user) ?(new UserInfo())->getUserName($value->feedback_user): '';
                $data[$key][] = !empty($value->other_feedback_user) ?(new UserInfo())->getUserName($value->other_feedback_user): '';
                $data[$key][] = Maintain::$statusList[$value['status']];
                $data[$key][] = $value['rejects'];
            }
        }
        $fileName = $name . time() . '.xlsx';
        $header = [
            '编号',//number
            '创建日期',//create_time
            '创建人',//create_user
            '班别',//shift
            '区域',
            '工序',//process
            '主管带班',//leader_uid
            '设备编号',//facility->number
            '问题物料批号',//material_number
            '问题事项',//things
            '问题事项描述',//other_things
            '问题类型',//type
            '问题类型描述',//other_type
            '需反馈人员',//head_user
            '其他需反馈人员',//head_user
            '解决状态',//status
            '次品数量',//rejects
        ];
        array_unshift($data, $header); //添加头部标题
        return $this->json(200, $fileName, $data);
    }


    /**
     * Finds the MaintainApi model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Maintain the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MaintainService::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
