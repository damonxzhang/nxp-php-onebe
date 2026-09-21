<?php

namespace frontend\controllers;

use Yii;
use backend\models\FccfpList;
use backend\models\FccfpExpireMethod;

class FebeApiController extends FrontendBaseController
{
    protected $unsetKey = [
        'fcmark_clean_date_left',
        'fcmark_clean_color',
        'fcmark_clean_time_limit',
        'fcmark_clean_start_process',
        'fcmark_clean_end_process',
        'fcmark_clean_start_time',
        'fcmark_clean_end_time',
        'fcmark_clean_use_date',
        'fcmark_clean_expiration_date',
        'fpfreflow_date_left',
        'fpfreflow_color',
        'fpfreflow_time_limit',
        'fpfreflow_start_process',
        'fpfreflow_end_process',
        'fpfreflow_start_time',
        'fpfreflow_end_time',
        'fpfreflow_use_date',
        'fpfreflow_expiration_date',
    ];

    /**
     * 获取清单列表数据接口
     * 请求方式 POST
     *  http://onebe.test/api/febe-api/get-list 
     */
    public function actionGetList()
    {
        //提交方式为POST
        $isPost = Yii::$app->request->isPost;
        if (!$isPost) {
            return $this->outputJson('false', '请求方法不正确!');
        }
        $beProcessArr = [
            'FCMARK',
            'FPF_REFLOW',
            'FPF_CLEAN',
            'BR',
            'FLUX_CLEAN',
        ];
        $resultData = [
            'total_number' => [
                'PRODUCTION' => 0,
                'FCMARK'     => 0,
                'FPF_REFLOW' => 0,
                'FPF_CLEAN'  => 0,
                'BR'         => 0,
                'FLUX_CLEAN' => 0,
            ],
            'data' => []
        ];
        $fccfpList = FccfpList::find()->where(['!=', 'status', FccfpList::STATUS_PASS])->andWhere(['in','nowspecname',['FCMARK','FPF_REFLOW','FPF_CLEAN','BR','FLUX_CLEAN']])->orderBy(['calibration_time' => SORT_DESC])->asArray()->all();
        if (empty($fccfpList)) {
            return $this->outputJson('true', 'success', $resultData);
        }
      
        foreach ($fccfpList as $fccfp) {
            $nowspecname = trim(strtoupper($fccfp['nowspecname']));
            $resultData['total_number'][$nowspecname] += 1;
            $resultData['total_number']['PRODUCTION'] += 1;
            
            if($nowspecname == 'FCMARK'){
                $fccfp['fcmark_freflow_date_left']     = $fccfp['fcmark_clean_date_left'];
                $fccfp['fcmark_freflow_clean_color']   = $fccfp['fcmark_clean_color'];
                $fccfp['fcmark_freflow_time_limit']    = $fccfp['fcmark_clean_time_limit'];
                $fccfp['fcmark_freflow_start_process'] = 'Marking';
                $fccfp['fcmark_freflow_end_process']   = 'Preflux&reflow';
                $fccfp['fcmark_freflow_start_time']    = $fccfp['fcmark_clean_start_time'];
                $fccfp['fcmark_freflow_end_time']      = $fccfp['fcmark_clean_end_time'];
                $fccfp['fcmark_freflow_use_date']      = $fccfp['fcmark_clean_use_date'];
                $fccfp['fcmark_freflow_expiration_date'] = $fccfp['fcmark_clean_expiration_date'];
            }elseif($nowspecname == 'FPF_REFLOW'){
                $fccfp['fcmark_freflow_date_left']     = $fccfp['fpfreflow_date_left'];
                $fccfp['fcmark_freflow_clean_color']   = $fccfp['fpfreflow_color'];
                $fccfp['fcmark_freflow_time_limit']    = $fccfp['fpfreflow_time_limit'];
                $fccfp['fcmark_freflow_start_process'] = 'Marking';
                $fccfp['fcmark_freflow_end_process']   = 'Preflux&reflow';
                $fccfp['fcmark_freflow_start_time']    = $fccfp['fpfreflow_start_time'];
                $fccfp['fcmark_freflow_end_time']      = $fccfp['fpfreflow_end_time'];
                $fccfp['fcmark_freflow_use_date']      = $fccfp['fpfreflow_use_date'];
                $fccfp['fcmark_freflow_expiration_date'] = $fccfp['fpfreflow_expiration_date'];
            }else{
                $fcmarkFreflowUseDate = '';
                if(!empty($fccfp['fpfreflow_end_time'])){
                    $diff = strtotime($fccfp['fpfreflow_end_time']) - strtotime($fccfp['nldpmc_trackout']);
                    $difftimeleft = $this->absolute_value($diff);
                    $hours    = floor($difftimeleft / 3600);
                    $minutes  = floor(($difftimeleft % 3600) / 60);
                    $fcmarkFreflowUseDate = $hours.'h'.$minutes.'m';
                }
                $fccfp['fcmark_freflow_date_left']     = '';
                $fccfp['fcmark_freflow_clean_color']   = 0;
                $fccfp['fcmark_freflow_time_limit']    = $fccfp['fcmark_clean_time_limit'];
                $fccfp['fcmark_freflow_start_process'] = 'Marking';
                $fccfp['fcmark_freflow_end_process']   = 'Preflux&reflow';
                $fccfp['fcmark_freflow_start_time']    = $fccfp['fcmark_clean_start_time'];
                $fccfp['fcmark_freflow_end_time']      = $fccfp['fpfreflow_end_time'];
                $fccfp['fcmark_freflow_use_date']      = $fcmarkFreflowUseDate;
                $fccfp['fcmark_freflow_expiration_date'] = '';
            }

            //剔除数据
            foreach ($fccfp as $key => $value) {
                if(in_array($key,$this->unsetKey)){
                    unset($fccfp[$key]);
                }
            }
            $resultData['data'][]   = $fccfp;
        }
        return $this->outputJson('true', 'success', $resultData);
    }

