<?php

namespace backend\controllers;

use backend\models\ExternalAuthority;
use backend\models\ExternalSystem;
use backend\models\UserInfo;
use Yii;
use backend\models\ExternalRelation;
use backend\models\search\ExternalRelationSearch;
use yii\db\Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ExternalRelationController implements the CRUD actions for ExternalRelation model.
 */
class ExternalRelationController extends BaseController
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
     * Lists all ExternalRelation models.
     * @return mixed
     */
    public function actionIndex($pid)
    {
        $searchModel = new ExternalRelationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$pid);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ExternalRelation model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('index', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ExternalRelation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ExternalRelation();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ExternalRelation model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($pid)
    {
       
        $model = new ExternalRelation();
        $userIdStr = $model->getUserIdByAuthority($pid);
        $status = $model->getStatus($pid)->status;
        $authorityModel = ExternalAuthority::findOne($pid);

        $connectUser = UserInfo::findAll(['status' => UserInfo::STATUS_OPEN]);

        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            $tra = Yii::$app->db->beginTransaction();

            try{

                if(false === $model->deleteAll(['authority_id' => $pid])){
                    throw new Exception('服务器错误！');
                }
                $userIds = explode(',',$post['user_id']);

                if(!empty($userIds) && !empty($userIds[0]) ){
                    $params = [];
                    $time = time();
                    foreach ($userIds as $id){
                        $params[] = [
                            'system_id' => $authorityModel->system_id,
                            'authority_id' => $authorityModel->id,
                            'user_id' => $id,
                            'status' => $post['status'],
                            'create_time' => $time
                        ];
                    }
                    $result = Yii::$app->db->createCommand()->batchInsert($model::tableName(),['system_id','authority_id','user_id','status','create_time'],$params)->execute();
                    if($result != count($userIds)){
                        throw new Exception('保存出错！');
                    }
                }
                $tra->commit();
                return $this->redirect(['/external-authority/index','pid' => $authorityModel->system_id]);
            }catch (\Exception $e){
                $tra->rollBack();
                return $this->json(100,$e->getMessage());
            }

        }

        return $this->render('update', [
            'model' => $model,
            'status' => $status,
            'connectUser' => $connectUser,
            'userIdStr' => $userIdStr,
            'systemId' => $authorityModel->system_id,
        ]);
    }

    /**
     * Deletes an existing ExternalRelation model.
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
     * Finds the ExternalRelation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ExternalRelation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ExternalRelation::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
