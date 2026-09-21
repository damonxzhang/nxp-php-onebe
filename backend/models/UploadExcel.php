<?php
namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;
use yii\web\Upload;

class UploadExcel extends Model
{
    /**
    * @var UploadedFile
    */
    public $imageFile;
    public $file;

    public function rules(){
        return [
            [['file'], 'file'],
            // [['imageFile'],'file', 'skipOnEmpty' => false,'extensions' => 'xls,xlsx'],
        ];
    }
    public function attributeLabels(){
        return [
            'file'=>'文件上传'
        ];
    }

    public function upload(){
        //实例化
        // $model = new Upload();
        if ($this->validate()) {
            $path = 'uploads/excel/'.date('Ymd');
            $rootPath = $path;
            if(!is_dir($rootPath)){
                mkdir($rootPath,0777,true);
            }
            // print_r($this->file);die;
            $fileName = uniqid().'.' .$this->file->getExtension();
            $filePath = $rootPath.'/'.$fileName;
            $this->file->saveAs($filePath);
            return ['status' => true, 'path' => '/' . $path .'/'. $fileName]; // uploads目录前加'/',否则url美化后无法正常显示
        } else {
            $error = array_values($this->getFirstErrors())[0];
            return ['status' => false, 'msg' => $error];
        }
    }
}