<?php

namespace backend\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * This is the model class for table "nxp_machine_check_file".
 *
 * @property int $id
 * @property int|null $option_id
 * @property int|null $createby
 * @property string $file_path 文件上传路径
 * @property string|null $create_time 创建时间
 */
class MachineCheckFile extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_machine_check_file';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['createby'], 'integer'],
            [['file_path'], 'string'],
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
            'createby'        => '发布人',
            'file_path'      => '文件上传路径',
            'create_time'    => '创建时间',
        ];
    }

    /**
     * @param $data
     * @return bool
     */
    public function create($data)
    {
        $this->file_path      = $data['file_path'];
        $this->createby       = $data['createby'];
        $this->create_time    = date('Y-m-d H:i:s', time());
        if (!$this->save()) {
            return FALSE;
        }
        return $this->attributes['id'];
    }

    /**
     * 获取 by id
     * 预留批量获取，目前只取最新的一个
     * @param $check_id
     * @return string
     */
    public function getPathById($check_id)
    {
        if (empty($check_id)) {
            return '';
        }
        $query = self::find();
        $query->select('file_path');
        $query->where(['=', 'check_id', $check_id]);
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

