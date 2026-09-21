<?php

namespace backend\controllers;

use backend\models\MachineCheckDetail;
use backend\models\MachineRegular;
use backend\models\search\MachineRegularSearch;
use yii\data\Pagination;
use backend\models\MachineRoster;
use backend\models\Facility;
use backend\models\MachineCheck;
use backend\models\MachineCheckFile;
use backend\models\MachineOption;
use backend\models\MachineOptionFile;
use backend\models\search\MachineCheckSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use Yii;
use yii\web\NotFoundHttpException;

class MachineRegularController extends BaseController
{
    public $enableCsrfValidation = false;

    /**
     * @param $request
     * @return array
     */
    public function getSearch($request)
    {
        $searchModel  = new MachineRegularSearch();
        $data         = $searchModel->search($request);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        return [
            'request'      => $request,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination
        ];
    }

    /**
     * Lists all Process models.
     * @return mixed
     */
    public function actionIndex()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new MachineRegularSearch();
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
    public function actionDay()
    {
        if (Yii::$app->request->isPost) {
            $post               = Yii::$app->request->post();
            $model              = new MachineRegular();
            $model->area        = ArrayHelper::getValue($post, 'area');
            $model->process     = ArrayHelper::getValue($post, 'process');
            $model->class       = ArrayHelper::getValue($post, 'class');
            $model->status      = MachineRegular::STATUS_UNDEFINED;
            $model->start_time  = ArrayHelper::getValue($post, 'start_time', '');
            $model->week        = Json::encode(ArrayHelper::getValue($post, 'week_id', []));
            $model->facility_id = Json::encode(ArrayHelper::getValue($post, 'facility_id', []));
            $model->create_user = Yii::$app->user->identity->user_id;
            $model->type        = MachineRegular::TYPE_N;
            $model->create_time = date('Y-m-d H:i:s', time());
            if (!$model->save(false)) {
                return $this->json(100, '添加失败!');
            }
            return $this->json(200, '添加成功!');
        }
        $facilityArr = Facility::getNumberList();
        return $this->render('day', ['facilityArr' => $facilityArr]);
    }


    /**
     * @return string
     */
    public function actionDayExamine()
    {
        $request               = Yii::$app->request->queryParams;
        $request['class']      = MachineCheck::CLASS_D;
        $request['status']     = MachineCheck::STATUS_NO;
        $request['check_time'] = date('Y-m-d', time());
        return $this->render('examine', $this->getSearch($request));
    }

    /**
     * @return string
     */
    public function actionNightExamine()
    {
        $request           = Yii::$app->request->queryParams;
        $request['class']  = MachineCheck::CLASS_N;
        $request['status'] = MachineCheck::STATUS_NO;
        $hour              = date('H');
        if ($hour >= 20) {
            $request['check_time'] = date('Y-m-d', time());  //今天
        } else {
            $request['check_time'] = date('Y-m-d', strtotime("-1 day"));  //昨天
        }
        return $this->render('examine', $this->getSearch($request));
    }

    /**
     * @return string
     */
    public function actionCreate()
    {
        if (Yii::$app->request->isPost) {
            $post  = Yii::$app->request->post();
            $model = new MachineCheck();
            $res   = $model->create($post);
            if (!$res) {
                return $this->json(100, '添加失败!');
            }
            return $this->json(200, '添加成功!');
        }
        return $this->render('create');
    }

