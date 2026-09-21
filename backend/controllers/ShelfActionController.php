<?php
namespace backend\controllers;

use app\models\UploadExcel;
use backend\models\ShelfActionLog;
use backend\models\ShelfMaterialNum;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;
use backend\models\ShelfMaterialTotal;

class ShelfActionController extends BaseController{


    public function actionIndex(){
        
        $request      = Yii::$app->request->queryParams;
        // print_r($request);die;
        $query = ShelfActionLog::find();
        if(!empty($request)){
            $query->andFilterWhere(['>=', 'created_time', $request['start_time']]);
            $query->andFilterWhere(['<=', 'created_time', $request['end_time']]);
       
        }
         $query->orderBy('id desc');

        $data = new ActiveDataProvider(['query' => $query]);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination
        ]);

    }

    public function actionPoliceIndex(){
        $query = ShelfActionLog::find()->where(['id'=>0]);
        $query->orderBy('id desc');
        $data = new ActiveDataProvider(['query' => $query]);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        return $this->render('police-index', [
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination
        ]);

    }

    public function actionCheckIndex(){
        $request  = Yii::$app->request->queryParams;
        $query = ShelfMaterialNum::find();
        $query->orderBy('created_at desc');
        $part_no = ArrayHelper::getValue($request, 'part_no');
        $created_at = ArrayHelper::getValue($request, 'created_at');
        if (!empty($part_no)){
            $query->where(['part_no' => $part_no]);
        }
        if (!empty($created_at)){
            $query->where(['>','created_at', $created_at]);
        }
        $data = new ActiveDataProvider(['query' => $query]);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        return $this->render('check-index', [
            'data' => $dataProvider,
            'pagination' => $pagination,
            'request' => $request
        ]);
    }

    public function actionCheckForm(){
        $model = new UploadExcel();
        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model,'file');

            $res = $model->upload();
            if (!empty($res['status'])){
                $data = $this->calculation($this->importExcel(Yii::$app->basePath.'/web'. $res['path'] ));
                return $this->render('check-form',['data' => $data,'step' => 2]);
            }

        }
        return $this->render('check-form',['model' => $model,'step' => 1]);
    }

    public function importExcel($file,$row_length = 30 ){
        include(__DIR__.'/../../common/models/phpexcel/PHPExcel.php');
        $cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
        $cacheSettings = array('memoryCacheSize' => '16MB');
        \PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);//文件缓存
        //当前空间不用\，非当前空间要加\
        $PHPExcel = new \PHPExcel();//创建一个excel对象
        $PHPReader = new \PHPExcel_Reader_Excel2007(); //建立reader对象，excel—2007以后格式
        if (!$PHPReader->canRead($file)) {
            $PHPReader = new \PHPExcel_Reader_Excel5();//建立reader对象，excel—2007以前格式
            if (!$PHPReader->canRead($file)) {
                $msg = '不是excel格式文件';
                $this->apiReturn(303,$msg);
            }
        }
        $PHPExcel = $PHPReader->load($file); //加载excel对象
        $sheet = $PHPExcel->getSheet(0); //获取指定的sheet表
        $rows = $sheet->getHighestRow();//行数
        $data = array();
        $key = array();
        for ($i = 1; $i <= $rows; $i++){ //行数是以第1行开始
            $k = 'A';
            for ($j = 0; $j <= $row_length; $j++) { //列数是以A列开始
                $value =  (string)$sheet->getCell($k . $i)->getValue();
                if ($i == 1){
                    if ($value) {
                        $key[$j] =$value;
                    }
                } else {
                    if (!empty($key[$j])){
                        $data[$i - 1][$key[$j]] = $value;
                    }
                }
                $k++;
            }
        }
        return $data;
    }
    public function actionSaveCheckNum(){
        if (Yii::$app->request->isPost) {
            $cnt = Yii::$app->request->post('cnt');
            $new_cnt = Yii::$app->request->post('new_cnt');
            if (!empty($cnt)){
                foreach ($cnt as $on => $y){
                    $model = new ShelfMaterialNum();
                    $model->part_no = $on;
                    $model->number = $y;
                    $model->check_number = !empty($new_cnt[$on]) ? intval($new_cnt[$on]) : 0;
                    $model->created_at = date('Y-m-d H:i:s');
                    $model->save();
                }
            }
            return $this->json(true,'保存成功');
        }
        return $this->json(false,'保存失败');

    }

    public function calculationBackUp($params){
        $new_data = [];
        if (empty($params)){
            return  $new_data;
        }
        foreach ($params as $key => $val){
            if (empty($val['COMPOUND_DEVICE'])){
                continue;
            }
            if (empty($new_data[$val['COMPOUND_DEVICE']])){
                $new_data[$val['COMPOUND_DEVICE']] = [
                    'a' =>  intval($val['Start Quantity']),
                    'b' =>  intval($val['STRIP_CAPACITY']),
                    'd' =>  intval($val['Unit Weight']),
                    'e' =>  intval($val['Unit_by_shot']),
                    'f' =>  intval($val['X_out Rate']),
                ];
            } else {
                $new_data[$val['COMPOUND_DEVICE']]['a'] += intval($val['Start Quantity']);
                $new_data[$val['COMPOUND_DEVICE']]['b'] += intval($val['STRIP_CAPACITY']);
                $new_data[$val['COMPOUND_DEVICE']]['d'] = intval($val['Unit Weight']);
                $new_data[$val['COMPOUND_DEVICE']]['e'] = intval($val['Unit_by_shot']);
                $new_data[$val['COMPOUND_DEVICE']]['f'] = intval($val['X_out Rate']);
            }
        }
        foreach ($new_data as &$item){
            $cont = false;
            foreach ($item as $v){
                if ($v == 0){
                    $item['y']= 0;
                    $cont = true;
                }
            }
            if ($cont){
                continue;
            }
            $y1 = (float)$item['a'] * (float)$item['f'] / (float)$item['b'];
            $y2 = (float)$y1 / 2;
            $y3 = (float)$y2 * (float)$item['d'] * (float)$item['e'];
            $y = (float)$y3 / 10000;
            if ($y > 0){
                $y = sprintf("%.3f",$y);
            }
            $item['h_y'] = $y;

            // $str_num = strpos($y,'.');
            // if ($str_num > 0){
            //     $num = substr($y,$str_num + 1,1);
            //     if ($num >= 5){
            //         $y = round($y);
            //     }
            // }

            $item['y']= (float)$item['f'];
        }
        return $new_data;
    }


    public function calculation($params){
        $new_data = [];
        if (empty($params)){
            return  $new_data;
        }
        foreach ($params as $key => $val){
            if (empty($val['COMPOUND_DEVICE'])){
                continue;
            }
            $y1 = (float)$val['Start Quantity']* (float)$val['X_out Rate'] / (float)$val['STRIP_CAPACITY'];
            $y2 = (float)$y1 / 2;
            $y3 = (float)$y2 * (float)$val['Unit Weight'] * (float)$val['Unit_by_shot'];
            $y = (float)$y3 / 10000;
            if ($y > 0){
                $y = sprintf("%.1f",$y);
            }
            if (empty($new_data[$val['COMPOUND_DEVICE']])){
                $new_data[$val['COMPOUND_DEVICE']]['y'] = $y;
            } else {
                $new_data[$val['COMPOUND_DEVICE']]['y'] += $y;
            }
        }
        // print_r($new_data);die;
        $data = [];
        foreach ($new_data as $key=>$item){
            $k = $item['y'];
            $m = $item['y'];
            $str_num = $k * 10 % 10;
            if ($str_num > 0){
                $m = $k - $str_num/10 + 0.5 ;
                // print_r($item['y']);die;
                if ($str_num > 5){
                    $m = round($k);
                }
            }
            $data[$key]['y'] = $m;
        }
        return $data;
    }


    public function actionDemandIndex(){
        $request      = Yii::$app->request->queryParams;
        $query = ShelfMaterialTotal::find();
        if(isset($request['start_time']) || isset($request['end_time'])){
            $query->andFilterWhere(['>=', 'create_time', $request['start_time']]);
            $query->andFilterWhere(['<=', 'create_time', $request['end_time']]);
       
        }
        
        if(isset($request['part_no'])){
            $query->andFilterWhere(['like', 'part_no', trim($request['part_no'])]);
        }

        $query->orderBy('create_time desc');
        $data = new ActiveDataProvider(['query' => $query]);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        return $this->render('demand-index', [
            'dataProvider' => $dataProvider,
            'request'      => $request,
            'pagination'   => $pagination
        ]);
    }

}