    //绝对值
    function absolute_value($number) {
        return $number < 0 ? -$number : $number;
    }

    /**
     * 搜索查询批号列表
     * 请求方式 POST
     */
    public function actionSearchList()
    {
        //提交方式为POST
        $isPost = Yii::$app->request->isPost;
        if (!$isPost) {
            return $this->outputJson('false', '请求方法不正确!');
        }

        $post = Yii::$app->request->post();
        if (!$post || !is_array($post)) {
            return $this->outputJson('false', '请求参数不能为空!');
        }

        //检查参数
        $base = ['containername','nowspecname'];
        $this->checkParameter($post, $base);
        if($post['nowspecname'] == 'ALL'){
            $fccfpList = FccfpList::find()->where(['!=', 'status', FccfpList::STATUS_PASS])->andWhere(['like','containername',$post['containername']])->andFilterWhere(['in','nowspecname',['FCMARK','FPF_REFLOW','FPF_CLEAN','BR','FLUX_CLEAN']])->orderBy(['calibration_time' => SORT_DESC])->asArray()->all();
        }else{
            $fccfpList = FccfpList::find()->where(['like','containername',$post['containername']])->andFilterWhere(['nowspecname'=>$post['nowspecname']])->orderBy(['calibration_time' => SORT_DESC])->asArray()->all();
        }
       
        if (empty($fccfpList)) {
            return $this->outputJson('false', '未能查询到此批号!');
        }

        $resultData = [];
        foreach ($fccfpList as $fccfp) {
            $nowspecname = trim(strtoupper($fccfp['nowspecname']));
            
            if($nowspecname == 'FCMARK'){
                $fccfp['fcmark_freflow_date_left']     = $fccfp['fcmark_clean_date_left'];
                $fccfp['fcmark_freflow_clean_color']   = $fccfp['fcmark_clean_color'];
                $fccfp['fcmark_freflow_time_limit']    = $fccfp['fcmark_clean_time_limit'];
                $fccfp['fcmark_freflow_start_process'] = 'Marking';
                $fccfp['fcmark_freflow_end_process']   = 'Preflux&reflow';
                $fccfp['fcmark_freflow_start_time']    = $fccfp['fcmark_clean_start_time'];
                $fccfp['fcmark_freflow_end_time']      = $fccfp['fcmark_clean_end_time'];
                $fccfp['fcmark_freflow_use_date']      = $fccfp['fcmark_clean_use_date'];
                $fccfp['fcmark_freflow_expiration_date'] = $fccfp['fcmark_clean_expiration_date'];
            }elseif($nowspecname == 'FPF_REFLOW'){
                $fccfp['fcmark_freflow_date_left']     = $fccfp['fpfreflow_date_left'];
                $fccfp['fcmark_freflow_clean_color']   = $fccfp['fpfreflow_color'];
                $fccfp['fcmark_freflow_time_limit']    = $fccfp['fpfreflow_time_limit'];
                $fccfp['fcmark_freflow_start_process'] = 'Marking';
                $fccfp['fcmark_freflow_end_process']   = 'Preflux&reflow';
                $fccfp['fcmark_freflow_start_time']    = $fccfp['fpfreflow_start_time'];
                $fccfp['fcmark_freflow_end_time']      = $fccfp['fpfreflow_end_time'];
                $fccfp['fcmark_freflow_use_date']      = $fccfp['fpfreflow_use_date'];
                $fccfp['fcmark_freflow_expiration_date'] = $fccfp['fpfreflow_expiration_date'];
            }else{
                $fcmarkFreflowUseDate = '';
                if(!empty($fccfp['fpfreflow_end_time'])){
                    $diff = strtotime($fccfp['fpfreflow_end_time']) - strtotime($fccfp['nldpmc_trackout']);
                    $difftimeleft = $this->absolute_value($diff);
                    $hours    = floor($difftimeleft / 3600);
                    $minutes  = floor(($difftimeleft % 3600) / 60);
                    $fcmarkFreflowUseDate = $hours.'h'.$minutes.'m';
                }
                $fccfp['fcmark_freflow_date_left']     = '';
                $fccfp['fcmark_freflow_clean_color']   = 0;
                $fccfp['fcmark_freflow_time_limit']    = $fccfp['fcmark_clean_time_limit'];
                $fccfp['fcmark_freflow_start_process'] = 'Marking';
                $fccfp['fcmark_freflow_end_process']   = 'Preflux&reflow';
                $fccfp['fcmark_freflow_start_time']    = $fccfp['fcmark_clean_start_time'];
                $fccfp['fcmark_freflow_end_time']      = $fccfp['fpfreflow_end_time'];
                $fccfp['fcmark_freflow_use_date']      = $fcmarkFreflowUseDate;
                $fccfp['fcmark_freflow_expiration_date'] = '';
            }

            //剔除数据
            foreach ($fccfp as $key => $value) {
                if(in_array($key,$this->unsetKey)){
                    unset($fccfp[$key]);
                }
            }
            $resultData[]   = $fccfp;
        }
        return $this->outputJson('true', 'success', $resultData);
    }

