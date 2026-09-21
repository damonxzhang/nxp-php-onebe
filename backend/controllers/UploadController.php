<?php
namespace backend\controllers;

use Yii;
use backend\models\FileInfo;
use common\models\GlobalFun;
use yii\helpers\Json;
use yii\web\Response;
use yii\web\UploadedFile;

class UploadController extends BaseController{

    public function actionUpload(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $file = UploadedFile::getInstanceByName('file');

        if (!$file) {
            return ['code' => 100, 'message' => '文件上传失败，请稍后再尝试!'];
        }

        //查询是否存在表中
        $fileModel = FileInfo::findOne(['name' => $file->name,'size' => $file->size]);


        //检查图片是否存在，路径是否正确
        if(!empty($fileModel) && GlobalFun::file_url_exists ($fileModel->path)){

            return ['code' => 200, 'message' => '上传成功!', 'path' =>  $fileModel->path,'id' => $fileModel->id ];

        }

        $path     = 'uploads/' . date('Ymd') ;
        $fileName = $file->baseName . time() . '.' . $file->extension;
        $filePath = $path . $fileName;
        if (file_exists($filePath)) {
            return ['code' => 100, 'message' => '此文件已经存在啦!'];
        }
        if (!is_dir($path)) {
            mkdir($path, 0777,true);//递归方式创建数组
        }
        $uploadResult = $file->saveAs($filePath);
        if ($uploadResult) {
            $fileModel = new FileInfo();
            $fileModel->setAttributes([
                'name' => $file->name ,
                'type' => $file->type,
                'size' => $file->size,
                'path' => '/'.$filePath
            ]);
            $fileModel->save();
            return ['code' => 200, 'message' => '上传成功!', 'path' => '/'.$filePath,'id' => $fileModel->id];
        }
        return ['code' => 100, 'message' => '文件上传失败，请稍后再尝试!'];

    }


}