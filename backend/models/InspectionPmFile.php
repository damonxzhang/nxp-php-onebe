<?php

namespace backend\models;

use Yii;

class InspectionPmFile extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_inspection_pm_file';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','pm_id','pm_check_id'], 'integer'],
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
            'pm_id'         => 'pm_id',
            'pm_check_id'   => 'pm_check_id',
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


