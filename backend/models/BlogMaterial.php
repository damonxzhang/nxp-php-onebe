<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "nxp_blog_material".
 *
 * @property int $id
 * @property int $blog_id 日志
 * @property string $matter_number 物料编号
 * @property string $devixe_number 设备编号
 * @property string $address 位置
 * @property string $comment 其他信息
 * @property int $create_time
 */
class BlogMaterial extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_blog_material';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['blog_id', 'create_time'], 'integer'],
            [['matter_number','device_number' ], 'string', 'max' => 30],
            [[ 'address', 'comment'], 'string', 'max' => 255],
        ];
    }


    /**
     * 保存日志类型资料
     * @param $params
     * @return bool
     * @throws Exception
     */
    public function saveBlogMaterial($params){

        $data = [];
        $time = time();

        foreach ($params['matter_number'] as $k => $v){

            if (($params['type'] == Blog::TYPE_MATERIAL || $params['type'] == Blog::TYPE_FLT ) && empty($v)){
                return '请输入物料批号！';

            }elseif($params['type'] == Blog::TYPE_FACILITY && empty($params['device_number'][$k])){
                return '请输入设备编号！';
            }
            if(!empty($v) || !empty($params['device_number'][$k])){
                $data[] = [
                    'blog_id' =>$params['blog_id'],
                    'matter_number' => $v,
                    'device_number' => !empty($params['device_number'][$k])?$params['device_number'][$k] :'',
                    'address' => $params['address'][$k],
                    'create_time' => $time
                ];
            }
        }
        if(empty($data)){
            return true;
        }
        $result = Yii::$app->db->createCommand()->batchInsert(BlogMaterial::tableName(),[
            'blog_id',
            'matter_number',
            'device_number',
            'address',
            'create_time'
        ], $data)->execute();
        if($result === count($data)){
            return true;
        }else{
            return '保存失败！';
        }

    }

    /**
     * 保存日志类型资料
     * @param $params
     * @return bool
     * @throws Exception
     */
    public function updateBlogMaterial($params){

        $data = [];
        $time = time();
        foreach ($params['matter_number'] as $k => $v){

            if (($params['type'] == Blog::TYPE_MATERIAL || $params['type'] == Blog::TYPE_FLT )&& empty($v)){
                return '请输入物料批号！';

            }elseif($params['type'] == Blog::TYPE_FACILITY && empty($params['device_number'][$k])){
                return '请输入设备编号！';
            }

            $array = [
                'matter_number' => $v,
                'device_number' =>  !empty($params['device_number'][$k])?$params['device_number'][$k] :'',
                'address' => $params['address'][$k],
            ];
            //如果有id 更新
            if(!empty($params['material_id'][$k])){
                if(false === self::updateAll($array,'id = '. $params['material_id'][$k]) ){
                    return "更新类型信息失败！";
                }
            }else{
                $data[] = array_merge($array,[
                    'blog_id' =>$params['blog_id'],
                    'create_time' => $time
                ]);
            }
        }
        if(!empty($data) && count($data) > 0){
            $result = Yii::$app->db->createCommand()->batchInsert(BlogMaterial::tableName(),[
                'matter_number',
                'device_number',
                'address',
                'blog_id',
                'create_time'
            ], $data)->execute();
            if($result !== count($data)){
                return '新增类型信息失败！';
            }
        }
        return true;
    }



    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'blog_id' => '日志',
            'matter_number' => '物料编号',
            'device_number' => '设备编号',
            'address' => '位置',
            'comment' => '备注',
            'create_time' => 'Create Time',
        ];
    }
}
