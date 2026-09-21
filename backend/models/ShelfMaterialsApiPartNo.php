<?php

namespace backend\models;
use Yii;

class ShelfMaterialsApiPartNo extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_shelf_materials_api_part_no';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'all_qty','qty','beilv','boxbeilv'], 'safe'],
            [[
                'part_no',
                'guid',
                'unit',
                'create_time',
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
            'part_no'     => '物料型号',
            'guid'        => '唯一标识',
            'qty'         => '可用数量',
            'all_qty'     => '库存数量',
            'unit'        => '主单位',
            'beilv'       => '主转换倍率',
            'boxbeilv'    => '箱的转换倍率',
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
        $this->part_no      = $data['part_no'];
        $this->guid         = $data['guid'];
        $this->qty          = $data['qty'];
        $this->all_qty      = $data['all_qty'];
        $this->unit         = $data['unit'];
        $this->beilv        = $data['beilv'];
        $this->boxbeilv     = $data['boxbeilv'];
        $this->create_time  = date('Y-m-d H:i:s', time());
        $this->save();
        return true;
    }

}
