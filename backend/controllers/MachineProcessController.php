<?php

namespace backend\controllers;

use Yii;
use backend\models\MachineProcess;
use backend\models\search\MachineProcessSearch;
use yii\data\Pagination;
use yii\filters\VerbFilter;

class MachineProcessController extends BaseController
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
     * Lists all Process models.
     * @return mixed
     */
    public function actionIndex()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new MachineProcessSearch();
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
            $post['createby'] = $this->getUid();
            $model            = new MachineProcess();
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
        $model = MachineProcess::findOne($id);
        if (Yii::$app->request->isPost) {
            $post  = Yii::$app->request->post();
            $model = new MachineProcess();
            $check = $model->check($post);
            if (!$check) {
                return $this->json(100, '工序已存在');
            }
            $res = $model->edit($post);
            if ($res['status'] != 200) {
                return $this->json(100, $res['msg']);
            }
            return $this->json(200, $res['msg']);
        }
        return $this->render('update', compact('model'));
    }

    public function actionSearchByName($area = null){
        $name = Yii::$app->request->get('keyword');
        $result = [
            'code' => 1,
            'msg' => '没有数据',
            'results' => []
        ];

        $query = MachineProcess::find()->select('id,name as text');
        if($name){
            $query->where(['like','name',$name]);
        }
        $query->andWhere(['=','status',1]);
        $params = $query->limit(20)->asArray()->all();
        if($params){
            $result['code'] = 0;
            $result['msg'] = 'success';
            $result['results'] = $params;
        }
        return json_encode($result);
    }
}
