<?php

namespace common\models\uploadvideo;

use Yii;
class UploadVideo
{
    private $filepath;
    private $tmpPath;  //PHP文件临时目录
    private $blobNum; //第几个文件块
    private $totalBlobNum; //文件块总数
    private $fileName; //文件名
    private $saveDate; //文件名
    private $basePath; //基础文件夹
    private $temporallyPath;//临时文件目录
    private $basetemporallyPath;//临时文件夹;
    private $url;//视频地址
    public function __construct(){
        $this->basePath = Yii::$app->params['file_path'].'video';
        $this->basetemporallyPath = Yii::$app->params['temporallyPath_path'];
        $this->url = Yii::$app->params['file_url'].'video/';
        $this->saveDate = date('Ymd',time());
        $this->filepath = $this->basePath.'/'.$this->saveDate ;
    }

    /**
     * UploadVideo constructor.
     * @param $tmpPath 临时文件
     * @param $blobNum 分片文件数
     * @param $totalBlobNum 分片文件总数
     * @param $fileName 文件名称
     */
    public function setAttributes($tmpPath,$blobNum,$totalBlobNum,$fileName){
        $this->tmpPath =  $tmpPath;
        $this->blobNum =  $blobNum;
        $this->totalBlobNum =  $totalBlobNum;
        $this->fileName =  $fileName;
        $this->temporallyPath = $this->basetemporallyPath.'/'.$this->getPath();
        $this->initFolder();
        return $this;
    }


    //判断是否是最后一块，如果是则进行文件合成并且删除文件块
    private function fileMerge(){
        if($this->blobNum == $this->totalBlobNum){

            $file = fopen($this->filepath. '/'.$this->fileName,'w');
            for($i=1; $i<= $this->totalBlobNum; $i++){
                $temporaryFile = fopen($this->temporallyPath.'/'. $this->fileName.'__'.$i,'r');
                stream_copy_to_stream($temporaryFile,$file);
                fclose($temporaryFile);
            }
            fclose($file);

            $this->deleteFileBlob();
        }
    }

    //删除文件块
    private function deleteFileBlob(){
        for($i=1; $i<= $this->totalBlobNum; $i++){

            @unlink($this->temporallyPath. '/' .$this->fileName.'__'.$i);
        }
        @rmdir($this->temporallyPath);
    }

    //移动文件
    private function moveFile(){
        $filename = $this->temporallyPath.'/'. $this->fileName.'__'.$this->blobNum;
        move_uploaded_file($this->tmpPath,$filename);
    }

    /**
     * 执行程序和返回结果
     * @return false|string
     */
    public function apiReturn(){
        $this->moveFile();
        $this->fileMerge();
        if($this->blobNum == $this->totalBlobNum){
            if(file_exists($this->filepath. '/'.$this->fileName)){

                return $this->resultJson(2,'上传成功！',['file_path' => $this->url.$this->saveDate.'/'.$this->fileName]);
            }
        }else{
            if(file_exists($this->temporallyPath.'/'. $this->fileName.'__'.$this->blobNum)){

                return $this->resultJson(1,'上传中' );
            }else{
                $this->deleteFileBlob();
                return $this->resultJson(3,'上传失败！请稍后重试！' );
            }
        }
    }

    /**
     * 创建文件夹
     */
    public function initFolder(){

        if(!file_exists($this->basePath)){
            mkdir($this->basePath);
        }
        if(!file_exists($this->filepath)){
            mkdir($this->filepath);
        }
        if(!file_exists($this->basetemporallyPath)){
            mkdir($this->basetemporallyPath);
        }
        if(!file_exists($this->temporallyPath)){
            mkdir($this->temporallyPath);
        }
    }

    /**
     * 删除无用的分片文件
     * @return false|string
     */
    public function removeJunkFiles($fileName){

        if(empty($fileName)){
            return $this->resultJson(3,'删除失败！');
        }
        $this->fileName = $fileName;
        $this->temporallyPath = $this->basetemporallyPath.'/'.$fileName;

        if (!$handle = @opendir($this->temporallyPath)) {
            return false;
        }
        while (false !== ($file = readdir($handle))) {
            if ($file !== "." && $file !== "..") {       //排除当前目录与父级目录
                $file = $this->temporallyPath . '/' . $file;
                if (is_dir($file)) {
                    deleteDir($file);
                } else {
                    @unlink($file);
                }
            }

        }
        @rmdir($this->temporallyPath);

        return $this->resultJson(2,'删除成功！');
    }

    //分片文件夹名称
    public function getPath(){
        return substr($this->fileName,0,strpos($this->fileName,'.'));
    }

    //返回结果
    public function resultJson($code,$msg,array $params = []){
        $resArray = ['code' =>$code, 'msg' => $msg ];
        if(count($params) > 0){
            $resArray = array_merge($resArray,$params);
        }
        return json_encode($resArray);
    }

}