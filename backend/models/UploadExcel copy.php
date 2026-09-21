<?php
namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadExcel extends Model
{
    /**
    * @var UploadedFile
    */
    public $imageFile;
    public $file;

    public function rules(){
        return [
            [['file'], 'file', 'extensions' => 'xls,xlsx'],
            // [['imageFile'],'file', 'skipOnEmpty' => false,'extensions' => 'xls,xlsx'],
        ];
    }
    public function attributeLabels(){
        return [
            'file'=>'文件上传'
        ];
    }

    public function upload(){
        if ($this->validate()) {
            $path = 'uploads/excel/'.date('Ymd');
            $rootPath = $path;
            if(!is_dir($rootPath)){
                mkdir($rootPath,0777,true);
            }
            $fileName = uniqid().'.' .$this->imageFile->extension;
            $filePath = $rootPath.'/'.$fileName;
            $this->imageFile->saveAs($filePath);
            return ['status' => true, 'path' => '/' . $path .'/'. $fileName]; // uploads目录前加'/',否则url美化后无法正常显示
        } else {
            $error = array_values($this->getFirstErrors())[0];
            return ['status' => false, 'msg' => $error];
        }
    }
}