<?php

namespace backend\models;

use yii\helpers\ArrayHelper;

class LogHandover extends Base
{
  
    const STATUS_OPEN  = 1;
    const STATUS_CLOSE = 2;

    //启用状态
    public static $statusArr = [
        self::STATUS_OPEN  => '延续',
        self::STATUS_CLOSE => '不延续',
    ];

    const CLASSES_DAY  = 1;
    const CLASSES_NIGHT = 2;

    //启用状态
    public static $classesArr = [
        self::CLASSES_DAY  => '白班',
        self::CLASSES_NIGHT => '夜班',
    ];

    const COPULATE_WAIT       = 0;//已经创建完成等待提交
    const COPULATE_SUBMIT     = 1;//已经提交等待确认
    const COPULATE_VERIFY     = 2;//已经点击确认按钮
    const COPULATE_GO         = 3;//已经点击确认按钮但是此日志是延续的状态
    const COPULATE_ADDITIONAL = 4;//已经补充完信息


    const TYPE_MATERIAL   = '物料';//已经创建完成等待提交
    const TYPE_APPLIANCES = '设备';//已经提交等待确认
    const TYPE_COMMENT    = '备注';//已经点击确认按钮

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_log_handover';
    }


    /**
     * 添加
     * @param $data
     * @return array
     */
    public function create($data)
    {
        $this->handover_id            = 0;
        $this->type                   = ArrayHelper::getValue($data, 'type', 0);
        $this->type_name              = ArrayHelper::getValue($data, 'type_name', '');
        $this->content                = trim($data['content']);
        $this->user_id                = $data['user_id'];
        $this->chinese_name           = $data['chinese_name'];
        $this->pid                    = $data['pid'];
        $this->shift                  = $data['shift'];
        $this->section                = $data['section'];
        $this->status                 = $data['status'];
        $this->copulate               = self::COPULATE_WAIT;
        $this->copulate_user_id       = 0;
        $this->copulate_chinese_name  = '';
        $this->copulate_pid           = '';
        $this->copulate_shift         = 0;
        $this->copulate_section       = 0;
        $this->copulate_time          = '';
        $this->type_id                = ArrayHelper::getValue($data, 'type_id', 0);
        $this->type_title             = ArrayHelper::getValue($data, 'type_title', '');
        $this->batch_id               = ArrayHelper::getValue($data, 'batch_id',  0);
        $this->batch_title            = ArrayHelper::getValue($data, 'batch_title', '');
        $this->comment_id             = ArrayHelper::getValue($data, 'comment_id', 0);
        $this->comment_title          = ArrayHelper::getValue($data, 'comment_title', '');
        $this->device_type_id         = ArrayHelper::getValue($data, 'device_type_id', 0);
        $this->device_type_title      = ArrayHelper::getValue($data, 'device_type_title', '');
        $this->is_sensitive_words     = ArrayHelper::getValue($data, 'is_sensitive_words', 0);
        $this->date                   = '';
        $this->create_time            = date('Y-m-d H:i:s', time());
        $this->update_time            = date('Y-m-d H:i:s', time());
        $this->submit_time            = '';
      
        $this->is_del                 = 0;
        $this->del_time               = '';
        $this->content_audit          = ArrayHelper::getValue($data, 'content_audit', '');
        $this->is_audit               = ArrayHelper::getValue($data, 'is_audit', 0);
        $this->is_del_audit           = 0;
        $this->del_time_audit         = '';
        $this->classes                = 0;
        if (!$this->save(false)) {
            $error = array_values($this->getFirstErrors())[0];
            return $this->arrData(100, $error);
        }
        return $this->arrData(200, '添加成功');
    }
    
}


