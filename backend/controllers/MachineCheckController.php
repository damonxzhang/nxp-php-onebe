<?php

namespace backend\controllers;

use backend\models\MachineCheckDetail;
use Yii;
use yii\data\Pagination;
use yii\filters\VerbFilter;
use backend\models\Facility;
use backend\models\MachineCheck;
use backend\models\MachineCheckFile;
use backend\models\MachineOption;
use backend\models\MachineOptionFile;
use backend\models\search\MachineCheckSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class MachineCheckController extends BaseController
{
    public $enableCsrfValidation = FALSE;

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
     * @param $request
     * @return array
     */
    public function getSearch($request)
    {
        $searchModel  = new MachineCheckSearch();
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
        $request = Yii::$app->request->queryParams;
        return $this->render('index', $this->getSearch($request));
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
        $request          = Yii::$app->request->queryParams;
        $request['class'] = MachineCheck::CLASS_N;
        $request['status']= MachineCheck::STATUS_NO;
        $hour             = date('H');
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
                mkdir($path, 0777, TRUE);
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
     * 扫描
     * @return string
     */
    public function actionQrcodeCamera()
    {
        return $this->render('qrcode-camera');
    }


    /**
     * @return string
     * @throws \yii\db\Exception
     */
    public function actionDetail()
    {
        if (Yii::$app->request->isPost) {
            $post       = Yii::$app->request->post();
            if (!isset($post['option_id'])) {
                return $this->json(100, '数据不完整!');
            }
           
            $transaction     = Yii::$app->db->beginTransaction();
            $checkId    = $post['check_id'];
            $CheckModel = MachineCheck::findOne($checkId);
            $CheckModel->status   = MachineCheck::STATUS_YES;
            $CheckModel->createby = $this->getUid();
            if (!$CheckModel->save()) {
                $transaction->rollBack();
                return $this->json(100, '保存失败!');
            }
            $data       = [];
            $time       = date('Y-m-d H:i:s', time());
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
                if($post['score'][$key] < 3 && !isset($post['file_id'])){
                    return $this->json(100, '评分小于3分的检查项请拍照!!'); 
                }
                $insertData['create_time'] = $time;
                $insertData['remarks']     = $post['remarks'][$key];
                $data[]                    = $insertData;
            }
            $insertFlowNode = Yii::$app->db->createCommand()->batchInsert(MachineCheckDetail::tableName(), ['check_id', 'option_id', 'facility_id', 'createby', 'score', 'file_path_id', 'create_time','remarks'], $data)->execute();
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
    public function actionShow()
    {
        $id            = Yii::$app->request->get('id');
        $CheckModel    = MachineCheck::findOne($id);
        $facilityModel = Facility::findOne($CheckModel->facility_id);
        $optionArr     = MachineOption::find()->where(['status' => MachineOption::SELECT_YES, 'model_id' => $facilityModel->model])->asArray()->all();
        foreach ($optionArr as $key => $value) {
            $optionArr[$key]['file_path']       = MachineOptionFile::find()->select('file_path')->where(['option_id' => $value['id']])->column();
            $filePathId                         = MachineCheckDetail::find()->select('file_path_id')->where(['option_id' => $value['id'],'check_id' => $id, 'facility_id' => $CheckModel->facility_id])->scalar();
            $optionArr[$key]['check_file_path'] = !empty($filePathId) ? MachineCheckFile::find()->select('file_path')->where(['in','id',Json::decode($filePathId)])->column() : [];
            $optionArr[$key]['score']           = MachineCheckDetail::find()->select('score')->where(['option_id' => $value['id'],'check_id' => $id, 'facility_id' => $CheckModel->facility_id])->scalar();
        }
        $baseUrl = Yii::$app->params['base_url'];
        return $this->render('show', [
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
     * @param $endDate 结束日期
     * @return array
     */
    function getDateFromRange($startDate, $endDate)
    {
        $date           = [];
        $startTimesTamp = strtotime($startDate);
        $endTimesTamp   = strtotime($endDate);
        if ($startTimesTamp > $endTimesTamp) return $date;
        $days = ($endTimesTamp - $startTimesTamp) / 86400 + 1;// 计算日期段内有多少天
        for ($i = 0; $i < $days; $i++) {
            $date[] = date('Y-m-d', $startTimesTamp + (86400 * $i));
        }
        return $date;
    }

}

