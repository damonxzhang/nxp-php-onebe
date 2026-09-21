<?php
namespace backend\modules\Appeal\models;

use backend\models\Appeal;

class AppealFactory{

    private static $model;


    /**
     * 返回Appeal实例
     * @param $class
     * @param bool $id
     * @return mixed
     */
    public static function create($class,$id = false){

        if(class_exists($class)){
            if(!empty($id)){
                $model = $class::findOne($id);
            }else{
                $model = new $class;
            }
            if($model instanceof  Appeal){
                return $model;
            }else{
                return false;
            }
        }
        return false;
    }

}