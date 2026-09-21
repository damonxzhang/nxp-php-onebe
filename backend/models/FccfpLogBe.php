<?php

namespace backend\models;

class FccfpLogBe extends Base
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_fccfp_log_be';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['params','create_time'], 'string'],
        ];
    }

    /**
     * 添加数据
     * @param $data
     * @return bool
     */
    public function create($data)
    {
        $this->params       = $data['param'];
        $this->create_time  = date('Y-m-d H:i:s', time());
        $this->save();
        return true;
    }

}