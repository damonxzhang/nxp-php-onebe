<?php

namespace backend\models;

class FccfpList extends Base
{

    const STATUS_EXECUTING = 0;
    const STATUS_PASS      = 1;
    const STATUS_WARN      = 2;
    const STATUS_EXPIRE    = 3;

    //物料状态
    public static $statusList = [
        self::STATUS_EXECUTING => '执行中',
        self::STATUS_PASS      => '已完成',
        self::STATUS_WARN      => '预警中',
        self::STATUS_EXPIRE    => '已过期'
    ];

    //显示颜色
    public static $colorList = [
        1 => 'red',
        2 => 'yellow',
        3 => 'orange',
        4 => 'gray',
        5 => 'green'
    ];

    /**
     * 各个工序对应名称
     * 整体各个工序流程FC_INSP-->SUB_PBAKE-->Premoldbake-->PremoldPlasma-->Molding-->PMC-->Marking-->Preflux&reflow-->Preflux&clean-->BallAttach-->Fluxclean-->BeSAW
     * Premoldbake-->PremoldPlasma-->Molding 24小时的执行时间
     * PremoldPlasma-->Molding  8小时的执行时间,这个8小时在上面的24小时执行以内
     * PMC-->Marking-->Preflux&reflow-->Preflux&clean  40小时执行时间,不包含PMC的执行时间,是以PMC执行时间结束为开始计算到Preflux&clean 执行结束40个小时
     * Preflux&reflow-->Preflux&clean 从这个Preflux&reflow 结束到 Preflux&clean 结束为8个小时,这个8个小时是在上面那40个小时以内
     * Preflux&clean-->BallAttach  12个小时的执行时间,从Preflux&clean 结束时间计算
     * BallAttach-->Fluxclean  60个小时的执行时间,从BallAttach结束时间计算
     * 整体是要计算从PMC结束时间为开始,到Fluxclean结束为止
     **/

    //PMC 40个小时的各个工序的列表
    public static $pmc = [
        'fcmark'    => 'Marking',
        'fpfREFLOW' => 'Preflux&reflow',
        'fpfCLEAN'  => 'Preflux&clean',
    ];

    //BALLAttach的各个工序的列表
    public static $ballAttach = [
        'br'        => 'BallAttach',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_fccfp_list';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                ['id', 'status','order'], 'integer'
            ],
            [
                [
                    'containername',
                    'nowspecname',
                    'nldpmc_trackout',
                    'fcmark_clean_date_left',
                    'fcmark_clean_time_limit',
                    'fcmark_clean_start_process',
                    'fcmark_clean_end_process',
                    'fcmark_clean_start_time',
                    'fcmark_clean_end_time',
                    'fcmark_clean_use_date',
                    'fcmark_clean_expiration_date',
                    'fpfreflow_date_left',
                    'fpfreflow_time_limit',
                    'fpfreflow_start_process',
                    'fpfreflow_end_process',
                    'fpfreflow_start_time',
                    'fpfreflow_end_time',
                    'fpfreflow_use_date',
                    'fpfreflow_expiration_date',
                    'fpfclean_date_left',
                    'fpfclean_time_limit',
                    'fpfclean_start_process',
                    'fpfclean_end_process',
                    'fpfclean_start_time',
                    'fpfclean_end_time',
                    'fpfclean_use_date',
                    'fpfclean_expiration_date',
                    'br_date_left',
                    'br_time_limit',
                    'br_start_process',
                    'br_end_process',
                    'br_start_time',
                    'br_end_time',
                    'br_use_date',
                    'br_expiration_date',
                    'fluxclean_date_left',
                    'fluxclean_time_limit',
                    'fluxclean_start_process',
                    'fluxclean_end_process',
                    'fluxclean_start_time',
                    'fluxclean_end_time',
                    'fluxclean_use_date',
                    'fluxclean_expiration_date',
                    'update_time',
                    'calibration_time'
                ], 
                'string'
            ],
        ];
    }

      /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'containername' =>    '批号',
            'nowspecname' =>    '当前所处工序',
            'nldpmc_trackout' =>    '出炉日期',
            'fcmark_clean_date_left' =>    'Marking ~Preflux clean 整个工序的剩余时间',
            'fcmark_clean_time_limit' =>    'Marking ~Preflux clean 时间上限',
            'fcmark_clean_start_process' =>    'Marking ~Preflux clean 开始工序',
            'fcmark_clean_end_process' =>    'Marking ~Preflux clean 结束工序',
            'fcmark_clean_start_time' =>    'Marking ~Preflux clean 开始时间',
            'fcmark_clean_end_time' =>    'Marking ~Preflux clean 结束时间',
            'fcmark_clean_use_date' =>    'Marking ~Preflux clean 已用时间',
            'fcmark_clean_expiration_date' => 'Marking ~Preflux clean 过期时间',
            'fpfreflow_date_left' =>    'Preflux&reflow clean 整个工序的剩余时间',
            'fpfreflow_time_limit' =>    'Preflux&reflow clean 时间上限',
            'fpfreflow_start_process' =>    'Preflux&reflow clean 开始工序',
            'fpfreflow_end_process' =>    'Preflux&reflow clean 结束工序',
            'fpfreflow_start_time' =>    'Preflux&reflow clean 开始时间',
            'fpfreflow_end_time' =>    'Preflux&reflow clean 结束时间',
            'fpfreflow_use_date' =>    'Preflux&reflow clean 已用时间',
            'fpfreflow_expiration_date' => 'Preflux&reflow clean 过期时间',
            'fpfclean_date_left' =>    'Preflux clean 整个工序的剩余时间',
            'fpfclean_time_limit' =>    'Preflux clean 时间上限',
            'fpfclean_start_process' =>    'Preflux clean 开始工序',
            'fpfclean_end_process' =>    'Preflux clean 结束工序',
            'fpfclean_start_time' =>    'Preflux clean 开始时间',
            'fpfclean_end_time' =>    'Preflux clean 结束时间',
            'fpfclean_use_date' =>    'Preflux clean 已用时间',
            'fpfclean_expiration_date' =>    'Preflux clean 过期时间',
            'br_date_left' =>    'Ball Attach 整个工序的剩余时间',
            'br_time_limit' =>    'Ball Attach 时间上限',
            'br_start_process' =>    'Ball Attach 开始工序',
            'br_end_process' =>    'Ball Attach 结束工序',
            'br_start_time' =>    'Ball Attach 开始时间',
            'br_end_time' =>    'Ball Attach 结束时间',
            'br_use_date' =>    'Ball Attach 已用时间',
            'br_expiration_date' =>    'Ball Attach 过期时间',
            'fluxclean_date_left' =>    'Flux Clean 整个工序的剩余时间',
            'fluxclean_time_limit' =>    'Flux Clean  时间上限',
            'fluxclean_start_process' =>    'Flux Clean  开始工序',
            'fluxclean_end_process' =>    'Flux Clean  结束工序',
            'fluxclean_start_time' =>    'Flux Clean  开始时间',
            'fluxclean_end_time' =>    'Flux Clean  结束时间',
            'fluxclean_use_date' =>    'Flux Clean  已用时间',
            'fluxclean_expiration_date' =>    'Flux Clean  过期时间',
            'update_time' =>    '更新时间',
            'status' =>    '状态  0执行中 1已完成  2预警 3过期',
        ];
    }

}

