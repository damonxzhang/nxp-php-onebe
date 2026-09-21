<?php

namespace backend\controllers;

use Yii;
use backend\models\HandoverUsers;
use backend\models\search\HandoverUsersSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * HandoverUsersController implements the CRUD actions for HandoverUsers model.
 */
class HandoverUsersController extends BaseController
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
     * Lists all HandoverUsers models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new HandoverUsersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single HandoverUsers model.
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
     * Creates a new HandoverUsers model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new HandoverUsers();

        if ($model->load(Yii::$app->request->post())) {
            if(empty($model->user_id)){
                return $this->json(10,'用户不能为空');
            }
            $ids = explode(',',$model->user_id);
            $existIds = $model->getExistUserId($ids);
            $params = [];
            foreach ($ids as $val){
                if(!in_array($val,$existIds)){
                   $params[] = [
                       'user_id' => $val,
                       'create_time' => time()
                   ] ;
                }
            }

            $res = Yii::$app->db->createCommand()->batchInsert($model::tableName(),['user_id','create_time'],$params)->execute();
            if($res){
                return $this->json(200,'保存成功！');
            }else{
                return $this->json(100,'保存失败！请稍后重试！');
            }

        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing HandoverUsers model.
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
     * Deletes an existing HandoverUsers model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete()
    {
        if(Yii::$app->request->isPost){
            $id = Yii::$app->request->post('id');
            if($this->findModel($id)->delete()){
                return $this->json(200,'删除成功！');
            }else{
                return $this->json(100,'删除失败！');
            }

        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the HandoverUsers model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return HandoverUsers the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = HandoverUsers::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
