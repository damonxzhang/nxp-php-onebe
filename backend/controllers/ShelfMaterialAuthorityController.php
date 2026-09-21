<?php

namespace backend\controllers;

use backend\models\search\ShelfSearch;
use Yii;
use backend\models\ShelfMaterialAuthority;
use backend\models\search\ShelfMaterialAuthoritySearch;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ShelfMaterialAuthorityController implements the CRUD actions for shelf-material-authority model.
 */
class ShelfMaterialAuthorityController extends Controller
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
     * Lists all shelf-material-authority models.
     * @return mixed
     */
    public function actionIndex()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new ShelfMaterialAuthoritySearch();
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
     * Displays a single shelf-material-authority model.
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
     * Creates a new shelf-material-authority model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ShelfMaterialAuthority();
        $model->status = ShelfMaterialAuthority::STATUS_TRIAL;
        $model->authority = ShelfMaterialAuthority::AUTHORITY_ACTION;
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if (empty($post['ShelfMaterialAuthority']['user_id'])){
                return json_encode([
                    'msg' => '请选择操作员',
                    'status' => false
                ]);
            }
            $model->user_id = $post['ShelfMaterialAuthority']['user_id'];
            if(ShelfMaterialAuthority::find()->where(['user_id' => $model->user_id])->exists()){
                return json_encode([
                    'msg' => '操作员已存在',
                    'status' => false
                ]);
            }
            $model->save();
            return json_encode([
                'msg' => '操作成功',
                'status' => 200
            ]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }


    /**
     * Deletes an existing shelf-material-authority model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDel($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the shelf-material-authority model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ShelfMaterialAuthority the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ShelfMaterialAuthority::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
