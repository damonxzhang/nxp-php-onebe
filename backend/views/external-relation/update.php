<?php

use yii\helpers\Html;
use backend\models\UserInfo;
use yii\helpers\ArrayHelper;
use backend\models\UserStation;
/* @var $this yii\web\View */
/* @var $model backend\models\ExternalRelation */

$this->title = 'Update External Relation: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'External Relations', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="x-nav">
    <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">外部系统</a>
        <a href="<?= \yii\helpers\Url::to(['external-system/index'])?>">系统列表</a>
        <a href="<?= \yii\helpers\Url::to(['external-authority/index','pid' => $systemId])?>">权限列表</a>
        <a href="javascript:;">
            <cite>编辑用户</cite>
        </a>
    </span>
</div>
<div class="x-body layui-anim layui-anim-up">

    <?php $form = \yii\widgets\ActiveForm::begin(['options' => ['class' => 'layui-form']]); ?>


        <?= \backend\widgets\SelectSearch::widget([
            'name' => 'user',
            'text' => '用户',
            'columnName' => 'user_id',
            'value' => $userIdStr,
            'required' => true,
            'options' => [
                'multiple' => true,
                'style' => ['width'=> '800px'],
            ],
            'selectList' => [
                'section' => [
                    'text' =>'请选择区域',
                    'data' => UserInfo::$sectionArr,
                ],
                'position' => [
                    'text' =>'请选择职务',
                    'data'=>  UserInfo::$positionArr,
                ],
                'shift' => [
                    'text' =>'请选择班别',
                    'data'=> UserInfo::$shiftArr,
                ],

                'user-stations' => [
                    'text' =>'请选择站别',
                    'data'=> ArrayHelper::map((new \backend\models\UserStation())->getStation(),'id','station'),
                ],
            ]
        ])?>

        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>交接类型:
            </label>
            <div class="layui-input-inline" style="width: 800px;">
                <select name="status" lay-verify="required">
                    <?php foreach (\backend\models\ExternalRelation::$statusArr as $key => $value): ?>
                        <option value="<?= $key ?>"  <?= $status == $key ? 'selected':''?> ><?= $value ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    <div class="layui-form-item"  style="margin-top: 50px;">
        <label for="" class="layui-form-label">
        </label>
        <?= Html::button('保存', ['class' => 'layui-btn submit-btn',   'lay-submit'=>""]) ?>
    </div>

    <?php \yii\widgets\ActiveForm::end(); ?>

</div>

<?php
$js =<<<JS
    $('.submit-btn').click(function() {
        var formData = $('form').serialize();
        $.post('',formData,function(res) {
            if(res.status != 200){
                 layui.msg(res.msg);
            }
        },'json')
    });
JS;

$this->registerJs($js)
?>