<?php

namespace backend\controllers;

use Yii;
use backend\models\Judge;
use backend\models\search\JudgeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * JudgeController implements the CRUD actions for Judge model.
 */
class JudgeController extends BaseController
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
     * Lists all Judge models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new JudgeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Judge model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Judge();

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

    public function actionUpdateStatus(){
        $model = $this->findModel(Yii::$app->request->post('id'));
        if($model->status == Judge::STATUS_YES){
            $model->status = Judge::STATUS_NO;
        }else{
            $model->status = Judge::STATUS_YES;
        }
        if($model->save()){
            return $this->json(200,'保存成功');
        }else{
            return $this->json(100,$model->errorToString());
        }
    }

    /**
     * Updates an existing Judge model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
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

    /**
     * Deletes an existing Judge model.
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
     * Finds the Judge model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Judge the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Judge::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
