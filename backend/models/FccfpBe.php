<?php

namespace backend\models;

class FccfpBe extends Base
{
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
    public static $processList = [
        'FDB_REFLOW',
        'DB_FCLEAN',
        'SUB_UNLOAD',
        'FC_INSP',
        'SUB_PBAKE',
        'PREM_PLS',
        'PSSWND',
        'FMOLD',
        'NLD_PMC',
        'FCMARK',
        'FPF_REFLOW',
        'FPF_CLEAN',
        'BR',
        'FLUX_CLEAN',
        'BE_SAW',
    ];

    //各个工序之前的名词和工序名称的对应关系    
    // public static $comparison_of_each_process = [
    //     'fcINSP'    => 'FC_INSP',
    //     'subPBAKE'  => 'SUB_PBAKE',
    //     'premPLS'   => 'Premoldbake',
    //     'psswnd'    => 'PremoldPlasma',
    //     'fmold'     => 'Molding',
    //     'nldPMC'    => 'PMC',
    //     'fcmark'    => 'Marking',
    //     'fpfREFLOW' => 'Preflux&reflow',
    //     'fpfCLEAN'  => 'Preflux&clean',
    //     'br'        => 'BallAttach',
    //     'fluxCLEAN' => 'Fluxclean',
    //     'beSAW'     => 'BeSAW',
    // ];
    


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
        return 'nxp_fccfp_be';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                ['id',
                'besaw',
                'fcinsp',
                'subpbake',
                'prempls',
                'psswnd',
                'fmold',
                'nldpmc',
                'fcmark',
                'fpfreflow',
                'fpfclean',
                'br',
                'fluxclean'
                ], 
                'integer'
            ],
            [
                [
                'containername',
                'nowspecname',
                'fcinsp_movein',
                'fcinsp_trackin',
                'fcinsp_trackout',
                'fcinsp_moveout',
                'subpbake_movein',
                'subpbake_trackin',
                'subpbake_trackout',
                'subpbake_moveout',
                'prempls_movein',
                'prempls_trackin',
                'prempls_trackout',
                'prempls_moveout',
                'psswnd_movein',
                'psswnd_trackin',
                'psswnd_trackout',
                'psswnd_moveout',
                'fmold_movein',
                'fmold_trackin',
                'fmold_trackout',
                'fmold_moveout',
                'nldpmc_movein',
                'nldpmc_trackin',
                'nldpmc_trackout',
                'nldpmc_moveout',
                'fcmark_movein',
                'fcmark_trackin',
                'fcmark_trackout',
                'fcmark_moveout',
                'fpfreflow_movein',
                'fpfreflow_trackin',
                'fpfreflow_trackout',
                'fpfreflow_moveout',
                'fpfclean_movein',
                'fpfclean_trackin',
                'fpfclean_trackout',
                'fpfclean_moveout',
                'br_movein',
                'br_trackin',
                'br_trackout',
                'br_moveout',
                'fluxclean_movein',
                'fluxclean_trackin',
                'fluxclean_trackout',
                'fluxclean_moveout',
                'besaw_movein',
                'besaw_trackin',
                'besaw_trackout',
                'besaw_moveout',
                'create_time'
                ], 
                'string'
            ],
        ];
    }
}
