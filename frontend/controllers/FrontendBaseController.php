<?php
namespace frontend\controllers;

use yii\helpers\Json;
use yii\web\Controller;
use Yii;
use yii\helpers\ArrayHelper;
use backend\models\UserInfo;
use backend\models\ShelfMaterialAuthority;

class FrontendBaseController extends Controller
{
    public $enableCsrfValidation = false;


    /**
     * 输出Json 数据
     * @param $status
     * @param string $message
     * @param array $data
     */
    public function outputJsonApp($status, $message = '', $data = []) {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $array['status']  = $status;
        $array['message'] = $message;
        $resultData = $data ? ArrayHelper::toArray($data) : [];
        if (count($resultData) == count($resultData, 1)){ //判断是多维数组还是一维数组
            $array['data'] = $resultData;
            $array['data']['total'] = count($resultData);
        }else{//多维数组
            $array['data']['total'] = count($resultData);
            $array['data']['data_content'] = $resultData;
        }
        return exit(Json::encode($array));
    }


    /**
     * 输出Json 数据
     * @param $status
     * @param string $message
     * @param array $data
     */
    public function outputJson($status, $message = '', $data = []) {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $array['status']  = $status;
        $array['message'] = $message;
        if(!empty($data)){
            $array['data'] = $data;
        }
        return exit(Json::encode($array));
    }

    /**
     * get 获取请求
     * @param null $name
     * @param null $defaultValue
     * @return array|mixed|string
     */
    protected function get($name = null, $defaultValue = null) {
        $get = Yii::$app->request->get($name, $defaultValue);
        return  $get ? $this->new_trim($get) : $get;
    }

    /**
     * post 获取请求
     * @param null $name
     * @param null $defaultValue
     * @return array|string
     */
    protected function post($name = null, $defaultValue = null) {
        $post = Yii::$app->request->post($name, $defaultValue);
        return $this->new_trim($post);
    }

    /**
     * 去除空格
     * @param $string
     * @return array|string
     */
    public function new_trim($string) {
        if (!is_array($string)){
            return trim($string);
        }
        foreach ($string as $key => $val) {
            $string[$key] = self::new_trim($val);
        }
        return $string;
    }

    /**
     * 检查参数
     * @param array $param 需要检查的参数数组
     * @param array $base  包含值的数组
     */
    public function checkParameter($param =[], $base = []) {
        if(empty($param) || empty($base)){
            $this->outputJson('false', 'Parameter error!');
        }
        $paramKey = array_keys($param);
        foreach ($base as $val){
            if(!in_array($val, $paramKey)){
                $this->outputJson('false', 'Parameter "' . $val . '" does not exist!');
            }
        }
    }

    /**
     *判断请求，并返回参数
     */
    public function validateRequest( ){

        if(Yii::$app->request->isPost){

            $params = Yii::$app->request->post();

            if(!empty($params)){
                return $params;
            }
            return $this->outputJson('false', '请求参数不能为空!');

        }
        return $this->outputJson('false', '请求方式不正确!');



    }
    
    public function checkMaterialsAuth($user_id){
        if (!ShelfMaterialAuthority::checkAuthority($user_id)){
            return $this->outputJson('false', '权限验证失败!请先添加操作权限');
        }
    }


    /**
     * auth duhf
     * 22/11/27
     * @param $wi_id
     * @return mixed
     */
    public function getUserInfo($wi_id){
        $userInfo = UserInfo::findOne(['badge_code' => $wi_id]);
        if (empty($userInfo)){
            return $this->outputJson('false', '用户不存在!');
        }
        return $userInfo;
    }

}
