<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "nxp_file_info".
 *
 * @property int $id
 * @property string $name 文件名称
 * @property string $path 文件路径
 * @property string $size 文件大小
 * @property string $type 文件类型
 * @property int $create_time
 * @property int $update_time
 */
class FileInfo extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_file_info';
    }

    public function behaviors()
    {
        return [
            $this->getTimestampBehaviorArray()
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['create_time', 'update_time'], 'integer'],
            [['name', 'path',  'type'], 'string', 'max' => 255],
            ['size','safe']
        ];
    }

    /**
     * @param $path array|string
     */
    public static function getFileIdByPath($path){
        return self::find()->where(['path' => $path])->select('id')->column();
    }

    /**
     * 保存文件信息
     * @param $file
     * @param $path
     * @return bool
     */
    public function saveFileInfo($file,$path){
        $this->setAttributes([
            'name' => $file['name'],
            'type' => $file['type'],
            'size' =>  $file['size'],
            'path' => $path
        ]);
        return $this->save();
    }

    /**
     * 查询文件路径
     * @param $id
     * @return array|bool
     */
    public function getFilePathById($id){
        $data = self::find()->select('id,path')->where(['id' => $id])->asArray()->all();
        if($data){
            return ArrayHelper::map($data,'id','path') ;
        }
        return false;
    }

    /**
     * 查询文件信息
     * @param $id
     * @return array|bool
     */
    public function getFileInfoById($id){
        $data = self::find()->where(['id' => $id])->asArray()->all();
        if($data){
            return $data;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'path' => '路径',
            'size' => '大小',
            'type' => '类型',
            'create_time' => 'Create Date',
            'update_time' => 'Update Date',
        ];
    }
}
