<?php
use backend\models\Ir;
use yii\helpers\Html;
use backend\models\IrUser;
use yii\web\JsExpression;
use backend\models\UserInfo;
use yii\helpers\ArrayHelper;
use backend\widgets\LayuiActiveForm;
?>
<form class="layui-form" action="">
    <table class="layui-table">
        <tr style="width: 100%;">
            <th rowspan="4" class="leftTitle">建<br/>议<br/>人<br/>填<br/>写</th>
            <td colspan="8" class="noborderRight">
                <label class="layui-form-label leftLable">主题：</label>
                <div class="layui-input-block rightbox">
                    <input type="text" value="<?=$model->subject?>" disabled required lay-verify="required" autocomplete="off" class="layui-input">
                </div>
            </td>
            <td colspan="4" class="noborderLeft">
                <label class="layui-form-label leftLable01">填表日期：</label>
                <div class="layui-input-block rightbox01">
                    <input type="text" disabled value="<?= date('Y-m-d',$model->create_time)?>" required lay-verify="required" autocomplete="off" class="layui-input">
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="3" class="noborder">
                <label class="layui-form-label leftLable01 ">建议类别：</label>
            </td>
            <td colspan="9" class="noborder">
                <?php foreach ($model::$adviceTypes as  $key => $val):?>
                    <input type="radio" disabled <?= $model->advice_type == $key  ? 'checked': '';?> name="<?= Html::getInputName($model,'advice_type')?>" value="<?=$key?>" title="<?= $val ?>">
                <?php endforeach;?>
            </td>
        </tr>
        <tr>
            <td colspan="12">
                <div id="">目前状况：</div>
                <div class="">
                    <textarea name="" disabled rows="7" disabled style="width: 100%;"><?=$model->old_status?></textarea>
                </div>
                <div id="">建议内容：（请尽量与相关人员讨论后填写）</div>
                <div class="">
                    <textarea name="" disabled rows="7" disabled style="width: 100%;"><?=$model->advice_content?></textarea>
                </div>
                <div class="layui-upload">
                    <button type="button" class="layui-btn" id="test2">图片上传</button>
                    <blockquote class="layui-elem-quote layui-quote-nm" style="margin-top: 10px;">
                        预览图：
                        <div class="layui-upload-list" >
                            <?php

                            if($model->old_images):
                                foreach ($model->old_images as  $val):
                                    if(empty($model->images_path[$val])) continue;
                                    ?>
                                    <span class="itemimg">
                                        <img src="<?=$model->images_path[$val]?>" class="layui-upload-img show-img">
                                    </span>
                                <?php
                                endforeach;
                            endif;
                            ?>
                        </div>
                    </blockquote>
                </div>
            </td>

        </tr>
        <tr>
            <td colspan="2" class="noborderRight">
                <label class="layui-form-label leftLable">姓名：</label>
                <div class="layui-input-block rightbox">
                    <input type="text" disabled value="<?= $model->createUserInfo->chinese_name?>" required lay-verify="required" autocomplete="off" class="layui-input">
                </div>
            </td>
            <td colspan="2" class="noborderRight noborderLeft">
                <label class="layui-form-label leftLable">CID：</label>
                <div class="layui-input-block rightbox">
                    <input type="text" disabled value="<?= $model->createUserInfo->badge_code?>" required lay-verify="required" autocomplete="off" class="layui-input">
                </div>
            </td>

            <td colspan="2" class="noborderRight noborderLeft">

            <td class="boderstyle"><label class="layui-form-label leftLable">gemba小组：</label></td>
            <td  class="boderstyle">
                <input type="text" name="" disabled required lay-verify="required"  value="<?=\backend\models\Team::getTeamName($model->createUserInfo->team)?>"  autocomplete="off" class="layui-input">
            </td>
            </td>
            <td colspan="2" class="noborderRight noborderLeft">
                <label class="layui-form-label leftLable">班次：</label>
                <div class="layui-input-block rightbox">
                    <input type="text" disabled value="<?=$userInfo::$shiftArr[$model->createUserInfo->shift]?>" required lay-verify="required" autocomplete="off" class="layui-input">
                </div>
            </td>
            <td colspan="2" class="noborderLeft">
                <label class="layui-form-label leftLable">部门：</label>
                <div class="layui-input-block rightbox">
                    <input type="text" disabled value="<?=$userInfo::$sectionArr[$model->createUserInfo->section] ?>" required lay-verify="required" autocomplete="off" class="layui-input">
                </div>
            </td>
        </tr>
    </table>
