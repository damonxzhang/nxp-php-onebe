<?php

namespace backend\models;

use Yii;

class ShelfMaterialsModel extends Base
{

    //大小物料
    const TYPE_ONE    = 1; //小物料
    const TYPE_TWO    = 2; //大物料

    public static $typeArr = [
        self::TYPE_ONE    => '小物料',
        self::TYPE_TWO    => '大物料',
    ];


     //是否显示NO BOX
    const NOBOXNO    = 0; //不显示
    const NOBOXYES   = 1; //显示
    public static $noboxArr = [
        self::NOBOXNO    => '不显示',
        self::NOBOXYES   => '显示',
    ];


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_shelf_materials_model';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'user_id','no_box'], 'integer'],
            [[
                'dm_nc',
                'part_no',
                'thawing_time',
                'expiry_time',
                'create_time',
                'update_time',
            ], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'           => 'ID',
            'user_id'      => '制单人id',
            'dm_nc'        => 'DM 12NC',
            'part_no'      => 'Part No',
            'type'         => '大小物料',
            'no_box'       => 'NO BOX',
            'thawing_time' => '解冻时间',
            'expiry_time'  => '解冻过期时间',
            'create_time'  => '创建时间',
            'update_time'  => '更新时间',
        ];
    }

    /**
     * 人员信息表
     * @return \yii\db\ActiveQuery
     */
    public function getUserinfo()
    {
        return $this->hasOne(UserInfo::className(), ['id' => 'user_id']);
    }

    /**
     * 添加数据
     * @param $data
     * @return bool
     */
    public function create($data)
    {
        $this->part_no      = trim($data['part_no']);
        $this->type         = $data['type'];
        $this->user_id      = trim($data['user_id']);
        $this->dm_nc        = $data['dm_nc'];
        $this->no_box       = $data['no_box'];
        $this->thawing_time = $data['thawing_time'];
        $this->expiry_time  = $data['expiry_time'];
        $this->create_time  = date('Y-m-d H:i:s', time());
        $this->update_time  = date('Y-m-d H:i:s', time());
        if (!$this->save()) {
            return false;
        }
        return true;
    }

    /**
     * 根据用户ID获取名称
     * @param $id
     * @return string
     */
    public function getPartNo($id)
    {
        if(empty($id)){
            return '';
        }
        $query = self::findOne(['id' => $id]);
        return empty($query) ? '' : $query->part_no;
    }

}
