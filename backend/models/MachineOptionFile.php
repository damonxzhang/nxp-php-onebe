<?php

namespace backend\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * This is the model class for table "nxp_machine_option_file".
 *
 * @property int $id
 * @property int|null $option_id 检查项id
 * @property string $file_path 文件上传路径
 * @property string $file_name 上传文件名称
 * @property string $file_extension 文件后缀
 * @property string|null $create_time 创建时间
 */
class MachineOptionFile extends \backend\models\Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_machine_option_file';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['option_id'], 'integer'],
            [['file_path', 'file_name', 'file_extension'], 'required'],
            [['file_path', 'file_name', 'file_extension'], 'string'],
            [['create_time'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'             => 'ID',
            'option_id'      => '检查项id',
            'file_path'      => '文件上传路径',
            'file_name'      => '上传文件名称',
            'file_extension' => '文件后缀',
            'create_time'    => '创建时间',
        ];
    }

    /**
     * 获取 by id
     * 预留批量获取，目前只取最新的一个
     * @param $id
     * @return string
     */
    public function getPathById($option_id)
    {
        if (empty($option_id)) {
            return '';
        }
        $query = self::find();
        $query->select('file_path');
        $query->where(['=', 'option_id', $option_id]);
        $query->orderBy('id desc');
        $query->asArray();
        return $query->all();
    }

    /**
     * 获取文件
     * @param array $where
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getFileData($where = [])
    {
        $query = self::find();
        $query->where($where);
        $query->orderBy(['id' => SORT_DESC]);
        $query->asArray();
        return $query->all();
    }

}
