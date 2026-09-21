<?php

namespace backend\controllers;

use Yii;
use backend\models\MachineModel;
use backend\models\search\MachineModelSearch;
use backend\controllers\BaseController;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use backend\models\MachineVendor;
use backend\models\MachineModelVendor;


/**
 * MachineModelController implements the CRUD actions for MachineModel model.
 */
class MachineModelController extends BaseController
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
     * Lists all MachineModel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new MachineModelSearch();
        $data         = $searchModel->search($request);
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
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $post['createby'] = $this->getUid();
            $model = new MachineModel();
            $res = $model->create($post);
            if ($res['status'] != 200) {

                return $this->json(100, $res['msg']);
            }
         //   return $this->json(200, $res['msg']);
		 
		 
                $query_m = new MachineModelVendor();
                $query_m->vendor_id = $post['vendorId'];
                $query_m->model_id = $res['msg'];
                $query_m->save();

            return $this->json(200, '添加成功');
        }
        $query = MachineVendor::find();
        $query->andWhere(['status'=>1]);
        $dataProvider = $query->asArray()->all();

        return $this->render('create',[
            'dataProvider' => $dataProvider,
        ]);    }

    /**
     * @return string
     */
    public function actionUpdate()
    {
        $id = Yii::$app->request->get('id');
        $model = MachineModel::findOne($id);
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $model = new MachineModel();
            $check = $model->check($post);
            if(!$check){
                return $this->json(100, '机型已存在');
            }
            $res = $model->edit($post);
            if ($res['status'] != 200) {
                return $this->json(100, $res['msg']);
            }
			
			
            $check_vendor = MachineModelVendor::find();
            $check_vendor->andWhere(['model_id'=>$post['id']]);
            $data_check_vendor = $check_vendor->asArray()->all();
            if ($data_check_vendor){
                foreach ($data_check_vendor as $key => $value){
					$update_vendor = MachineModelVendor::findOne($value['id']);
                    $update_vendor->vendor_id = $post['vendorId'];
                    $update_vendor->save();
                }

            }else{
                $create_vendor = new MachineModelVendor();
                $create_vendor->model_id = $post['id'];
                $create_vendor->vendor_id = $post['vendorId'];
                $create_vendor->save();
            }


            return $this->json(200, $res['msg']);
        }
      //  return $this->render('update', compact('model'));
	  
        $query = MachineVendor::find();
        $query->andWhere(['status'=>1]);
        $dataProvider = $query->asArray()->all();

        $query_check = MachineModelVendor::find();
        $query_check->andWhere(['model_id'=>$id]);
        $query_check->limit(1);
        $data_query = $query_check->asArray()->all();

        if ($data_query){
            $data_check = $data_query[0]['vendor_id'];
        }else{
            $data_check = '';
        }


        return $this->render('update',[
            'model'=>$model,
            'dataProvider' => $dataProvider,
            'data_check' => $data_check,

        ]);
    }

    public function actionSearchByName($area = null){
        $name = Yii::$app->request->get('keyword');
        $result = [
            'code' => 1,
            'msg' => '没有数据',
            'results' => []
        ];

        $query = MachineModel::find()->select('id,name as text');
        if($name){
            $query->where(['like','name',$name]);
        }
        $query->andWhere(['=','status',1]);
        $params = $query->asArray()->all();
        if($params){
            $result['code'] = 0;
            $result['msg'] = 'success';
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
            $query = MachineModel::find();
            $query->where(['in', 'id', $idList]);
            $query->andFilterWhere(['status' => $status]);
            $query->asArray();
            $dataProvider = $query->all();
        } else {
            $query = MachineModel::find();
            $query->asArray();
            $dataProvider = $query->all();
        }
        $data = [];
        if (!empty($dataProvider)) {
            foreach ($dataProvider as $key => $value) {
                $data[$key][] = $value['id'];
                $data[$key][] = $value['name'];
                $data[$key][] = $value['status']?'启用':'禁用';
            }
        }
        $fileName = '设备型号_' . time() . '.xlsx';
        $header   = ['ID', '设备型号', '状态'];
        array_unshift($data, $header); //添加头部标题
        return $this->json(200, $fileName, $data);
    }
   
}
