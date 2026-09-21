<?php

  namespace backend\models;


  class GembaBlockedwords extends Base
  {
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
      return 'nxp_gemba_blockedwords';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
      return [
        [['status','user_id'], 'integer'],
        [['name'], 'string', 'max' => 100],
        [['create_time'], 'string'],
      ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
      return [
        'id'             => 'ID',
        'name'           => '屏蔽词名称',
        'status'         => '状态',
        'create_time'    => '创建时间',
        'user_id'        => '创建人id',
      ];
    }

    public function create($data)
    {
      $this->name = trim($data['name']);
      $this->status      = $data['status'];
      $this->user_id     = $data['user_id'];
      $this->create_time = date('Y-m-d H:i:s', time());
      if (!$this->save(false)) {
        $error = array_values($this->getFirstErrors())[0];
        return $this->arrData(100, $error);
      }
      return $this->arrData(200, '添加成功');
    }

    public function edit($data)
    {
      $model          = self::findOne($data['id']);
      $model->name    = trim($data['name']);
      $model->status  = $data['status'];
      if (!$model->save(false)) {
        $error = array_values($model->getFirstErrors())[0];
        return $this->arrData(100, $error);
      }
      return $this->arrData(200, '更新成功');
    }

  }


