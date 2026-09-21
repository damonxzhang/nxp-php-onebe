<?php

namespace backend\controllers;

use Yii;
use backend\models\ExternalAuthority;
use backend\models\search\ExternalAuthoritySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\ExternalRelation;

/**
 * ExternalAuthorityController implements the CRUD actions for ExternalAuthority model.
 */
class ExternalAuthorityController extends Controller
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
     * Lists all ExternalAuthority models.
     * @return mixed
     */
    public function actionIndex($pid)
    {
        $searchModel = new ExternalAuthoritySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$pid);
        $dataProvider->query->andWhere(['system_id' => $pid])->orderBy('status asc,id desc');
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'pid' => $pid
        ]);
    }


    /**
     * Creates a new ExternalAuthority model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($pid)
    {
        $model = new ExternalAuthority();
        $model->system_id = $pid;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
			$model1 = new ExternalRelation();
            $model1->system_id = $pid;
            $model1->authority_id = $model->id;
            $model1->user_id = 1;
            $model1->status = 1;
            $model1->save();
			
            return $this->redirect(['index', 'pid' => $model->system_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ExternalAuthority model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index',  'pid' => $model->system_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ExternalAuthority model.
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
     * Finds the ExternalAuthority model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ExternalAuthority the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ExternalAuthority::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
