<?php
namespace backend\controllers;

use Yii;
use backend\models\CompoundUserRole;
use yii\base\ActionFilter;
use yii\helpers\Json;

class MaterialVerifyRoleController extends ActionFilter{

    public $on      = []; //上架要验证的方法
    public $under   = [];//下架要验证的方法
    protected $result = true;

    public function beforeAction($action)
    {
        //查询用户用有的权限
        $userRole = CompoundUserRole::findOne(['user_id' => Yii::$app->user->identity->user_id]);
        //没有任何权限
        if(in_array($action->id,$this->on) && empty($userRole)){
            $this->result = false;
        //无上架权限
        }else if(in_array($action->id,$this->on) && $userRole->role_id != CompoundUserRole::ROLE_TOP){
            $this->result = false;
        //无下架权限
        }elseif(in_array($action->id,$this->under) && $userRole->role_id != CompoundUserRole::ROLE_BUTTON){
            $this->result = false;
        }
        if (!$this->result){
            echo Json::encode(['code' => 100,'msg' => '没有权限！']);die;
        }
        return parent::beforeAction($action);
    }


}