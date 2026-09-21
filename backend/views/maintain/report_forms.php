<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use backend\models\UserInfo;
use daixianceng\echarts\ECharts;
use backend\widgets\SearchActiveForm;
use backend\models\Facility;
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
                'action' => ['report-forms'],
                'method' => 'get',
            ]); ?>

            <div class="layui-input-inline" style="width: 150px">
                <?= \backend\widgets\SearchUser::widget([
                    'name' => Html::getInputName($model, 'f_id'),
                    'value' => $model->f_id,
                    'allowClear' => true,
                    'initValueText' => Facility::getNumberById($model->f_id),
                    'url' => \yii\helpers\Url::to('/facility/find-facility'),
                    'placeholder' => '请输入设备号'
                ]);?>
            </div>

            <div class="layui-input-inline">
                <?= $form->field($model, 'type')->dropDownList(ArrayHelper::merge(['' => '请选择问题类型'],Maintain::$problem)) ?>
            </div>
            <div class="layui-input-inline">
                <?= $form->field($model, 'process')->dropDownList(ArrayHelper::merge(['' => '请选择工序'],Facility::$processList)) ?>
            </div>
            <div class="layui-input-inline">
                <?= $form->field($model, 'area')->dropDownList(ArrayHelper::merge(['' => '请选择区域'],UserInfo::$areaArr)) ?>
            </div>
            <div class="layui-input-inline">
                <?= $form->field($model, 'start_time')->textInput(['id' => 'startTime','autocomplete'=>"off",'placeholder' => '请输入时间']) ?>
            </div>
            <div class="layui-input-inline">
                <?= $form->field($model, 'end_time')->textInput(['id' => 'endTime','autocomplete'=>"off",'placeholder' => '请输入时间']) ?>
            </div>
            <?= Html::submitButton('<i class="layui-icon"></i>查询', ['class' => 'layui-btn']) ?>
            <?= Html::a('<i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新','report-forms', ['class' => 'layui-btn']) ?>

            <?php SearchActiveForm::end(); ?>

        </div>
        <?php if(!empty($model->f_id) && !empty($data)):?>
            <div style="width: 30%; height:400px;float: left;">
                <table class="table">
                    <tr>
                        <th><?=Facility::getNumberById($model->f_id)?></th>
                        <th>Process Step</th>
                        <th>Issue Qty</th>
                    </tr>
                    <?php
                    $html = '';
                    foreach ($data as $val){
                        $html .= '<tr><td> </td>';
                        $html .= '<td>'.$val['name'].'</td>';
                        $html .= '<td>'.$val['value'].'</td>';
                        $html .= '</tr>';

                    }
                    echo $html;
                    ?>
                </table>
            </div>

        <?php elseif (!empty($model->process) && !empty($data)):?>
        <div style="width: 30%; height:400px;float: left;">
            <table class="table">
                <tr>
                    <th>Saw Singulation</th>
                    <th>Machine Issue Qty</th>
                </tr>
                <?php
                    $html = '';
                    foreach ($data as $val){
                        $html .= '<tr>';
                        $html .= '<td>'.$val['name'].'</td>';
                        $html .= '<td>'.$val['value'].'</td>';
                        $html .= '</tr>';

                    }
                    echo $html;
                ?>
            </table>
        </div>

    </div>

        <?php elseif (!empty($data) && (!empty($model->start_time) || !empty($model->end_time) || !empty($model->type))):?>
        <div style="width: 30%; height:400px;float: left;">
            <table class="table">
                <tr>
                    <th>Process Step</th>
                    <th>Issue Qty</th>
                </tr>
                <?php
                    $html = '';
                    foreach ($data as $val){
                        $html .= '<tr>';
                        $html .= '<td>'.$val['name'].'</td>';
                        $html .= '<td>'.$val['value'].'</td>';
                        $html .= '</tr>';

                    }
                    echo $html;
                ?>
            </table>
        </div>
        <?php
            endif;
            if(!empty($data)):
        ?>
        <div style="width: 40%; height:400px;float: left;">
            <?= ECharts::widget([
                'responsive' => true,
                'options' => [
                    'style' => 'height: 400px;width:800px;'
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
                        'tooltip' => [
                            'trigger'=> 'item',
                            'formatter'=> '{b} : {c} ({d}%)'
                        ],
                        'legend' => [
                            'data' => array_values($list)
                        ],
                        'series' =>[
                            'type'=> 'pie',
                            'radius'=> '40%',
                            'center'=> ['50%', '80%'],
                            'data'  => $data,

                        ]
                    ]
                ]
            ]);
            ?>
        </div>
        <?php endif;?>
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