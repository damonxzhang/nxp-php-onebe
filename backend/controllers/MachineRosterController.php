<?php

namespace backend\controllers;

use backend\models\MachineRoster;
use backend\models\search\MachineRosterSearch;
use backend\models\UserInfo;
use common\models\Utility;
use Yii;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class MachineRosterController extends BaseController
{
    /**
     * Lists all Process models.
     * @return mixed
     */
    public function actionIndex()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new MachineRosterSearch();
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
     * 批量导入数据
     * @return string
     */
    public function actionImport()
    {
        $isPost = Yii::$app->request->isPost;
        if (!$isPost) {
            return $this->render('import');
        }
        $file = $_FILES['file'];
        if (empty($file['tmp_name'])) {
            return $this->json(100, '上传文件不能为空!');
        }
        $tmp    = explode('.', $file['name']);
        $suffix = array_pop($tmp);
        if ('xlsx' !== $suffix) {
            return $this->json(100, '上传文件无法识别，请使用模版!');
        }

        if ($file['size'] > 2097152) {
            return $this->json(100, '上传文件不能超过2MB!');
        }
        $excelData = Utility::getExcelData($file['tmp_name']);
        if (empty($excelData)) {
            return $this->json(100, '上传文件无内容!');
        }
        $return    = [
            'successCount' => 0,
            'errorCount'   => 0,
            'errorList'    => '',
        ];
        $errorList = [];
        foreach ($excelData as $key => $item) {
            $model              = new MachineRoster();
            $workDate            = trim($item[0]);
            if (!empty($workDate)) {
                $workDate = date('Y-m-d', strtotime(gmdate('Y-m-d', \PHPExcel_Shared_Date::ExcelToPHP($workDate))));
            }
            $model->work_date   = $workDate;
            $model->shift       = trim($item[1]);
            $model->class       = trim($item[2]);
            $model->week        = date("w", strtotime($workDate));
            $model->create_time = date('Y-m-d', time());
            if ($model->save(false)) {
                $return['successCount']++;
            } else {
                $errorList[] = '日期:' . $item[0];
                $return['errorCount']++;
            }
        }
        $return['errorList'] = join("<br>", $errorList);
        return $this->json(200, '添加完成!', $return);
    }

}

