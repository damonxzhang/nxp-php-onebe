<?php

namespace backend\controllers;

use backend\models\MachineOptionFile;
use Yii;
use backend\models\MachineOption;
use backend\models\search\MachineOptionSearch;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class MachineOptionController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all MachineOption models.
     * @return mixed
     */
    public function actionIndex()
    {
        $request = Yii::$app->request->queryParams;
        $searchModel = new MachineOptionSearch();
        $data = $searchModel->search($request);
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
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {
        $model = new MachineOption();
        $isPost = Yii::$app->request->isPost;
        if ($isPost) {
            $db   = Yii::$app->db->beginTransaction();
            $post = Yii::$app->request->post();
            $id   = $model->create($post);
            if (!$id) {
                $db->rollBack();
                return $this->json(100, '添加失败！');
            }
            $post['option_id'] = $id;
            $up_file           = $this->optionFile($post);
            if (!$up_file) {
                $db->rollBack();
                return $this->json(100, '文件保存失败');
            }
            $db->commit();
            return $this->json(200, '添加成功！');
        }
        $param      = Yii::$app->request->queryParams;
        return $this->render('create', [
            'model' => $model,
            'param' => $param,
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = MachineOption::findOne($id);
        $post  = Yii::$app->request->post();
        if ($model->load($post)) {
            if ($model->save()) {
                return $this->json(200, '保存成功！');
            } else {
                return $this->json(100, $model->errorToString());
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return MachineOption|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = MachineOption::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionUpload()
    {
        $path = $this->upload('/option/');
        return json_encode($path);
    }

    public function optionFile($param, $key = 'option_id')
    {
        //检查是否上传文件
        if (!isset($param['upload_file'])) {
            return true;
        }
        $data = [];
        $time = date('Y-m-d H:i:s', time());
        foreach ($param['upload_file'] as $file) {
            $path      = pathinfo($file);
            $extension = $path['extension'];
            $data[]    = [$param[$key], $file, $path['filename'], $extension, $time];
        }
        $insertFlowNode = Yii::$app->db->createCommand()->batchInsert(MachineOptionFile::tableName(), [$key, 'file_path', 'file_name', 'file_extension', 'create_time'], $data)->execute();
        if ($insertFlowNode > 0) {
            return true;
        }
        return false;
    }

    public function actionDetail()
    {
        $id         = Yii::$app->request->get('id');
        $model      = MachineOption::findOne($id);
        $baseUrl    = Yii::$app->params['base_url'];
        $optionFile = (new MachineOptionFile())->getFileData(['option_id' => $id]);
        return $this->render('detail', ['model' => $model, 'baseUrl' => $baseUrl, 'optionFile' => $optionFile]);
    }


    public function actionSearchOption(){
        $request = Yii::$app->request->post();
        $result = [
            'code' => 1,
            'msg' => '没有数据',
            'results' => []
        ];
        $params = MachineOption::getOptByKey($request);
        if(!empty($params)){
            $result['code'] = 0;
            $result['msg'] = 'success';
            $result['results'] = $params;
        }
        return json_encode($result);
    }

     /**
     * 删除
     * @param $id
     * @return string
     */
    public function actionDel(){
        $id = (int)Yii::$app->request->get('id');
        $model = MachineOption::findOne(['id' => $id]);
        if(empty($model)){
            return $this->json(100,'数据错误！');
        }
        if($model->delete()){
            return $this->json(200,'删除成功！');
        }else{
            return $this->json(100,'删除失败!');
        }
    }
}