    /**
     * @return string
     */
    public function actionCamera()
    {
        if (Yii::$app->request->isPost) {
            $post       = Yii::$app->request->post();
            $imgPicture = str_replace('data:image/png;base64,', '', $post['img_picture']);
            $imgPicture = str_replace(' ', '+', $imgPicture);
            $data       = base64_decode($imgPicture);
            $path       = 'uploads/5s/' . date('Ymd') . '/camera/';
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }
            $fileName = time() . '.png';
            $filePath = $path . $fileName;
            $fp       = fopen($filePath, 'w');
            fwrite($fp, $data);
            fclose($fp);
            $post['file_path'] = '/' . $filePath;
            $post['createby']  = $this->getUid();
            $fileId            = (new MachineCheckFile())->create($post);
            if (!$fileId) {
                return $this->json(100, '文件上传失败!');
            }
            return $this->json(200, '拍照完成！', ['file_id' => $fileId, 'img_url' => '/' . $filePath]);

        }
        $key = Yii::$app->request->get('key');
        return $this->render('camera', ['key' => $key]);
    }

    /**
     * @return string
     * @throws \yii\db\Exception
     */
    public function actionDetail()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if (!isset($post['option_id'])) {
                return $this->json(100, '数据不完整!');
            }
            $transaction          = Yii::$app->db->beginTransaction();
            $checkId              = $post['check_id'];
            $CheckModel           = MachineCheck::findOne($checkId);
            $CheckModel->status   = MachineCheck::STATUS_YES;
            $CheckModel->createby = $this->getUid();
            if (!$CheckModel->save()) {
                $transaction->rollBack();
                return $this->json(100, '保存失败!');
            }
            $data = [];
            $time = date('Y-m-d H:i:s', time());
            foreach ($post['option_id'] as $key => $id) {
                $insertData['check_id']    = $checkId;
                $insertData['option_id']   = $id;
                $insertData['facility_id'] = $CheckModel->facility_id;
                $insertData['createby']    = $this->getUid();
                $insertData['score']       = $post['score'][$key];
                if (isset($post['file_id'])) {
                    $fileId                     = ArrayHelper::getValue($post['file_id'], $key, []);
                    $insertData['file_path_id'] = Json::encode($fileId);
                } else {
                    $insertData['file_path_id'] = Json::encode([]);
                }
                $insertData['create_time'] = $time;
                $data[]                    = $insertData;
            }
            $insertFlowNode = Yii::$app->db->createCommand()->batchInsert(MachineCheckDetail::tableName(), ['check_id', 'option_id', 'facility_id', 'createby', 'score', 'file_path_id', 'create_time'], $data)->execute();
            if ($insertFlowNode > 0) {
                $transaction->commit();
                return $this->json(200, '保存成功');
            }
            $transaction->rollBack();
            return $this->json(100, '保存失败!!');
        }

        $id            = Yii::$app->request->get('id');
        $CheckModel    = MachineCheck::findOne($id);
        $facilityModel = Facility::findOne($CheckModel->facility_id);
        $optionArr     = MachineOption::find()->where(['status' => MachineOption::SELECT_YES, 'model_id' => $facilityModel->model])->asArray()->all();
        foreach ($optionArr as $key => $value) {
            $optionArr[$key]['file_path'] = MachineOptionFile::find()->select('file_path')->where(['option_id' => $value['id']])->column();
        }
        $baseUrl = Yii::$app->params['base_url'];
        return $this->render('detail', [
            'checkId'   => $id,
            'baseUrl'   => $baseUrl,
            'optionArr' => $optionArr,
            'number'    => $facilityModel->number,
        ]);
    }

    /**
     * @return string
     * @throws \yii\db\Exception
     */
    public function actionImport()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if (empty($post['facility_id'])) {
                return $this->json(100, '添加失败!');
            }
            $dateArr    = $this->getDateFromRange($post['start_date'], $post['end_date']);
            $createTime = date('Y-m-d H:i:s', time());
            $data       = [];
            foreach ($dateArr as $date) {
                foreach ($post['facility_id'] as $facilityId) {
                    $data[] = [$facilityId, $post['class'], $date, $createTime];
                }
            }
            $insertFlowNode = Yii::$app->db->createCommand()->batchInsert(MachineCheck::tableName(), ['facility_id', 'class', 'check_time', 'create_time'], $data)->execute();
            if ($insertFlowNode > 0) {
                return $this->json(200, '添加成功!');
            }
            return $this->json(100, '添加失败!');
        }
        $facilityArr = Facility::getNumberList();
        return $this->render('import', ['facilityArr' => $facilityArr]);
    }

    /**
     * 获取指定日期段内每一天的日期
     * @param $startDate 开始日期
     * @param $endDate   结束日期
     * @return array
     */
    function getDateFromRange($startDate, $endDate)
    {
        $date           = [];
        $startTimesTamp = strtotime($startDate);
        $endTimesTamp   = strtotime($endDate);
        if ($startTimesTamp > $endTimesTamp)
            return $date;
        $days = ($endTimesTamp - $startTimesTamp) / 86400 + 1;// 计算日期段内有多少天
        for ($i = 0; $i < $days; $i++) {
            $date[] = date('Y-m-d', $startTimesTamp + (86400 * $i));
        }
        return $date;
    }

    public function actionRegular(){
        $this->addCheck();//生成规则
    }
    
    /**
     * @return bool
     */
    public function addCheck()
    {
        $query = MachineRegular::find();
        $query->where(['type' => MachineRegular::TYPE_N]);
        $query->andWhere(['status' => MachineRegular::STATUS_YES]);
        $query->asArray();
        $regularData = $query->all();
        if (!$regularData) {
            return false;
        }
        $toady = date("w", time());
        $workDate = date("Y-m-d", time());
        foreach ($regularData as $regular) {
            foreach (Json::decode($regular['week']) as $week) {
                if ($toady == $week) {
                    $roster = MachineRoster::findOne(['class' =>$regular['class'], 'week' => $toady,'work_date' => $workDate]);
                    if ($roster){
                        foreach (Json::decode($regular['facility_id']) as $facilityId) {
                            $insert            = [
                                'facility_id' => $facilityId,
                                'regular_id'  => $regular['id'],
                                'roster_id'   => $roster->id,
                                'class'       => $regular['class'],
                                'area'        => $regular['area'],
                                'process'     => $regular['process'],
                                'check_time'  => date('Y-m-d', time()),
                            ];
                            $checkData = MachineCheck::find()->where($insert)->one();
                            if ($checkData){
                                continue;
                            }
                            $insert['status']      = MachineCheck::STATUS_NO;
                            $insert['create_time'] = date('Y-m-d H:i:s', time());
                            $model             = new MachineCheck();
                            $model->attributes = $insert;
                            $model->save(false);
                        }
                    }
                }
            }
        }
        return true;
    }

    /**
     * 详情
     * @return string
     */
    public function actionView()
    {
        $id      = Yii::$app->request->get('id');
        $model   = MachineRegular::findOne(['id' => $id]);
        return $this->render('view', [
            'model'       => $model
        ]);
    }

     /**
     * @return string
     * @throws \yii\base\Exception
     */
    public function actionModify()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $model   = MachineRegular::findOne(['id' => $post['id']]);
            $model -> status = $post['status'];
            if (!$model->save(false)) {
                return $this->json(100, '修改失败');
            }
            return $this->json(200, '修改成功');
        }
        $id = Yii::$app->request->get('id');
        $model = MachineRegular::findOne($id);
        return $this->render('modify', compact('model'));
    }

    /**
     * 删除配置项
     */
    public function actionDelete($id)
    {
        $model = MachineRegular::findOne($id);

        if($model && $model->delete()){
            return $this->json(200,'删除成功！');
        }else{
            return $this->json(100,'删除失败！');
        }
    }

    /**
     * 编辑未定义配置内容
     */
    public function actionUpdate($id){

        $model = $this->findModel($id);

        if (Yii::$app->request->isPost) {

            $post               = Yii::$app->request->post();
            $model              = MachineRegular::findOne($id);
            $model->area        = ArrayHelper::getValue($post, 'area');
            $model->process     = ArrayHelper::getValue($post, 'process');
            $model->class       = ArrayHelper::getValue($post, 'class');
            $model->status      = MachineRegular::STATUS_undefined;
            $model->start_time  = ArrayHelper::getValue($post, 'start_time', '');
            $model->week        = Json::encode(ArrayHelper::getValue($post, 'week_id', []));
            $model->facility_id = Json::encode(ArrayHelper::getValue($post, 'facility_id', []));
            $model->create_user = Yii::$app->user->identity->user_id;
            $model->type        = MachineRegular::TYPE_N;
//            $model->create_time = date('Y-m-d H:i:s', time());
            if (!$model->save(false)) {
                return $this->json(100, '修改失败!');
            }
            return $this->json(200, '修改成功!');
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the TrainCourses model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrainCourses the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MachineRegular::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');

    }
}


