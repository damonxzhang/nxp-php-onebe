<?php

namespace backend\models;
use Yii;

class ShelfMaterialsApiLog extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_shelf_materials_api_log';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'param',
                'result',
                'url',
                'create_time',
                'address',
            ], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'param'       => '请求的参数',
            'result'      => '返回的结果',
            'url'         => '请求的地址',
            'address'     => '请求的API接口方法',
            'create_time' => '创建时间',
        ];
    }
 
    /**
     * 添加数据
     * @param $data
     * @return bool
     */
    public function create($data)
    {
        $this->param        = $data['param'];
        $this->result       = $data['result'];
        $this->url          = trim($data['url']);
        $this->address      = $data['address'];
        $this->create_time  = date('Y-m-d H:i:s', time());
        $this->save();
        return true;
    }

}