</form>
<!--主管意见-->

<form class="layui-form" action="">
    <table class="layui-table" width="100%">
        <tr style="width: 100%;">
            <th rowspan="2" class="leftTitle">主<br/>管<br/>意<br/>见</th>
            <td colspan="12">
                <label class="layui-form-label leftLable02">不及格IR评判标准：</label>
                <div class="layui-input-block rightbox02">
                    <select disabled name="<?=Html::getInputName($model,'standard')?>">
                        <?php foreach($model::$standards as $key => $val):?>
                            <option value="<?=$key?>" <?= $key == $model->standard ? 'selected' : ''  ?> ><?=$val?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="4" class="noborderRight">
                <label class="layui-form-label leftLable">初评：</label>
                <div class="layui-input-block rightbox">
                    <input type="text" disabled name="<?=Html::getInputName($model,'director_advice')?>" value="<?=$model->director_advice? : ''?>"  required lay-verify="required" autocomplete="off" class="layui-input">
                </div>
            </td>
            <td colspan="4" class="noborderLeft noborderRight">
                <label class="layui-form-label leftLable02">直接主管签字：</label>
                <div class="layui-input-block rightbox02">
                    <input type="text" disabled value="<?=(new \backend\models\UserInfo())->getUserName($model->director_user) ?>" required lay-verify="required" autocomplete="off" class="layui-input">
                </div>
            </td>
            <td colspan="4" class="noborderLeft">
                <label class="layui-form-label leftLable01">评估日期：</label>
                <div class="layui-input-block rightbox01">
                    <input type="text" disabled id="startTime" value="<?= date('Y-m-d',$model->director_submit_time?: time())?>" required lay-verify="required" autocomplete="off"
                           class="layui-input">
                </div>
            </td>
        </tr>
    </table>
