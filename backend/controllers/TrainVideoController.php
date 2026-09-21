<?php

namespace backend\controllers   ;

use backend\actions\UploadAction;
use backend\Behaviors\Upload;
use Yii;
use backend\models\TrainVideo;
use backend\models\search\TrainVideoSearch;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use common\models\FtpUpdate;

/**
 * TrainVideoController implements the CRUD actions for TrainVideo model.
 */
class TrainVideoController extends BaseController
{

    public function actions()
    {
        return [
            'upload' =>[
                'class' => UploadAction::className(),
                'type'  => Upload::VEDIO_TYPE,
            ]
        ];
    }

    /**
     * Lists all TrainVideo models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TrainVideoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 所有视频列表展示
     * @return false|string
     */
    public function actionSearchVideos(){
        $model = new TrainVideoSearch();
        $name = Yii::$app->request->get('name');
        $id = Yii::$app->request->get('id');
        if(!empty($id)){
            $ids = explode(',',$id);
        }else{
            $ids=false;
        }
        $data = $model->getvideoListByArray($ids,$name);
        if(!empty($ids)){
            $data = $this->setLayuiTableSelect($ids,$data);
        }
        return json_encode(['code' => 0,'count' => count($data),'data' => $data]);
    }


    /**
     * Displays a single TrainVideo model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model =  $this->findModel($id);

        return $this->render('view', [
            'model' =>$model,
        ]);
    }

    /**
     * Creates a new TrainVideo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TrainVideo();

        if ($model->load(Yii::$app->request->post())) {
            $upload_file = Yii::$app->request->post('upload_file');

            $filePath = Yii::$app->request->post('file_path');
            if(!empty($filePath)){
                $filePath = str_replace('，',',',$filePath);
                $filePathArray = explode(',',$filePath);
                foreach ($filePathArray as $value){

                    $upload_file[] = '\\\\10.192.134.255\web\training_video\file\\'.$value;
                }
            }
            if(empty($upload_file)){
                return $this->json(100,'请上传视频！');
            }

            $upload_file = array_filter($upload_file,function ($path){
                return strpos($path,'.mp4');
            });

            if(!is_array($upload_file) || count($upload_file) <= 0){
                return $this->json(100,'请上传视频！');
            }
            $model->video_path = json_encode($upload_file);
            $model->create_user = Yii::$app->user->identity->user_id;
            if($model->save()){
                return $this->json(200,'保存成功！');
            }else{
                return $this->json(100,$model->errorToString());
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TrainVideo model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $upload_file = Yii::$app->request->post('upload_file');
            $filePath = Yii::$app->request->post('file_path');
            // $model->video_path = json_encode(Yii::$app->request->post('upload_file'));
            if(!empty($filePath)){
                $filePath = str_replace('，',',',$filePath);
                $filePathArray = explode(',',$filePath);
                foreach ($filePathArray as $value){

                    $upload_file[] = 'http://10.192.134.255/training_video/file/'.$value;
                }
            }

            if(!is_array($upload_file) || count($upload_file) <= 0){
                return $this->json(100,'请上传课件！');

            }
            $model->video_path = json_encode($upload_file);
            if($model->save()){
                return $this->json(200,'保存成功！');
            }else{
                return $this->json(100,$model->errorToString());
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TrainVideo model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete_time = time();
        if($model->save()){

            return $this->redirect(['index']);
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Finds the TrainVideo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrainVideo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrainVideo::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


   /**
     * 上传文件
     * @param string $folder
     * @return array
     */
    public function actionUpload()
    {
       
        $file = UploadedFile::getInstanceByName('file');//var_dump($file);die;
        if(!empty($this->type) && $this->type !== substr($file->type,0,strpos($file->type,'/'))){
            return ['code' => 100, 'message' => '请根据要求上传文件！'];
        }

        if (!$file) {
            return ['code' => 100, 'message' => '文件上传失败，请稍后再尝试!'];
        }
        $name = iconv("UTF-8","gb2312",$file->baseName); //利用Iconv函数对文件名进行重新编码
        $time = time();
        $fileName =  $name . $time . '.' . $file->extension;
        $filePath = $this->path . $fileName;
        $ftp = new FtpUpdate();
        $result = $ftp->connect();
        if (!$result){
            return ['code' => 100, 'message' => $ftp->get_error_msg()];
        }
        //上传文件
        if ($ftp->upload($file->tempName,$filePath)){
            $size = round($file->size/1024,2).'kb' ;
            $baseName=  $file->baseName . $time . '.' . $file->extension;
            return ['code' => 200, 'message' => '上传成功!', 'path' => Yii::$app->params['file_url'].$this->folder.$baseName,'size'=>$size];
        }else{
            return ['code' => 100, 'message' => '文件上传失败，请稍后再尝试!'];
        }
    }

}
