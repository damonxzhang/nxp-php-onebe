<?php
namespace backend\controllers;

use backend\models\BossMaintainLeader;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class BossMaintainLeaderController extends BaseController{

    /**
     * 列表
     * @return string
     */
    public function actionIndex(){
        $query = BossMaintainLeader::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        return $this->render('index',['dataProvider' => $dataProvider]);
    }

    /**
     * 新增
     * @return string|\yii\web\Response
     */
    public function actionCreate(){
        $model = new BossMaintainLeader();
        if($model->load(\Yii::$app->request->post()) && $model->save()){
            return $this->redirect('index');
        }
        return $this->render('create',['model' => $model ]);
    }

    /**
     * 删除
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id){
        $this->findModel($id)->delete();

        return $this->redirect('index');

    }

    public function findModel($id){
        if(($model = BossMaintainLeader::findOne( $id )) !== false){
            return $model;
        }
        throw new NotFoundHttpException('page is not found');
    }

}