<?php
namespace backend\models;

use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;

class GembaChart extends Model {

    public $chartData = [];

    /**
     * @param $chartData
     * @param $name
     * @param $label
     * @param int $barWidth
     * @param string $stack
     * @param string $type
     * @param array $params
     * @return mixed
     */
    public function setShowChartData($chartData,$name,$label = [],$type = 'bar',$barWidth = 20,$stack='状态',$params = []){
        foreach ($chartData as $key => &$val){
            if(!empty($label[$key])){
                foreach ($val as $k => &$v){
                    $value = [];
                    if( !empty($label[$key][$k]) ){
                        $value['label'] = $label[$key][$k];
                    }
                    $value['color'] = 'red';
                    $value['value'] = $v;
                    $v = $value;
                }
            }
            $val = [
                'barWidth' => $barWidth,
                'stack' => $stack,
                'type' => $type,
                'data' => array_values($val),
            ];
            if(is_callable($name)){
                $val['name'] = call_user_func($name,$val,$key);
            }else{
                $val['name'] = $name;
            }
            if(!empty($params)){
                ArrayHelper::merge($val,$params);
            }
        }
        return $chartData;
    }

    /**
     * 设置百分比label
     * @param $chartData
     * @param $category
     * @return array
     */
    public function setPercentLabel($chartData,$category){
        $label = [];
        foreach (UserInfo::$areaArr as $key => $val){
            $sum = $chartData[$category['finish']][$key] + $chartData[$category['perform']][$key];
            $finish = $chartData[$category['finish']][$key] ;
            if($sum > 0){
                $percent  = round( $finish / $sum * 100 );
            }else{
                $percent = 0;
            }
            $label[$category['finish']][$key] = [
                'show' => true,
                'formatter' =>  new JsExpression('function(obj){ 
                    return  "'.$percent.'%";
                }'),
                'position' => 'top'
            ];
        }
        return $label;
    }

    /**
     * 设置饼状图区域
     * @param $data
     * @param $result
     */
    public function setChartParamsByArea($data,&$result,$category){
        foreach ($data as $value){
            $k = $value['status'] == NewGemba::STATUS_PERFORM ? $category['finish'] : $category['perform'];
            if(!isset($result[$k][$value['area']])){
                $result[$k][$value['area']] = 0;
            }
            $result[$k][$value['area']] += 1;
        }
        foreach ($result as &$val){
            foreach (UserInfo::$areaArr as $key => $area){
                if(!in_array($key,array_keys($val))){
                    $val[$key] = 0;
                }
            }
            ksort($val);
        }
        foreach ($category as $v){
            if(empty($result[$v])){
                $result[$v] = [];
            }
        }
        ksort($result);
    }


    /**
     * @param $data
     * @param $result
     * @param $category
     * @return array
     */
    public function setChartParamsByW($data,&$result,&$category){
        foreach ($data as $key => $value){
            $ww = 'ww'.date('W',$value['create_time']);
            if(empty($result[$value['type']][$ww])){
                foreach (NewGemba::$typeList as $ke => $valu){
                    $result[$value['type']][$ww] = 0;
                }
            }
            if(!in_array($ww,$category)){
                $category[] = $ww;
            }
            if (!empty(NewGemba::$typeList[$value['type']])){
                $result[$value['type']][$ww] +=1;
            }
        }
        foreach ($result as &$value){
            foreach ($category as $w){
                if(!in_array($w,array_keys($value))){
                    $value[$w] = 0;
                }
            }
            ksort($value,SORT_NATURAL);
        }
    }


    /**
     * @param $data
     * @param $arr
     * @return array
     */
    public function setPieChartParamsByArea($data,$arr){

        $params = [];
        foreach ($arr as $key => $value){
            if(!isset($params[$key])){
                $params[$key] = [
                    'value' => 0,
                    'name' => $value,
                ];
            }
            foreach ($data as $k => $v){
                if($key == $v['area']){
                    $params[$key]['value'] += 1;
                }
            }
        }
        return $params;
    }
    /**
     * @param $data
     * @param $arr
     * @return array
     */
    public function setPieChartParamsByFinish($data,$arr){
        $params = [];
        foreach ($data as $k => $v){
            if(NewGemba::STATUS_PERFORM == $v['status']){
                if(!isset($params[$arr['perform']])){
                    $params[$arr['perform']] = [
                        'value' => 0,
                        'name' =>  '实施中',
                    ];
                }

                $params[$arr['perform']]['value'] += 1;
            } else{
                if(!isset($params[$arr['finish']])){
                    $params[$arr['finish']] = [
                        'value' => 0,
                        'name' => '已完成',
                    ];
                }
                $params[$arr['finish']]['value'] += 1;
            }

        }
        return $params;
    }


}