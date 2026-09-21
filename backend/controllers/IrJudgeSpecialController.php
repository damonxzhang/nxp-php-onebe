<?php

namespace backend\controllers;

use Yii;
use backend\models\IrJudgeSpecial;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * IrJudgeSpecialController implements the CRUD actions for IrJudgeSpecial model.
 */
class IrJudgeSpecialController extends BaseController
{

    /**
     * Lists all IrJudgeSpecial models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new IrJudgeSpecial();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new IrJudgeSpecial model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new IrJudgeSpecial();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect( 'index' );
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing IrJudgeSpecial model.
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
        if($model->status == IrJudgeSpecial::STATUS_OFF){
            $model->status = IrJudgeSpecial::STATUS_ON;
        }else{
            $model->status = IrJudgeSpecial::STATUS_OFF;
        }
        if($model->save()){
            return $this->json(200,'保存成功');
        }else{
            return $this->json(100,$model->errorToString());
        }
    }

    /**
     * Deletes an existing IrJudgeSpecial model.
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
     * Finds the IrJudgeSpecial model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return IrJudgeSpecial the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = IrJudgeSpecial::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
