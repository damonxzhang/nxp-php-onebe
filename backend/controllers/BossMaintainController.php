<?php

namespace backend\controllers;
use backend\models\BossFeedback;
use backend\models\BossMaintainLeader;
use backend\models\Facility;
use backend\models\MaintainFeedback;
use backend\models\MaintainReportRecord;
use backend\models\search\FacilitySearch;
use backend\models\UserInfo;
use backend\modules\MaintainApi\models\SendEmail;
use Yii;
use backend\models\BossMaintain;
use backend\models\search\BossMaintainSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
/**
 * BossMaintainController implements the CRUD actions for BossMaintain model.
 */
class BossMaintainController extends BaseController
{
    /**
     * Lists all BossMaintain models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BossMaintainSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single BossMaintain model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new BossMaintain model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new BossMaintain();
        $model->number = $model::getNumber(8,'B');

        if ($model->load(Yii::$app->request->post())) {
            if($model->setFacilityInfo() && $model->newBossMaintain()){
                //$this->sendCreateEmail($model);

                return $this->json(200,'操作成功！');
            }
            return $this->json(100,$model->errorToString());
        }
        return $this->render('create', [
            'model' => $model,
            'userInfo' => Yii::$app->user->identity->userInfo
        ]);
    }
    /**
     * 发送邮件
     * @param BossMaintain $maintainModel
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function sendCreateEmail(BossMaintain $maintainModel){
        $leaderUserId = BossMaintainLeader::getAreaLeaderUserId($maintainModel->f_id);

        $userEmails =(new UserInfo())->getUserEmail(ArrayHelper::merge($leaderUserId,[$maintainModel->feedback_user,$maintainModel->head_user]));

        if(!empty($userEmails)){
            foreach ($userEmails as $user){
                $email[] = $user['email'];
            }

            return Yii::createObject([
                'class' => SendEmail::className(),
                'to' => $email,
                'view' => SendEmail::BOSS_CREATE_EMAIL_VIEW,
                'subject' => 'New Boss Audit Record',
                'renderParams' => $maintainModel
            ])->send();
        }
    }

    /**
     * 验证用户是否有权限反馈
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionCheckFeedbackUser($id){
        $model = $this->findModel($id);
        if($model->status != $model::STATUS_MARCH){
            return $this->json(100,'已完成或被取消！');
        }

        if($model->checkFeedback()){
            return $this->json(200,'验证通过！');
        }else{
            return $this->json(100,$model->errorToString());
        }
    }

    /**
     * 查询设备及负责人
     * @return string
     */
    public function actionGetAutoInput(){
        $number = Yii::$app->request->post('number');
        if(!empty($number)){
            $model = new FacilitySearch();
            $facility = $model->findFacilityUserInfo($number);

            $facility['leaderName'] = (new UserInfo())->getUserName($facility['leader_uid']);
            if(empty($facility)){
                return $this->json(100,'设备不存！');
            }
            $leaderNames = BossMaintainLeader::getLeaderByArea($facility['area']);
            if(empty($leaderNames)){
                return $this->json(100,'该区域反馈人员未输入！');
            }
            return $this->json(200,'操作成功！',ArrayHelper::merge(['leaderNames' => $leaderNames ], $facility));
        }
        return $this->json(100,'请输入设备编号！');
    }

