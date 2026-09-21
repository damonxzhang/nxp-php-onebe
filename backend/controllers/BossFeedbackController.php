<?php

namespace backend\controllers;

use backend\models\BossMaintain;
use backend\models\BossMaintainLeader;
use backend\models\Facility;
use backend\models\UserInfo;
use backend\modules\MaintainApi\models\SendEmail;
use Yii;
use backend\models\BossFeedback;
use backend\models\search\BossFeedbackSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * BossFeedbackController implements the CRUD actions for BossFeedback model.
 */
class BossFeedbackController extends BaseController
{

    /**
     * Lists all BossFeedback models.
     * @return mixed
     */
    public function actionIndex($id)
    {
        $searchModel = new BossFeedbackSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$id);
        $maintain = BossMaintain::findOne($id);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'maintain' => $maintain
        ]);
    }

    /**
     * Displays a single BossFeedback model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = BossFeedback::findOne($id);
        if(empty($model)){
            throw new NotFoundHttpException('page is not found');
        }
        $maintainModel = BossMaintain::findOne($model->m_id);
        $userInfo = UserInfo::findOne($maintainModel->create_user);
        $facility = (new Facility())->findFacilityInfo($maintainModel->f_id);

        return $this->render('view', [
            'model' => $model,
            'maintainModel' =>$maintainModel,
            'facility' =>$facility,
            'userInfo' =>$userInfo,
        ]);
    }

    /**
     * Creates a new BossFeedback model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        $maintainModel = BossMaintain::findOne($id);
        $feedbackModel = $this->findModel($id);
        if ($feedbackModel->load(Yii::$app->request->post())) {
            if( $feedbackModel->getFeedbackType($maintainModel) &&
                $feedbackModel->newFeedback($id) &&
                $maintainModel->changeMaintain($feedbackModel->rejects)){
                return $this->json(200,'保存成功！');
            }else{
                return $this->json(100,$feedbackModel->errorToString());
            }
        }
        $feedbackModel->rejects = $maintainModel->rejects;
        $userInfo = UserInfo::findOne($maintainModel->create_user);
        $feedbackModel->create_time = !empty($feedbackModel->create_time) ? date('Y-m-d H:i:s',$feedbackModel->create_time) : date('Y-m-d H:i:s');
        !empty($feedbackModel->predict_date) && $feedbackModel->predict_date = date('Y-m-d',$feedbackModel->predict_date);
        !empty($feedbackModel->complete_date) && $feedbackModel->complete_date = date('Y-m-d',$feedbackModel->complete_date);
        return $this->render('create', [
            'maintainModel' => $maintainModel,
            'feedbackModel' => $feedbackModel,
            'userInfo' => $userInfo,
        ]);
    }

    /**
     * Updates an existing BossFeedback model.
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
     * Finds the BossFeedback model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BossFeedback the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($m_id)
    {

        if (($model = BossFeedback::findOne(['m_id' => $m_id,'feedback_user' => Yii::$app->user->identity->user_id,'solve_status' => BossFeedback::SOLVE_STATUS_NO])) !== null) {
            return $model;
        }
        return new BossFeedback();
    }
}
