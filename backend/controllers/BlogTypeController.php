<?php
namespace backend\controllers;

use backend\models\BlogType;
use backend\models\search\BlogTypeSearch;
use backend\models\search\UserStationSearch;
use backend\models\UserStation;
use Yii;
use yii\data\Pagination;

class BlogTypeController extends BaseController
{

    /**
     * 交接类型列表
     * @return string
     */
    public function actionIndex()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new BlogTypeSearch();
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
     * 添加交接类型
     * @return string
     */
    public function actionCreate()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $model = new BlogType();
            $res = $model->create($post);
            if ($res['status'] != 200) {
                return $this->json(100, $res['msg']);
            }
            return $this->json(200, $res['msg']);
        }
        return $this->render('create');
    }

    /**
     * 修改交接类型
     * @return string
     * @throws \yii\base\Exception
     */
    public function actionUpdate()
    {
        $id = Yii::$app->request->get('id');
        $model = BlogType::findOne($id);
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $model = new BlogType();
            $check = $model->check($post);
            if(!$check){
                return $this->json(100, '交接类型名称已存在');
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