</form>
<!--评估人建议-->
<?php
if($model->status != Ir::STATUS_DIE && $model->status != Ir::STATUS_APPEAL ) {
    $form = LayuiActiveForm::begin([
        'fieldConfig' => [
            'template' => ' {input} ',
        ],
    ]) ?>

    <table class="layui-table" width="100%">
        <tr style="width: 100%;">
            <th rowspan="3" class="leftTitle">评<br/>估<br/>人<br/>建<br/>议</th>
            <td colspan="6" class="noborderRight">
                <label class="layui-form-label leftLable02">建议是否接受：</label>
                <div class="layui-input-block rightbox02">
                    <select name="<?= Html::getInputName($model, 'assess_status') ?>" lay-verify="required">
                        <option value="">请选择</option>
                        <?php foreach ($model::$assessStatusArr as $key => $value): ?>
                            <option value="<?= $key ?>" <?= $model->assess_status === $key ? 'selected' : '' ?>><?= $value ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </td>
            <td colspan="6" class="noborderLeft">
                <label class="layui-form-label leftLable01">标题搜索：</label>
                <div class="layui-input-block rightbox02">
                    <?= \kartik\select2\Select2::widget([
                        'name' => 'title',
                        'options' => [
                            'class' => 'title-search',
                            'multiple' => false,
                            'lay-ignore' => true,
                        ],
                        'pluginOptions' => [
                            'id' =>  new JsExpression("function(rs) {
                                return rs.taskId;
                            }"),
                            'placeholder' => '请输入或选择用户',
                            'allowClear' => false,
                            'language' => [
                                'errorLoading' => new JsExpression("function () { return 'Waiting...'; }"),
                            ],
                            'ajax' => [
                                'url' => \yii\helpers\Url::to('search-ir-title'),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {keyword:params.term}; }')
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new JsExpression('function(res) { return res.text; }'),
                            'templateSelection' => new JsExpression('function (res) { return res.text; }'),

                        ]
                    ])?>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="12">
                <textarea name="<?= Html::getInputName($model, 'assess_advice') ?>" rows="7"
                          style="width: 100%;"><?= $model->assess_advice ?></textarea>
            </td>
        </tr>
        <tr>
            <td colspan="3" class="noborderRight">
                <label class="layui-form-label leftLable01">评估人签字</label>
                <div class="layui-input-block rightbox01">
                    <input type="text" disabled lay-verify="required" value="<?= $userInfo->chinese_name ?>"
                           autocomplete="off" class="layui-input">
                </div>
            </td>
            <td colspan="3" class="noborderLeft noborderRight">
                <label class="layui-form-label leftLable02">完成评估日期：</label>
                <div class="layui-input-block rightbox02">
                    <div class="layui-inline">
                        <input type="text" disabled class="layui-input" value="<?= date('Y-m-d') ?>" utocomplete="off"
                               id="assessmentTime">
                    </div>
                </div>
            </td>
            <td colspan="3" class="noborderLeft" width="300px">
                <label class="layui-form-label leftLable02"> 主负责人：</label>
                <div class="layui-input-block rightbox02">
                    <?php
                        $head_users = $model->getRelevanceUsers(IrUser::HEAD_TYPE);
                        $master_head_users = [
                            'id' => false,
                            'text' => false
                        ];
                        $head_user = [
                            'id' => [],
                            'text' => []
                        ];
                        foreach ($head_users as $value){
                            if($value['master'] == IrUser::MASTER_YES){

                                $master_head_users['id']   = $value['user_id'];
                                $master_head_users['text'] = $value['chinese_name'] .'--'. ArrayHelper::getValue(UserInfo::$shiftArr, $value['area'], '');
                            }else{

                                $head_user['id'][]   = $value['user_id'];
                                $head_user['text'][] = $value['chinese_name'];
                            }
                        }

                        echo \backend\widgets\SearchUser::widget([
                            'name' => Html::getInputName($model, 'master_head_users'),
                            'value' => $master_head_users['id'],
                            'initValueText' => $master_head_users['text'],
                            'url' => \yii\helpers\Url::to('/user-info/search-by-name'),
                            'options' => [
                                'multiple' => false
                            ],
                        ]);
                    ?>
                </div>
            </td>
            <td colspan="3" class="noborderLeft" width="600px">
                <label class="layui-form-label leftLable02"> 次负责人：</label>
                <div class="layui-input-block rightbox02">
                    <?= \backend\widgets\SearchUser::widget([
                        'name' => Html::getInputName($model, 'head_users'),
                        'url' => \yii\helpers\Url::to('/user-info/search-by-name'),
                        'value' => $head_user['id'],
                        'initValueText' => $head_user['text'],
                        'options' => [
                            'multiple' => true
                        ],
                        'pluginOptions' => [
                            'closeOnSelect' => false,
                        ]
                    ])?>
                </div>
            </td>
        </tr>
    </table>
    <div class="layui-form-item" style="margin-top: 50px;">
        <label for="" class="layui-form-label">
        </label>
        <?= Html::button('保存', ['class' => 'layui-btn submit']) ?>
    </div>
<?php
    LayuiActiveForm::end();
}else{
?>
<form>
    <table class="layui-table" width="100%">
        <tr style="width: 100%;">
            <th rowspan="3" class="leftTitle">评<br/>估<br/>人<br/>建<br/>议</th>
            <td colspan="6" class="noborderRight">
                <label class="layui-form-label leftLable02">建议是否接受：</label>
                <div class="layui-input-block rightbox02">
                    <select disabled lay-verify="required">
                        <option value="">请选择</option>
                        <?php foreach ($model::$assessStatusArr as $key => $value): ?>
                            <option value="<?= $key ?>" <?= $model->assess_status === $key ? 'selected' : '' ?>><?= $value ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </td>
            <td colspan="6" class="noborderLeft">
                <label class="layui-form-label leftLable01">标题搜索：</label>
                <div class="layui-input-block rightbox02">
                    <select disabled xm-select="searchInput" name="" xm-select-radio=""
                            xm-select-search="<?= \yii\helpers\Url::to('search-ir-title') ?>"></select>
                 </div>
            </td>
        </tr>
        <tr>
            <td colspan="12">
                <textarea disabled rows="7" style="width: 100%;"><?= $model->assess_advice ?></textarea>
            </td>
        </tr>
        <tr>
            <td colspan="3" class="noborderRight">
                <label class="layui-form-label leftLable01">评估人签字</label>
                <div class="layui-input-block rightbox01">
                    <input type="text" disabled lay-verify="required" value="<?= (new \backend\models\UserInfo())->getUserName($model->assess_user) ?>"
                           autocomplete="off" class="layui-input">
                </div>
            </td>
            <td colspan="3" class="noborderLeft noborderRight">
                <label class="layui-form-label leftLable02">完成评估日期：</label>
                <div class="layui-input-block rightbox02">
                    <div class="layui-inline">
                        <input type="text" disabled class="layui-input" value="<?= date('Y-m-d') ?>" utocomplete="off"
                               id="assessmentTime">
                    </div>
                </div>
            </td>
            <td colspan="3" class="noborderLeft" width="300px">
                <label class="layui-form-label leftLable02"> 主负责人：</label>
                <div class="layui-input-block rightbox02">
                    <?php
                    $head_users = $model->getRelevanceUsers(IrUser::HEAD_TYPE);
                    $master_head_users = [
                        'id' => false,
                        'text' => false
                    ];
                    $head_user = [
                        'id' => [],
                        'text' => []
                    ];
                    foreach ($head_users as $value){
                        if($value['master'] == IrUser::MASTER_YES){

                            $master_head_users['id']   = $value['user_id'];
                            $master_head_users['text'] = $value['chinese_name'];
                        }else{

                            $head_user['id'][]   = $value['user_id'];
                            $head_user['text'][] = $value['chinese_name'];
                        }
                    }

                    echo \backend\widgets\SearchUser::widget([
                        'name' => Html::getInputName($model, 'master_head_users'),
                        'value' => $master_head_users['id'],
                        'initValueText' => $master_head_users['text'],
                        'url' => \yii\helpers\Url::to('/user-info/search-by-name'),
                        'options' => [
                            'multiple' => false,
                            'disabled' => true
                        ],
                    ]);
                    ?>
                </div>
            </td>
            <td colspan="3" class="noborderLeft" width="500px">
                <label class="layui-form-label leftLable02"> 次负责人：</label>
                <div class="layui-input-block rightbox02">
                    <?= \backend\widgets\SearchUser::widget([
                        'name' => Html::getInputName($model, 'head_users'),
                        'url' => \yii\helpers\Url::to('/user-info/search-by-name'),
                        'value' => $head_user['id'],
                        'initValueText' => $head_user['text'],
                        'options' => [
                            'multiple' => true,
                            'disabled' => true
                        ],
                        'pluginOptions' => [
                            'closeOnSelect' => false,
                        ]
                    ])?>
                </div>
            </td>
        </tr>
    </table>
</form>
<?php
  
}
?>

<?php
$js = <<<JS
    $(function() { 
        $('.appeal-form').show(); 
    });
    $(function() { 
        $('.title-search').on("change",function(){
            let data = $(this).val(); 
            window.open('view?id='+data); 
        });   
    });
     $('.submit').click(function() { 
        layer.load(3); 
        var form = $(this).parents('form').serialize();
      
        $.ajax({
            type:'post',
            dataType:'json',
            data:form,
            success:function(res) {
                layer.closeAll();
                if(res.status == 200){
                    window.location.href='index';
                }else{
                    layer.msg(res.msg);
                }
            }
        });
    });
JS;

$this->registerJs($js);

?>
