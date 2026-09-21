<?php

namespace backend\controllers;

use backend\actions\UploadAction;
use Yii;
use backend\models\TrainPpt;
use backend\models\search\TrainPptSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TrainPptController implements the CRUD actions for TrainPpt model.
 */
class TrainPptController extends BaseController
{

    public function actions()
    {
        return [
            'upload' =>[
                'class' => UploadAction::className(),
            ]
        ];
    }


    /**
     * Lists all TrainPpt models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrainPptSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrainPpt model.
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
     * Creates a new TrainPpt model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TrainPpt();
        if ($model->load(Yii::$app->request->post())) {

            $upload_file = Yii::$app->request->post('upload_file');
            $filePath = Yii::$app->request->post('file_path');
            if(!empty($filePath)){
                $filePath = str_replace('，',',',$filePath);
                $filePathArray = explode(',',$filePath);
                foreach ($filePathArray as $value){

                    $upload_file[] = 'http://10.192.134.255/training_video/file/'.$value;
                }
            }

            if(!is_array($upload_file) || count($upload_file) <= 0){
                return $this->json(100,'请上传课件！');

            }
            $model->path = json_encode($upload_file);
            $model->create_user = Yii::$app->user->identity->user_id;
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
     * Updates an existing TrainPpt model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $upload_file = Yii::$app->request->post('upload_file');
            $filePath = Yii::$app->request->post('file_path');
            if(!empty($filePath)){
                $filePath = str_replace('，',',',$filePath);
                $filePathArray = explode(',',$filePath);
                foreach ($filePathArray as $value){

                    $upload_file[] = 'http://10.192.134.255/training_video/file/'.$value;
                }
            }

            if(!is_array($upload_file) || count($upload_file) <= 0){
                return $this->json(100,'请上传课件！');

            }
            $model->path = json_encode($upload_file);
            if($model->save()){
                return $this->json(200,'保存成功！');
            }else{
                return $this->json(100,$model->errorToString());
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionSearchPpt(){
        $name = Yii::$app->request->get('name');
        $id = Yii::$app->request->get('id');
        if(!empty($id)){
            $ids = explode(',',$id);
        }else{
            $ids=false;
        }
        $data = (new TrainPptSearch())->getPptListByArray($ids,$name);

        if(!empty($id)){
            $data = $this->setLayuiTableSelect($ids,$data);
        }
        return json_encode(['code' => 0,'count' => count($data),'data' => $data]);

    }

    /**
     * Deletes an existing TrainPpt model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete_time = time();
        if($model->save()){

            return $this->redirect(['index']);
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Finds the TrainPpt model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrainPpt the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrainPpt::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
