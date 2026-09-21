<?php

namespace backend\controllers;

use backend\models\search\ShelfScatteredSearch;
use backend\models\Shelf;
use backend\models\ShelfScattered;
use backend\models\ShelfMaterials;
use Yii;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class ShelfScatteredController extends BaseController
{

    /**
     * 货架列表
     * @return string
     */
    public function actionIndex()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new ShelfScatteredSearch();
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
        $userInfo = Yii::$app->user->identity->userInfo;

        // print_r(($userInfo->pid));die;

        if(!$this->checkrule($userInfo)){
            echo $this->json(300, '抱歉，没有权限');die;
        }


        if (!Yii::$app->request->isPost) {
            return $this->render('create');
        }
        $post                 = Yii::$app->request->post();
        // print_r(($post));die;
        $post['count'] = number_format($post['count'],2);
        $shelf = (new ShelfScattered())->create($post);
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
        $userInfo = Yii::$app->user->identity->userInfo;

        // print_r(($userInfo->pid));die;

        if(!$this->checkrule($userInfo)){
            echo $this->json(300, '抱歉，没有权限');die;
        }

        if (Yii::$app->request->isPost) {
            $post                           = Yii::$app->request->post();
            $post['count'] = number_format($post['count'],2);
            $model                          = ShelfScattered::findOne($post['id']);
            // $model->part_no                 = trim($post['part_no']);
            $model->count                   = trim($post['count']);
            $model->scattered_time          = $post['scattered_time'];

            //更新数据
            if (!$model->save()) {
                return $this->json(100, '修改失败!');
            }
            return $this->json(200, '修改成功!');
        }
        $id     = Yii::$app->request->get('id');
        $model  = ShelfScattered::findOne($id);
        return $this->render('update', ['model' => $model]);
    }

    /**
     * 检查当前用户权限
     */
    public function checkrule($userInfo)
    {
        // $passID = ['91008723','12060498','12055248','91002672'];
        $passID = [91008723,12060498,123,12055248,91002672];

        if(in_array($userInfo->pid,$passID)){
            return 1;
        }else{
            return 0;
        }
    }


}
