<?php

namespace backend\models;

class FccfpExpireMethod extends Base
{

    const STATUS_START  = 1;
    const STATUS_DISABLE= 2;

    //物料状态
    public static $statusList = [
        self::STATUS_START   => '启用',
        self::STATUS_DISABLE => '禁用'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_fccfp_expire_method';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                ['id', 'status'], 'integer'
            ],
            [
                ['nowspecname','method'], 'string'
            ],
        ];
    }

}