    /**
     * Updates an existing BossMaintain model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * 导出反馈excel
     * @return string
     */
    public function actionExportFeedbackExcel()
    {
        $searchModel = new BossMaintainSearch();
        $dataProvider = $searchModel->exportSearch(Yii::$app->request->queryParams);

        return $this->setAttrByFeedbackExcel($dataProvider,'未完成异常反馈_');
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
                '反馈行动',
                '其他行动',
                '完成状态',
                '预计完成时间',
                '实际完成时间',
                '反馈时间',
            ];
            static $k = 0;
            foreach ($dataProvider as $value){
                $feedback = BossFeedback::findAll(['m_id' => $value['id']]);
                if(!empty($feedback)){
                    foreach ($feedback as $val){
                        $data[$k][] = $value['number'];
                        $data[$k][] = (new UserInfo())->getUserName($val['feedback_user']);
                        $data[$k][] = MaintainFeedback::$actionList[$val['action']];
                        $data[$k][] = $val['other_action'];
                        $data[$k][] = MaintainFeedback::$solveStatusList[$val['solve_status']];
                        $data[$k][] = date('Y-m-d',$val['predict_date']);
                        $data[$k][] = !empty($val['complete_date']) ? date('Y/m/d',$val['complete_date']):'';
                        $data[$k][] = date('Y/m/d',$val['create_time']);
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
        $searchModel = new BossMaintainSearch();
        //$dataProvider = $searchModel->exportSearch(Yii::$app->request->queryParams);
        // $dataProvider = $searchModel->exportSearch(Yii::$app->request->getQueryParams());
        $query = BossMaintain::find();
        $params = Yii::$app->request->queryParams;
 
        if(!empty($params['query'])){
            $paramsArr = json_decode($params['query'],1);
            if(!empty($paramsArr['BossMaintainSearch']['start_time'])){
                $start_time = strtotime($paramsArr['BossMaintainSearch']['start_time']);
                $query->andFilterWhere(['>','create_time',$start_time]);
            }
            if(!empty($paramsArr['BossMaintainSearch']['end_time'])){
                $end_time = strtotime($paramsArr['BossMaintainSearch']['end_time']);
                $query->andFilterWhere(['<','create_time',$end_time]);
            }
        }
       
        // $query->asArray();
        $dataProvider = $query->all();
        
        $data =  $this->setAttrByExcel($dataProvider,'MT检查_');
        $fileName = 'MT检查_' . time() . '.xls';
        header("Content-type:application/vnd.ms-excel;charset=utf8");
        header("Content-Disposition:attachment;filename=".$fileName);

        echo $this->render('export-excel',['data' => $data]);
    }


    /**
     * 设置excel属性
     * @param $dataProvider
     * @return string
     */
    public function setAttrByExcel($dataProvider,$name){
        $data = [];
        if (!empty($dataProvider)) {
            foreach ($dataProvider as $key => $value) {
                $create_user = UserInfo::findOne($value['create_user']);
                if(empty($create_user)){
                    continue;
                }
                $data[$key]['id'] = $value['id'];
                $data[$key]['things'] = $value['things'];
                $data[$key]['process'] = !empty($value->facility->process) ? Facility::$processList[$value->facility->process]:'';
                $data[$key]['number'] = !empty($value->f_id) ?$value->facility->number : '' ;
                $data[$key]['feedback_user'] = !empty($value->feedback_user) ?(new UserInfo())->getUserName($value->feedback_user): '';
                $data[$key]['area'] = !empty($value->area)? UserInfo::$areaArr[$value->area]:'';
                $data[$key]['status'] = BossMaintain::$statusList[$value['status']];
                $data[$key]['type'] = !is_null($value['type'])?BossMaintain::$typeList[$value['type']]:'';
                //$data[$key][] = date('Y-m-d H:i:s',$value['create_time']);
                $data[$key]['create_time'] = date('Y-m-d H:i:s',$value['create_time']);
             //   $data[$key][] = date('Y-m-d H:i:s',$value['create_time']);
                $data[$key]['chinese_name'] = $create_user->chinese_name;
                //$data[$key][] = UserInfo::$shiftArr[$create_user->shift];
                //$data[$key][] = (new UserInfo())->getUserName($create_user->leader_uid);
                //$data[$key][] = $value['rejects'];
            }
        }
        // $fileName = $name . time() . '.xlsx';
        // $header = [
        //     '编号',//number
        //     '问题描述',
        //     '工序',//process
        //     '设备编号',//facility->number
        //     '需反馈人员',//head_user
        //     '区域',//area
        //     '解决状态',//status
        //     '类型',
        //     '创建日期',//create_time
        //     '创建人',//create_user
        //     //'班别',//shif
        //     //'主管带班',//leader_uid
        //     //'次品数量',//rejects
        // ];
        // array_unshift($data, $header); //添加头部标题
        // return $this->json(200, $fileName, $data);
        return $data;
    }



    /**
     * Finds the BossMaintain model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BossMaintain the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BossMaintain::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
