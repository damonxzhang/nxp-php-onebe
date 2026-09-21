<?php
use backend\models\Ir;
use yii\helpers\Html;
use backend\models\IrUser;
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
                    <select xm-select="searchInput" name="" xm-select-radio=""
                            xm-select-search="<?= \yii\helpers\Url::to('search-ir-title') ?>"></select>
                    <!--                    <a href="javascript:void()" class="layui-btn searchBtn">跳转查看</a>-->
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
            <td colspan="3" class="noborderLeft">
                <label class="layui-form-label leftLable02"> 主负责人：</label>
                <div class="layui-input-block rightbox02">
                    <select name="<?= Html::getInputName($model, 'master_head_users') ?>" xm-select="select-principal"
                            xm-select-radio="" xm-select-search='/user-info/search-by-name'>
                        <?php
                        $head_users = $model->getRelevanceUsers(IrUser::HEAD_TYPE);

                        if (!empty($head_users)) {
                            foreach ($head_users as $key => $val) {
                                if ($val['master'] == IrUser::MASTER_YES) {
                                    echo '<option value="' . $val['user_id'] . '" selected>' . $val['chinese_name'] . '</option>';
                                    break;
                                }
                            }
                        }
                        ?>
                    </select>
                </div>
            </td>
            <td colspan="3" class="noborderLeft">
                <label class="layui-form-label leftLable02"> 次负责人：</label>
                <div class="layui-input-block rightbox02">
                    <select name="<?= Html::getInputName($model, 'head_users') ?>" xm-select="select-principals"
                            xm-select-search='/user-info/search-by-name'>
                        <?php
                        if (!empty($head_users)) {
                            foreach ($head_users as $key => $val) {
                                if ($val['master'] == IrUser::MASTER_NO) {
                                    echo '<option value="' . $val['user_id'] . '" selected>' . $val['chinese_name'] . '</option>';
                                }
                            }
                        }
                        ?>
                    </select>
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
            <td colspan="3" class="noborderLeft">
                <label class="layui-form-label leftLable02"> 主负责人：</label>
                <div class="layui-input-block rightbox02">
                    <select disabled xm-select="select-principal"
                            xm-select-radio="" xm-select-search='/user-info/search-by-name'>
                        <?php
                        $head_users = $model->getRelevanceUsers(IrUser::HEAD_TYPE);

                        if (!empty($head_users)) {
                            foreach ($head_users as $key => $val) {
                                if ($val['master'] == IrUser::MASTER_YES) {
                                    echo '<option value="' . $val['user_id'] . '" selected>' . $val['chinese_name'] . '</option>';
                                    break;
                                }
                            }
                        }
                        ?>
                    </select>
                </div>
            </td>
            <td colspan="3" class="noborderLeft">
                <label class="layui-form-label leftLable02"> 次负责人：</label>
                <div class="layui-input-block rightbox02">
                    <select disabled xm-select="select-principals" xm-select-search='/user-info/search-by-name'>
                        <?php
                        if (!empty($head_users)) {
                            foreach ($head_users as $key => $val) {
                                if ($val['master'] == IrUser::MASTER_NO) {
                                    echo '<option value="' . $val['user_id'] . '" selected>' . $val['chinese_name'] . '</option>';
                                }
                            }
                        }
                        ?>
                    </select>
                </div>
            </td>
        </tr>
    </table>
</form>
<?php
    echo $this->render('_appeal_form',['model' => $model,'userInfo' => $userInfo,'type' => \backend\models\Appeal::IR_ASSESS_TYPE]);

}
?>

<?php
$js = <<<JS
    $(function() {
        let formSelects = layui.formSelects;
        formSelects.on('searchInput', function(id, vals, val, isAdd, isDisabled){
            window.open('view?id='+val.value); 
            formSelects.render('searchInput');

        });
        $('.appeal-form').show();
        // formSelects.render('#searchInput')
    })
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
