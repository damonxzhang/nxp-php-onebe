<?php

  namespace backend\models;


  class NewGembaKeys extends Base
  {
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
      return 'nxp_new_gemba_keys';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
      return [
        [['gemba_id','created_at','shift','area'], 'integer'],
        [['keys'], 'string']
      ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
      return [
        'id'             => 'ID',
        'gemba_id'       => 'gembaid',
        'keys'           => '关键词',
        'created_at'     => '创建时间',
        'shift'          => '班别',
        'area'           => '区域',
      ];
    }
  }


