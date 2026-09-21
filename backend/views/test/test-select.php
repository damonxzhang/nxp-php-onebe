<?php

use kartik\select2\Select2;
use yii\web\JsExpression;
?>
<form class="layui-form">
        <?= \backend\widgets\SelectSearch::widget([
            'name' => 'name',
            'value' => [],
            'initValueText' => [],
            'required' => true,
            'options' => [
                'class' => 'js-data-example-ajax',
                'multiple' => true,
                'style' => ['width'=> '800px'],
            ],
            'pluginOptions' => [
                'closeOnSelect' => false,
                'placeholder' => 'search ...',
                'allowClear' => true,
                'language' => [
                    'errorLoading' => new JsExpression("function () { return 'Waiting...'; }"),
                ],

                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('function(res) { return res.text; }'),
                'templateSelection' => new JsExpression('function (res) { return res.text; }'),
            ],
        ])?>
</form>

<?php



?>