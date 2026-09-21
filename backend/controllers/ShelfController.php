<?php

namespace backend\controllers;

use backend\models\search\ShelfSearch;
use backend\models\Shelf;
use backend\models\ShelfMaterials;
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

            $model->floor       = trim($post['floor']);
            $model->car_number  = trim($post['car_number']);
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
        // print_r($result);die;
        return $this->render('show', ['dataProvider' => $dataProvider]);
    }

    /**
     * 货架展示
     * @return string
     */
    public function actionShowOne()
    {
        //获取货架状态
        $result = (new Shelf())->getStatusNumberByFloorOne();
        $dataProvider = [];
        if(!empty($result)){
            $dataProvider = array_chunk($result, 6, true);
        }
        // print_r($result);die;
        return $this->render('show-one', ['dataProvider' => $dataProvider]);
    }

    /**
     * 货架展示
     * @return string
     */
    public function actionShowAll()
    {
        $request      = Yii::$app->request->queryParams;
        // print_r($request);die;
        $searchModel  = new ShelfSearch();
        $data         = $searchModel->searchall($request);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        return $this->render('show-all', [
            'request'      => $request,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination
        ]);
    }

    /**
     * 货架当前状况统计
     */
    public function actionTotal()
    {
        // 统计所有
        $total_all = ShelfMaterials::find();
        $total_all->select('sm.part_no,s.status');
        $total_all->alias('sm');
        $total_all->leftJoin(Shelf::tableName() . ' s', 'sm.shelf_id = s.id');
        $total_all->where(['sm.fettle'=>1]);
        $total_all->andFilterWhere(['in','sm.status',[1,2,3,4]]);
        $total_all->asArray();

        $dataProvider = $total_all->all();

        $data = [];

        if ($dataProvider){
            foreach ($dataProvider as $key=>$value){
                if(isset($data[$value['part_no']][$value['status']])){
                    $data[$value['part_no']][$value['status']] += 1;
                }else{
                    $data[$value['part_no']] = [1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0];
                    $data[$value['part_no']][$value['status']] = 1;
                }
            }
        }
        return $this->render('total', ['result' => $data]);

      //  echo"<pre>";var_dump($data);exit;
    }
}
