<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use backend\models\UserInfo;
use daixianceng\echarts\ECharts;
use backend\widgets\SearchActiveForm;
use yii\web\JsExpression;
use backend\models\Maintain;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\Maintain */
/* @var $dataProvider yii\data\ActiveDataProvider */
ECharts::$dist = ECharts::DIST_FULL;
ECharts::registerMap(['china', 'province/beijing']);
?>
    <div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">设备异常</a>
        <a href="javascript:;">
          <cite>异常列表</cite>
        </a>
      </span>
    </div>

    <div class="x-body">

        <div class="layui-row">

            <?php $form = SearchActiveForm::begin([
                'action' => ['report'],
                'method' => 'get',
            ]); ?>

            <div class="layui-input-inline">
                <?= $form->field($model, 'area')->dropDownList(ArrayHelper::merge(['' => '请选择区域'],UserInfo::$areaArr)) ?>
            </div>
            <div class="layui-input-inline">
                <?= $form->field($model, 'start_time')->textInput(['id' => 'startTime','autocomplete'=>"off",'placeholder' => '请选择开始时间']) ?>
            </div>
            <div class="layui-input-inline">
                <?= $form->field($model, 'end_time')->textInput(['id' => 'endTime','autocomplete'=>"off",'placeholder' => '请选择结束时间']) ?>
            </div>

            <?= Html::submitButton('<i class="layui-icon"></i>查询', ['class' => 'layui-btn']) ?>
            <?= Html::a('<i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新','report', ['class' => 'layui-btn']) ?>

            <?php SearchActiveForm::end(); ?>

        </div>
        <div style="width:620px;padding-bottom:50px; ">
            <table lay-filter="table">
                <thead>
                <tr>
                    <th lay-data="{field:'date',width:400}">日期</th>
                    <th lay-data="{field:'operate',width:200}">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php
                    if(!empty($recortData)){
                        $html = '';

                        foreach ($recortData as $val){
                            $searchKey = \yii\helpers\Json::decode($val['searchKey'],1);
                            $searchKey = array_map(function($data){ return date('Y-m-d',$data);},$searchKey);
                            $html .= "<tr>";
                            $html .= "<td>".date('Y年m月d日',$val['create_time'])."</td>";
                            $html .= "<td>".Html::a('操作', ['/boss-maintain/index', 'BossMaintainSearch' => $searchKey   ],['class' => 'layui-btn'])."</td>";
                            $html .= "</tr>";
                        }
                        echo $html;
                    }
                ?>
                </tbody>
            </table>
        </div>
        <div style="width: 40%; height:400px;float: left;">
            <?php
                if(!empty($barData)){
                    $new_data = [];
                    foreach ($barData as $key => $val){
                        $new_data[] = [
                            'barWidth' => '20',
                            'name' => UserInfo::$areaArr[$key],
                            'type' => 'bar',
                            'data' => $val
                        ];
                    }
                    echo ECharts::widget([
                        'responsive' => true,
                        'options' => [
                            'style' => 'height: 400px;width:800px;'
                        ],
                        'pluginOptions' => [
                            'option' => [

                                'tooltip' => [
                                    'trigger' => 'axis'
                                ],
                                'legend' => [
                                    'data' => array_values( UserInfo::$areaArr)
                                ],
                                'grid' => [
                                    'left' => '10%',
                                    'right' => '10%',
                                    'bottom' => '10%',
                                    'containLabel' => true
                                ],
                                'xAxis' => [
                                    'name' => '日期',
                                    'type' => 'category',
                                    'data' => $barCategory
                                ],
                                'yAxis' => [
                                    'type' => 'value'
                                ],
                                'series' => $new_data
                            ]
                        ]
                    ]);
                }

            ?>
        </div>
        <div style="width: 5%; height:400px;float: left;"></div>
        <div style="width: 40%; height:400px;float: left;">
            <?php
                if(!empty($lineData)) {
                    $new_line_data = [];
                    foreach ($lineData as $key => $val) {
                        $new_line_data[] = [
//                            'barWidth' => '20',
                            'name' => Maintain::$problem[$key] ,
                            'type' => 'line',
                            'data' => $val
                        ];
                    }
                    echo ECharts::widget([
                        'responsive' => true,
                        'options' => [
                            'style' => 'height: 400px;width:800px;'
                        ],
                        'pluginEvents' => [
                            'click' => [
                                new JsExpression('function (params) {console.log(params)}'),
                                new JsExpression('function (params) {console.log("ok")}')
                            ],
                            'legendselectchanged' => new JsExpression('function (params) {console.log(params.selected)}')
                        ],
                        'pluginOptions' => [
                            'option' => [
                                'tooltip' => [
                                    'trigger' => 'axis'
                                ],
                                'legend' => [
                                    'data' => array_values( Maintain::$problem)
                                ],
                                'grid' => [
                                    'left' => '3%',
                                    'right' => '4%',
                                    'bottom' => '3%',
                                    'containLabel' => true
                                ],
                                'toolbox' => [
                                    'feature' => [
                                        'saveAsImage' => []
                                    ]
                                ],
                                'xAxis' => [
                                    'name' => '日期',
                                    'type' => 'category',
                                    'boundaryGap' => false,
                                    'data' => $lineCategory
                                ],
                                'yAxis' => [
                                    'type' => 'value'
                                ],
                                'series' =>   $new_line_data
                            ]
                        ]
                    ]);
                }
            ?>
        </div>
    </div>

<?php
$js = <<<JS
        layui.use('laydate', function(){
        var laydate = layui.laydate;
         
        laydate.render({
            elem: '#startTime' //指定元素 
        });
        laydate.render({
            elem: '#endTime' //指定元素 
        });
    });

    layui.use('table', function(){
        var table = layui.table; 
        table.init('table', {
            height: 200,//设置高度
            limit: 1000 
        }); 
    });

JS;
$this->registerJs($js);