<?php

namespace backend\controllers;

use backend\models\search\ShelfSearch;
use backend\models\Shelf;
use Yii;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class ShelfController extends BaseController
{

    /**
     * 货架列表
     * @return string
     */
    public function actionIndex()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new ShelfSearch();
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
     * 添加数据
     */
    public function actionCreate()
    {
        if (!Yii::$app->request->isPost) {
            return $this->render('create');
        }
        $post                 = Yii::$app->request->post();
        $post['shelf_number'] = Shelf::$markArr[$post['mark']] . '-' . $post['number'];
        $check                = Shelf::findOne(['shelf_number' => $post['shelf_number']]);
        if ($check) {
            return $this->json(100, '货架编号已存在!');
        }
        $shelf = (new Shelf())->create($post);
        if (!$shelf) {
            return $this->json(100, '添加失败!');
        }
        return $this->json(200, '添加成功!');
    }

    /**
     * 修改货架
     * @return string
     */
    public function actionUpdate()
    {
        if (Yii::$app->request->isPost) {
            $post                = Yii::$app->request->post();
            $model               = Shelf::findOne($post['id']);
            $model->shelf_number = Shelf::$markArr[$post['mark']] . '-' . $post['number'];
            //检查货架编号是否存在
            $check               = Shelf::findOne(['shelf_number' => $model->shelf_number]);
            if ($check) {
                return $this->json(100, '货架编号已存在!');
            }
            $model->number = trim($post['number']);
            $model->mark   = trim($post['mark']);
            //更新数据
            if (!$model->save()) {
                return $this->json(100, '修改失败!');
            }
            return $this->json(200, '修改成功!');
        }
        $id     = Yii::$app->request->get('id');
        $model  = Shelf::findOne($id);
        return $this->render('update', ['model' => $model]);
    }

    /**
     * 货架展示
     * @return string
     */
    public function actionShow()
    {
        //获取货架状态
        $result = (new Shelf())->getStatusNumber();
        $dataProvider = [];
        if(!empty($result)){
            $dataProvider = array_chunk($result, 5, true);
        }
        return $this->render('show', ['dataProvider' => $dataProvider]);
    }
}
