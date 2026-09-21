<?php

namespace backend\controllers;

use backend\models\TrainCoursesType;
use Yii;
use backend\models\TrainCourses;
use backend\models\search\TrainCoursesSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;


/**
 * TrainCoursesController implements the CRUD actions for TrainCourses model.
 */
class TrainCoursesController extends BaseController
{
    /**
     * Lists all TrainCourses models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrainCoursesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $coursesType = (new TrainCoursesType())->typeDropDownList();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'coursesType' =>$coursesType
        ]);
    }

    /**
     * Displays a single TrainCourses model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $model->setFilePathById();
        $coursesType = (new TrainCoursesType())->typeDropDownList();

        return $this->render('view', [
            'model' => $model,
            'coursesType' => $coursesType
        ]);
    }

    /**
     * Creates a new TrainCourses model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TrainCourses();

        if ($model->load(Yii::$app->request->post())) {
            //设置视频和课件参数
            $model->setFileInfo();
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

    /**
     * Updates an existing TrainCourses model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            //设置视频和课件参数
            $model->setFileInfo();
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
     * Deletes an existing TrainCourses model.
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

    public function getFileId($data){
        $data = json_decode($data,1);
        if(is_array($data) && count($data) > 0){

            return implode(',', array_keys(ArrayHelper::index($data,'id')) );
        }
        return false;
    }

    public function actionSearchSelect($keyword = null){
        $searchModel = new TrainCoursesSearch();
        $data = $searchModel->searchByName($keyword);
        $result = [
            'code' => 1,
            'msg' => '没有数据',
            'results' => []
        ];
        if($data){
            $result['results'] =  $data ;
        }else{
            $result['code'] = 1;
            $result['msg'] = '搜索的内容不存在';

        }
        return json_encode($result);
    }

    /**
    * Finds the TrainCourses model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrainCourses the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrainCourses::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