    /**
     * 过期列表接口
     * 请求方式 POST
     */
    public function actionExpireList()
    {
        //提交方式为POST
        $isPost = Yii::$app->request->isPost;
        if (!$isPost) {
            return $this->outputJson('false', '请求方法不正确!');
        }
        $post = Yii::$app->request->post();
        if (!$post || !is_array($post)) {
            return $this->outputJson('false', '请求参数不能为空!');
        }

        //检查参数
        $base = ['nowspecname'];
        $this->checkParameter($post, $base);
        if($post['nowspecname'] == 'ALL'){
            $post['nowspecname'] = '';
        }
        $beProcessArr = [
            'FCMARK',
            'FPF_REFLOW',
            'FPF_CLEAN',
            'BR',
            'FLUX_CLEAN',
        ];
        $resultData = [];
        $fccfpList  = FccfpList::find()->select('id,containername,nowspecname,calibration_time')->where(['status' => FccfpList::STATUS_EXPIRE])->andFilterWhere(['nowspecname'=>$post['nowspecname']])->orderBy(['id' => SORT_DESC])->asArray()->all();
        if (empty($fccfpList)) {
            return $this->outputJson('true', 'success', $resultData);
        }
       
        foreach ($fccfpList as $fccfp) {
            $nowspecname = trim(strtoupper($fccfp['nowspecname']));
            if(!in_array($nowspecname,$beProcessArr)){
                continue;
            }
            $resultData[]   = $fccfp;
        }
        return $this->outputJson('true', 'success', $resultData);
    }

    /**
     * 查看工序的处理方法
     * 请求方式 POST
     */
    public function actionExpire()
    {
        //提交方式为POST
        $isPost = Yii::$app->request->isPost;
        if (!$isPost) {
            return $this->outputJson('false', '请求方法不正确!');
        }
        $post = Yii::$app->request->post();
        if (!$post || !is_array($post)) {
            return $this->outputJson('false', '请求参数不能为空!');
        }
        //检查参数是否为空
        foreach ($post as $key => $value) {
            if (empty($value)) {
                return $this->outputJson('false', "请求参数'" . $key . "'不能为空!");
            }
        }

        //检查参数 传工序
        $base = ['nowspecname'];
        $this->checkParameter($post, $base);
       
        $data  = FccfpExpireMethod::find()->where(['status' => FccfpExpireMethod::STATUS_START, 'nowspecname' => $post['nowspecname']])->asArray()->one();
        if (!$data) {
            return $this->outputJson('false', '未能查询到此工序处理方法!');
        }
        return $this->outputJson('true', 'success', $data);
    }
    
}
