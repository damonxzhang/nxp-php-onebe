<?php
namespace backend\controllers;

use backend\models\search\UserStationSearch;
use backend\models\UserStation;
use Yii;
use yii\data\Pagination;

class UserStationController extends BaseController
{

    /**
     * 站别列表
     * @return string
     */
    public function actionIndex()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new UserStationSearch();
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
     * 添加站别
     * @return string
     */
    public function actionCreate()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $model = new UserStation();
            $res = $model->create($post);
            if ($res['status'] != 200) {
                return $this->json(100, $res['msg']);
            }
            return $this->json(200, $res['msg']);
        }
        return $this->render('create');
    }

    /**
     * 修改站别
     * @return string
     * @throws \yii\base\Exception
     */
    public function actionUpdate()
    {
        $id = Yii::$app->request->get('id');
        $model = UserStation::findOne($id);
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $model = new UserStation();
            $check = $model->check($post);
            if(!$check){
                return $this->json(100, '站别名称已存在');
            }
            $res = $model->edit($post);
            if ($res['status'] != 200) {
                return $this->json(100, $res['msg']);
            }
            return $this->json(200, $res['msg']);
        }
        return $this->render('update', compact('model'));
    }

}
