<?php
use yii\web\JsExpression;
use daixianceng\echarts\ECharts;
use backend\widgets\SearchActiveForm;
use yii\helpers\Html;

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
    <xblock> 
     <p>
        <?= Html::a('类型柱状图表', ['gemba-echarts/type-chart'], ['class' => 'layui-btn ']) ?>
        <?= Html::a('区域柱状图表', ['gemba-echarts/area-chart'], ['class' => 'layui-btn ']) ?>
        <?= Html::a('完成百分比饼图', ['gemba-echarts/area-percent-pie-chart'], ['class' => 'layui-btn ']) ?>
        <?= Html::a('区域饼图', ['gemba-echarts/area-pie-chart'], ['class' => 'layui-btn ']) ?>
     </p>
    </xblock>
    <div class="layui-row">

        <?php $form = SearchActiveForm::begin([
            'method' => 'get',
        ]); ?>

        <div class="layui-input-inline">
            <?= $form->field($model, 'type')->dropDownList(\yii\helpers\ArrayHelper::merge(['' => '请选择类型'],\backend\models\NewGemba::$typeList)) ?>
        </div>
        <div class="layui-input-inline">
            <?= $form->field($model, 'area')->dropDownList(\yii\helpers\ArrayHelper::merge(['' => '请选择区域'],\backend\models\UserInfo::$areaArr)) ?>
        </div>
        <div class="layui-input-inline">
            <?= $form->field($model, 'start_date')->textInput(['class' => 'layui-input','id' => 'startTime','placeholder' => '请输入开始时间']) ?>
        </div>
        <div class="layui-input-inline">
            <?= $form->field($model, 'end_date')->textInput(['class' => 'layui-input endTime','id' => 'endTime','placeholder' => '请输入结束时间']) ?>
        </div>

        <?= Html::submitButton('<i class="layui-icon"></i>查询', ['class' => 'layui-btn']) ?>
        <?= Html::a('<i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新',$this->context->action->id, ['class' => 'layui-btn']) ?>

        <?php SearchActiveForm::end(); ?>

    </div>
<?= ECharts::widget([
    'responsive' => true,
    'options' => [
        'style' => 'height: 400px;width:1000px'
    ],
    'pluginOptions' => [
        'option' => [
            'title' => [
                'test' => '饼状图',
                'left'=> 'center',
                'top'=> 40,
                'textStyle'=> [
                    'color'=>'#ccc'
                ]
            ],
            'legend' => [
                'data' => [ 'Non-lead',  'Leaded',  'Plating', 'PM']
            ],
            'tooltip' => [
                'trigger'=> 'item',
                'formatter'=> '{b} : {c} ({d}%)'
            ],
            'series' =>[
                'type'=> 'pie',
                'radius'=> '50%',
                'center'=> ['50%', '50%'],
                'data'  => array_values($chartData),
            ]
        ]
    ]
]); ?>