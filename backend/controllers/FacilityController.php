<?php

namespace backend\controllers;

use Yii;
use backend\models\Facility;
use backend\models\search\FacilitySearch;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\MachineProcess;
use backend\models\MachineModel;
use backend\models\MachineVendor;
use yii\helpers\ArrayHelper;
use backend\models\UserInfo;


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
        // print_r($request);die;
        $data = $searchModel->searchnew($request);
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
        
        $relevant_people_input = Yii::$app->request->post('relevant_people');
        $relevant_people = '';
        if (!empty($relevant_people_input)){

            foreach ($relevant_people_input as $key=>$value){
                $relevant_people .= $value . ',';
            }
            $relevant_people = substr($relevant_people,0,strlen($relevant_people)-1);
//            print_r($relevant_people);die;
        }


        $model = new Facility();
        if ($model->load(Yii::$app->request->post()) ) {
            $model->BUY_OFF_DATE = strtotime($model->BUY_OFF_DATE);
            $model->CBA_pass_date = strtotime($model->CBA_pass_date);
            $model->relevant_people = $relevant_people;

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
        
        $relevant_people_input = Yii::$app->request->post('relevant_people');
        $relevant_people = '';
        if (!empty($relevant_people_input)){

            foreach ($relevant_people_input as $key=>$value){
                $relevant_people .= $value . ',';
            }
            $relevant_people = substr($relevant_people,0,strlen($relevant_people)-1);
//            print_r($relevant_people);die;
        }


        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) ) {
            $model->BUY_OFF_DATE = strtotime($model->BUY_OFF_DATE);
            $model->CBA_pass_date = strtotime($model->CBA_pass_date);
            $model->relevant_people = $relevant_people;
            if($model->save()){
                return $this->json(200,'保存成功！');
            }else{
                return $this->json(100,$model->errorToString());
            }
        }
        $model->BUY_OFF_DATE = date('Y-m-d',$model->BUY_OFF_DATE);
        $model->CBA_pass_date = date('Y-m-d',$model->CBA_pass_date);
        
        $acceptUserArr = (new Facility())->getAcceptUserId($id);
        $acceptUserArr = explode(',',$acceptUserArr[0]);

//        print_r($acceptUserArr);die;
        if ($acceptUserArr[0] != '')
            $model->relevant_people = $acceptUserArr;

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

    
    /**
     * 导出数据
     * @return string
     */
    public function actionExport()
    {
        $request = Yii::$app->request->queryParams;
        $idList  = ArrayHelper::getValue($request, 'idlist', []);
        $status  = ArrayHelper::getValue($request, 'status', '');
        if (!empty($idList)) {
            $query = Facility::find();
            $query->where(['in', 'id', $idList]);
            $query->andFilterWhere(['status' => $status]);
            $query->asArray();
            $dataProvider = $query->all();
        } else {
            $query = Facility::find();
            $query->asArray();
            $dataProvider = $query->all();
        }
        $data = [];
        if (!empty($dataProvider)) {
            foreach ($dataProvider as $key => $value) {
                // print_r($value);die;
                $data[$key][] = $value['id'];
                $data[$key][] = $value['name'];
                $data[$key][] = $value['number'];
                $data[$key][] = MachineProcess::getNameById($value['process']);
                $data[$key][] = MachineModel::getNameById($value['model']);
                $data[$key][] = ArrayHelper::getValue(UserInfo::$areaArr, $value['area'], '');
                $data[$key][] = $value['serial_no'];
                $data[$key][] = MachineVendor::getNameById($value['vendor_id']);
                $data[$key][] = $value['ASSET_NO'];
                $data[$key][] = date('Y-m-d',$value['BUY_OFF_DATE']);
                $data[$key][] = date('Y-m-d',$value['CBA_pass_date']);
                $data[$key][] = UserInfo::getOneNameById($value['head_user']);
                $data[$key][] = $value['ip'];
                $data[$key][] = $value['remark'];
                $data[$key][] = $value['status']?'启用':'禁用';
            }
        }
        $fileName = '设备列表_' . time() . '.xlsx';
        $header   = ['ID', '设备名称', '机器编号', '工序','型号', '区域', 'SERIAL NO', 'VENDOR', 'ASSET NO','BUY_OFF_DATE','CBA_pass_date','负责人', 'IP','备注','状态'];
        array_unshift($data, $header); //添加头部标题
        return $this->json(200, $fileName, $data);
    }
}
