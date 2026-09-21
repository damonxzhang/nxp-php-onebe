<?php

namespace backend\controllers;

use backend\models\UserInfo;
use Yii;
use backend\models\MaintainAreaHead;
use backend\models\search\MaintainAreaHeadSearch;
use backend\controllers\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MaintainAreaHeadController implements the CRUD actions for MaintainAreaHead model.
 */
class MaintainAreaHeadController extends BaseController
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
     * Lists all MaintainAreaHead models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MaintainAreaHeadSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new MaintainAreaHead model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MaintainAreaHead();

        if ($model->load(Yii::$app->request->post())) {
            $user = UserInfo::findOne($model->user_id);
            if(!empty($user)){
                if($model->save()){
                    return $this->json(200,'保存成功！');
                }else{
                    return $this->json(100,$model->errorToString());
                }
            }
            return $this->json(100,'用户不存在！');
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * 修改状态
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionChangeStatus($id){
        $model = $this->findModel($id);
        if($model->changeStatus()){
            return $this->json(200,'操作成功！');
        }else{
            return $this->json(100,$model->errorToString());
        }
    }

    /**
     * Finds the MaintainAreaHead model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MaintainAreaHead the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MaintainAreaHead::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
