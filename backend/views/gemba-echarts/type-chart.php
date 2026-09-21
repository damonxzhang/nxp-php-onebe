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
    'pluginEvents' => [
        'click' => [
            new JsExpression('function (params) {console.log(params)}'),
            new JsExpression('function (params) {console.log("ok")}')
        ],
        'legendselectchanged' => new JsExpression('function (params) {console.log(params.selected)}')
    ],
    'pluginOptions' => [
        'option' => [
            'title' => [
                'text' => '折线图堆叠'
            ],
            'tooltip' => [
                'trigger' => 'axis'
            ],
            'legend' => [
                'data' => array_values(\backend\models\NewGemba::$typeList)
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
                'name' => '周别',
                'type' => 'category',
                'boundaryGap' => true,
                'data' => $category
            ],
            'yAxis' => [
                'type' => 'value'
            ],
            'series' => array_values($chartData),
        ]
    ]
]); ?>