<?php

namespace backend\controllers;

use backend\models\Facility;
use backend\models\Maintain;
use backend\models\MaintainType;
use backend\models\service\MaintainService;
use backend\models\UserInfo;
use Yii;
use backend\models\MaintainFeedback;
use backend\models\search\MaintainFeedbackSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MaintainFeedbackController implements the CRUD actions for MaintainFeedback model.
 */
class MaintainFeedbackController extends BaseController
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
     * Lists all MaintainFeedback models.
     * @return mixed
     */
    public function actionIndex($id)
    {
        $searchModel = new MaintainFeedbackSearch();
        $dataProvider = $searchModel->search($id);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MaintainFeedback model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = MaintainFeedback::findOne($id);
        if(empty($model)){
            throw new NotFoundHttpException('page is not found');
        }
        $maintainModel = Maintain::findOne($model->m_id);
        $typeList = MaintainType::getTypeList();
        $userInfo = UserInfo::findOne($maintainModel->create_user);
        $facility = (new Facility)->findFacilityInfo($maintainModel->f_id);

        return $this->render('view', [
            'model' => $model,
            'maintainModel' =>$maintainModel,
            'facility' =>$facility,
            'userInfo' =>$userInfo,
            'typeList' =>$typeList,
        ]);
    }

    /**
     * Creates a new MaintainFeedback model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        $maintainModel = MaintainService::findOne($id);
        if(!$maintainModel){
            throw new NotFoundHttpException("page is not found");
        }
        $feedbackModel = MaintainFeedback::getCreateModel($maintainModel);

        if ($feedbackModel->load(Yii::$app->request->post())) {

            if (empty($maintainModel->access_status) || $maintainModel->access_status == Maintain::SELECT_NO) {
                return $this->json(100, '审核未通过！');
            }

            if ($feedbackModel->saveFeedback($maintainModel->id)) {

                if(!$maintainModel->updateMaintain($feedbackModel)){
                    return $this->json(100, $maintainModel->errorToString());
                }
                return $this->json(200, '保存成功！');
            } else {
                return $this->json(100, $feedbackModel->errorToString());
            }
        }
        $feedbackModel->replaceTime();
        $feedbackModel->rejects = $maintainModel->rejects;
        $feedbackModel->feedback_type = $maintainModel->type;
        $userInfo = UserInfo::findOne($maintainModel->create_user);
        $facility = (new Facility())->findFacilityInfo($maintainModel->f_id);

        return $this->render('create', [
            'maintainModel' => $maintainModel,
            'feedbackModel' => $feedbackModel,
            'userInfo' => $userInfo,
            'facility' => $facility
        ]);
    }

    /**
     * Updates an existing MaintainFeedback model.
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
     * Deletes an existing MaintainFeedback model.
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
     * Finds the MaintainFeedback model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MaintainFeedback the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($m_id)
    {
        if (($model = MaintainFeedback::findOne(['m_id' => $m_id,'feedback_user' => Yii::$app->user->identity->user_id])) !== null) {
            return $model;
        }
        return new MaintainFeedback();
    }
}
