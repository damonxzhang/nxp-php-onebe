<?php
namespace backend\controllers;

use backend\models\MachineVendor;
use backend\models\search\MachineVendorSearch;
use Yii;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class MachineVendorController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all MachineVendor models.
     * @return mixed
     */
    public function actionIndex()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new MachineVendorSearch();
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
     * @return string
     */
    public function actionCreate()
    {
        if (Yii::$app->request->isPost) {
            $post             = Yii::$app->request->post();
            $model            = new MachineVendor();
            $res              = $model->create($post);
            if ($res['status'] != 200) {
                return $this->json(100, $res['msg']);
            }
            return $this->json(200, $res['msg']);
        }
        return $this->render('create');
    }

    /**
     * @return string
     */
    public function actionUpdate()
    {
        $id    = Yii::$app->request->get('id');
        $model = MachineVendor::findOne($id);
        if (Yii::$app->request->isPost) {
            $post  = Yii::$app->request->post();
            $model = new MachineVendor();
            $check = $model->check($post);
            if (!$check) {
                return $this->json(100, '编号已存在');
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

