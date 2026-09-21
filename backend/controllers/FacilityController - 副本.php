<?php

namespace backend\controllers;

use Yii;
use backend\models\Facility;
use backend\models\search\FacilitySearch;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class FacilityController extends BaseController
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
     * Lists all Facility models.
     * @return mixed
     */
    public function actionIndex()
    {
        $request = Yii::$app->request->queryParams;
        $searchModel = new FacilitySearch();
        $data = $searchModel->search($request);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        return $this->render('index', [
            'request'      => $request,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination
        ]);
    }

    /**
     * @return string
     */
    public function actionCreate()
    {
        $model = new Facility();
        if ($model->load(Yii::$app->request->post()) ) {
            $model->BUY_OFF_DATE = strtotime($model->BUY_OFF_DATE);
            $model->CBA_pass_date = strtotime($model->CBA_pass_date);
            if($model->save()){
                return $this->json(200,'保存成功！');
            }else{
                return $this->json(100,$model->errorToString());
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) ) {
            $model->BUY_OFF_DATE = strtotime($model->BUY_OFF_DATE);
            $model->CBA_pass_date = strtotime($model->CBA_pass_date);
            if($model->save()){
                return $this->json(200,'保存成功！');
            }else{
                return $this->json(100,$model->errorToString());
            }
        }
        $model->BUY_OFF_DATE = date('Y-m-d',$model->BUY_OFF_DATE);
        $model->CBA_pass_date = date('Y-m-d',$model->CBA_pass_date);
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * 查询设备及设备管理员
     * @return string
     */
    public function actionGetFacility(){
        $number = Yii::$app->request->post('number');
        if(empty($number)){
            return $this->json(100,'缺少参数！');
        }
        $number = trim($number,' ');
        $model = new FacilitySearch();
        $facility = $model->findFacilityUserInfo($number);
        if(!empty($facility)){
            return $this->json(200,'查询成功！',$facility);
        }else{
           return $this->json(100,'设备不存在!');
        }
    }

    public function actionFindFacility(){
        $name = Yii::$app->request->get('keyword');
        $result = [
            'code' => 1,
            'msg' => '没有数据',
            'results' => []
        ];
        $query = Facility::find()->select('id,number as text');
        if($name){
            $query->where(['like','number',$name]);
        }
        $params = $query->limit(20)->asArray()->all();
        if($params){
            $result['code'] = 0;
            $result['msg'] = 'success';
            $result['results'] = $params;
        }
        return json_encode($result);
    }

    /**
     * @param $id
     * @return Facility|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Facility::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionDetail()
    {
        $id    = Yii::$app->request->get('id');
        $model = Facility::findOne($id);
        return $this->render('detail', [
            'model'       => $model,
        ]);
    }

    /**
     * @return false|string
     */
    public function actionSearchFacility()
    {
        $request = Yii::$app->request->post();
        $result  = [
            'code'    => 1,
            'msg'     => '没有数据',
            'results' => []
        ];
        $params  = Facility::getFacility($request);
        if (!empty($params)) {
            $result['code']    = 0;
            $result['msg']     = 'success';
            $result['results'] = $params;
        }
        return json_encode($result);
    }
}
