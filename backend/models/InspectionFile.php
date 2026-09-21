<?php

namespace backend\models;

use Yii;

class InspectionFile extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_inspection_file';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['file_path', 'create_time'], 'string']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'file_path'     => '文件路径',
            'create_time'   => '创建时间'
        ];
    }

    /**
     * @param $data
     * @return bool
     */
    public function create($data)
    {
        $this->file_path      = $data['file_path'];
        $this->create_time    = date('Y-m-d H:i:s', time());
        if (!$this->save(false)) {
            return FALSE;
        }
        return $this->attributes['id'];
    }
}


