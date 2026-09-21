<?php
namespace backend\controllers;

use backend\models\FileInfo;
use backend\models\Role;
use backend\models\Route;
use backend\models\Shelf;
use backend\models\ShelfMaterials;
use backend\models\TrainFile;
use backend\models\User;
use backend\models\UserInfo;
use common\models\GlobalFun;
use common\models\SendMqtt;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\Controller;
use Yii;
use yii\web\UploadedFile;

class BaseController extends Controller
{
    //不验证表单的action
    protected $CsrfValidationNo = [
        'upload'
    ];
    /**
     * @param $action
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {

        if (Yii::$app->user->isGuest) {
            $this->redirect(['site/login']);
            return false;
        }
        if(in_array($action->id,$this->CsrfValidationNo)){

            $action->controller->enableCsrfValidation = false ;
        }
        $userInfo = UserInfo::findOne($this->getUid());
        $role = Role::findOne(['id' => $userInfo->role_id]);
        $permission = Json::decode($role->permission);
        $routes = Route::find()->where(['in', 'id', $permission])->asArray()->all();
        $permissionArr = array_column($routes, 'route');
        $allowUrl = ['index/index', 'index/welcome', 'index/clear-cache'];
        if (!in_array($this->route, $allowUrl)  && !in_array($this->route, $permissionArr) && RBAC) {
            echo $this->json(100, '抱歉，没有权限');
            return false;
        }
        return parent::beforeAction($action);
    }

    /**
     * 获取登陆ID
     * @return int|string
     */
    public function getUid()
    {
        $id= Yii::$app->user->getId();
        $userModel = User::findOne($id);
        return $userModel ? $userModel ->user_id : '';
    }

    /**
     * ajax 返回json数据
     * @param int $status  状态码
     * @param string $msg  提示信息
     * @param string $data
     * @return string
     */
    public function json($status, $msg, $data = '')
    {
        if ($data) {
            return Json::encode(['status' => $status, 'msg' => $msg, 'data' => $data], JSON_UNESCAPED_UNICODE);
        } else {
            return Json::encode(['status' => $status, 'msg' => $msg, 'data' => $data], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 返回数组信息
     * @param $status
     * @param $msg
     * @return array
     */
    public function arrData($status, $msg)
    {
        return ['status' => $status, 'msg' => $msg];
    }

    /**
     * CSV文件导出
     * @param $data
     * @param string $name
     */
    public function csvExport($data, $name = '')
    {
        $csvFileName = $name ? $name . '.csv' : date('YmdHis') . rand(111111, 999999) . '.csv';
        header('Content-Type: application/octet-stream');
        header("Accept-Ranges:bytes");
        header('Content-Disposition: attachment; filename="' . $csvFileName . '"');
        header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
        header("Content-Type: application/msexcel");
        header('Expires: Mon,26 Jul 1997 05:00:00 GMT');
        header('Content-Transfer-Encoding: binary');
        echo implode("\r\n", $data);
        exit;
    }

    /**
     * 批量添加数据
     * @param $tableName
     * @param $tableList
     * @param $insertData
     * @return bool
     * @throws \yii\db\Exception
     */
    public function insertData($tableName, $tableList,$insertData)
    {
        if(!is_array($tableList) || !is_array($insertData)){
            return false;
        }
        $result = Yii::$app->db->createCommand()->batchInsert($tableName, $tableList, $insertData)->execute();
        return $result > 0 ? true: false;
    }

    /**
     * 调试函数（支持语法高亮）
     */
    public function dd(){
        $param = func_get_args();
        foreach ($param as $p)  {
            VarDumper::dump($p, 10, true);
        }
        exit(1);
    }

    /**
     * 向用户发送邮件的方法
     * @param $toMember
     * 用户的邮件地址
     * @param $subject
     * 邮件主题
     * @param $body
     * 邮件内容
     * @return bool
     * 发送结果
     */
    public function sendEmail($toMember, $subject, $body)
    {
        //1构造对象
        $emailObj = Yii::$app->mailer->compose();
        $result   = $emailObj->setTo($toMember)//设置收件人的地址
        ->setSubject($subject)//设置邮件主题
        ->setHtmlBody($body)//设置邮件的内容的html
        ->send();//发送方法
        return $result;
    }

    /**
     * 上传文件
     * @param string $folder
     * @return array
     */
    public function upload($folder = '')
    {
        $file = UploadedFile::getInstanceByName('file');


        if (!$file) {
            return ['code' => 100, 'message' => '文件上传失败，请稍后再尝试!'];
        }

        $fileModel = FileInfo::findOne(['name' => $file->name,'size' => $file->size]);


        //图片存在，直接返回
        if(!empty($fileModel) && GlobalFun::file_url_exists ($fileModel->path)){

            return ['code' => 200, 'message' => '上传成功!', 'path' =>  $fileModel->path ];

        }

        $path     = 'uploads/' . date('Ymd') . $folder;
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
            return ['code' => 200, 'message' => '上传成功!', 'path' => '/'.$filePath];
        }
        return ['code' => 100, 'message' => '文件上传失败，请稍后再尝试!'];
    }

    /**
     * ppt转pdf
     * @param $file
     * @param $fileName
     * @return bool|string
     */
    public function pptTOPdf($file, $fileName)
    {
        $path         = pathinfo($file);
        $baseUrl      = Yii::$app->params['base_url'];
        $srcfilename  = $baseUrl . $file;
        $srcfilename  = str_replace('/', DIRECTORY_SEPARATOR , $srcfilename);
        $upfileBase   = dirname(dirname(__FILE__));
        $destfilename =$upfileBase.'\\web\\uploads\\'. date('Ymd') .'\\'. $fileName . '\\' . $path['filename']. '.pdf';
        try {
            $ppt = new \COM('powerpoint.application') or die('Unable to instantiate Powerpoint');
            $presentation = $ppt->Presentations->Open($srcfilename, false, false, false);
            $presentation->SaveAs($destfilename, 32, 1);
            $presentation->Close();
            $ppt->Quit();
        } catch (\Exception $e) {
            if (method_exists($ppt, 'Quit')) {
                $ppt->Quit();
            }
            return false;
        }
        return true;
    }

    /**
     *  返回layui数据表格选中状态值
     * @param $id
     * @param $data
     * @return array
     */
    public function setLayuiTableSelect($ids,$data){
        if(is_array($ids) && count($ids) > 0){
            return array_map(function($value) use ($ids){
                if(in_array($value['id'],$ids)){
                    $value['LAY_CHECKED'] = true;
                }
                return $value;
            },$data);
        }else{
            return $data;
        }
    }

    /**
     * 给下位机发消息
     * @param $shelfId
     * @param string $status
     * @return bool
     */
    public function sendMqttData($shelfId, $status = '')
    {
        //检查是否还有在货架格子上的物料
        $query = (new ShelfMaterials())->checkMaterialsByShelfId($shelfId);
        if ($query) {
            return true;
        }
        $shelfModel         = Shelf::findOne(['id' => $shelfId]);
        $shelfModel->status = $status;
        if (!$shelfModel->save()) {
            return false;
        }
        $sendMessage = (new SendMqtt())->publishMessage($shelfModel->mark, $shelfModel->number, $status);
        if (!$sendMessage) {
            return false;
        }
        return true;
    }

    /**
     * 将$model->getErrors得到的错误信息转换
     * @param $errors
     * @return string
     */
    public function errorToString($errors){

        if(empty($errors)){
            return '未定义错误！';
        }
        $errorStr = '';
        foreach ($errors as $value){

            $errorStr .=  ",".implode("\r\n",$value);
        }

        return trim($errorStr,',');
    }

}
