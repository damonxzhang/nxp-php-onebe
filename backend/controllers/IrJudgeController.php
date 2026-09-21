<?php

namespace backend\controllers;

use Yii;
use backend\models\IrJudge;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * IrJudgeController implements the CRUD actions for IrJudge model.
 */
class IrJudgeController extends BaseController
{

    /**
     * Lists all IrJudge models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new IrJudge();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new IrJudge model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new IrJudge();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect( 'index' );
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing IrJudge model.
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

    public function actionUpdateStatus(){
        $model = $this->findModel(Yii::$app->request->post('id'));
        if($model->status == IrJudge::STATUS_OFF){
            $model->status = IrJudge::STATUS_ON;
        }else{
            $model->status = IrJudge::STATUS_OFF;
        }
        if($model->save()){
            return $this->json(200,'保存成功');
        }else{
            return $this->json(100,$model->errorToString());
        }
    }

    /**
     * Deletes an existing IrJudge model.
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
     * Finds the IrJudge model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return IrJudge the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = IrJudge::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
