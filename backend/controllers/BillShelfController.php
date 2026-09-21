<?php

namespace backend\controllers;

use backend\models\search\BillShelfSearch;
use backend\models\BillShelf;
use Yii;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class BillShelfController extends BaseController
{

    /**
     * 货架列表
     * @return string
     */
    public function actionIndex()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new BillShelfSearch();
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
        $check                = BillShelf::findOne(['shelf_number' => trim($post['shelf_number']),'mark' => $post['mark']]);
        if ($check) {
            return $this->json(100, '货架编号已存在!');
        }
        $shelf = (new BillShelf())->create($post);
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
            $post     = Yii::$app->request->post();
            $model    = BillShelf::findOne($post['id']);
            $cont     = BillShelf::find()->where(['shelf_number' => $post['shelf_number']])->andWhere(['!=','id', $post['id']])->count();
            //如果存在  则提示
            if ($cont > 0) {
                return $this->json(100, '货架编号已存在!');
            }
            $model->shelf_number   = trim($post['shelf_number']);
            //更新数据
            if (!$model->save(false)) {
                return $this->json(100, '修改失败!');
            }
            return $this->json(200, '修改成功!');
        }
        $id     = Yii::$app->request->get('id');
        $model  = BillShelf::findOne($id);
        return $this->render('update', ['model' => $model]);
    }

  
}
