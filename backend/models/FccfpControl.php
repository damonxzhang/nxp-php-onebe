<?php

namespace backend\models;

class FccfpControl extends Base
{

    const WAIT_BAKE_WATE         = 0;
    const WAIT_BAKE_SUCCESS      = 1;
    //waitBake
    public static $waitBakeList = [
        self::WAIT_BAKE_WATE    => '待执行',
        self::WAIT_BAKE_SUCCESS => '已完成'
    ];

    const DRAG_DROP_BAKE_WATE      = 0;
    const DRAG_DROP_BAKE_SUCCESS   = 1;
    const DRAG_DROP_BAKE_OVER      = 2;
  
    //drag_drop_bake
    public static $dragBropBakeList = [
        self::DRAG_DROP_BAKE_WATE    => '待拖拽',
        self::DRAG_DROP_BAKE_SUCCESS => '已拖拽',
        self::DRAG_DROP_BAKE_OVER    => '已完成'
    ];
      
    const SUBPBAKE_STATUS_WATE      = 1;
    const SUBPBAKE_STATUS_SUCCESS   = 2;
    const SUBPBAKE_STATUS_OVER      = 3;
  
    //subpbake_status
    public static $subpbakeStatusList = [
        self::SUBPBAKE_STATUS_WATE    => '待发信',
        self::SUBPBAKE_STATUS_SUCCESS => '已发信',
        self::SUBPBAKE_STATUS_OVER    => '已完成'
    ];


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_fccfp_control';
    }

}
