<?php

namespace backend\controllers;

use Yii;
use backend\models\TrainFileType;
use backend\models\search\TrainFileType as TrainFileTypeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TrainFileTypeController implements the CRUD actions for TrainFileType model.
 */
class TrainFileTypeController extends BaseController
{

    /**
     * Lists all TrainFileType models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrainFileTypeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new TrainFileType model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TrainFileType();


        if ($model->load(Yii::$app->request->post())) {
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

    public function actionUpdate($id){
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
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

    public function actionUpdateStatus($id){
        $model = $this->findModel($id);
        if($model->status == TrainFileType::STATUS_YES){
            $model->status = TrainFileType::STATUS_NO;
        }else{
            $model->status = TrainFileType::STATUS_YES;
        }
        if($model->save()){
            return $this->json(200,'保存成功！');
        }
        return $this->json(100,$model->errorToString());
    }

    /**
     * Finds the TrainFileType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrainFileType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrainFileType::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
