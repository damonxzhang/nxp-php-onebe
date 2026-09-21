<?php
namespace backend\controllers;


use backend\models\Blog;
use backend\models\BlogShow;

class LogShowController extends BaseController{

    /**
     * 日志滚动展示
     * @return string
     */
    public function actionRollIndex(){

        $logList = (new BlogShow)->getShowLogInfo();
        return $this->render('roll-index',['logList' => $logList]);
    }

    /**
     * 日志展示列表
     * @return string
     */
    public function actionIndex(){
        $logList = (new BlogShow)->getShowLogInfo();

        return $this->render('index',['logList' => $logList]);
    }

    /**
     * 日志禁止显示
     * @return string
     */
    public function actionDisable(){

        $id = \Yii::$app->request->post('id');

        if(empty($id)){
            return $this->json(100,'数据错误');
        }

        $model = BlogShow::findOne(['id' => $id]);

        if(!empty($model)){
            $model->status = $model::STATUS_HIDDEN;
            if($model->save()){

               return $this->json(200,'保存成功');
            }
            else{

                return $this->json(100,$model->errorToString());
            }
        }else{

            return $this->json(100,'请求失败！请刷新后重试');
        }
    }
}