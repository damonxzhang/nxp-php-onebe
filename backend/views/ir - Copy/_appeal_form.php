<?php
use yii\helpers\Html;
use backend\models\Appeal;
use backend\models\UserInfo;
use backend\widgets\LayuiActiveForm;
?>
<!--申诉-->
<?php
if($model->status !== $model::STATUS_APPEAL){
$form = LayuiActiveForm::begin([
    'options' => ['class' => 'appeal-form','style' => 'display:none;'],
    'fieldConfig' => [
        'template' => ' {input} ',
    ],
]);
?>

<table class="layui-table">
    <tr>
        <th rowspan="3" class="leftTitle">申<br/>诉</th>
        <td colspan="12">
            <label class="layui-form-label leftLable">申诉：</label>
        </td>
    </tr>
    <tr>
        <td colspan="12">
                <textarea name="content" required lay-verify="required" placeholder="请输入"
                          class="layui-textarea"> </textarea>
        </td>
    </tr>
    <tr>
        <td colspan="12">
            <label class="layui-form-label leftLable01">申诉人：</label>
            <div class="layui-input-block rightbox01">
                <input type="hidden" name="type" value="<?=$type?>">
                <input type="hidden" name="user_id" value="<?=$userInfo->id?>">
                <input type="hidden" name="ir_id" value="<?=$model->id?>">
                <input type="text" disabled value="<?=$userInfo->chinese_name?>" required lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
            </div>
        </td>
    </tr>
</table>
<div class="layui-form-item"  style="margin-top: 50px;">
    <label for="" class="layui-form-label">
    </label>
    <?= Html::button('保存', ['class' => 'layui-btn submit-appeal']) ?>
    <?= in_array($type,\backend\modules\Appeal\models\IrAppeal::$die_appeal) ? Html::button('取消申诉', ['class' => 'layui-btn close layui-btn-normal']) :'' ; ?>
</div>
<?php LayuiActiveForm::end();?>
<?php }else{?>

    <table class="layui-table">
        <tr>
            <th rowspan="3" class="leftTitle">申<br/>诉</th>
            <td colspan="12">
                <label class="layui-form-label leftLable">申诉：</label>
            </td>
        </tr>
        <tr>
            <td colspan="12">
                <textarea name="content" required lay-verify="required" placeholder="请输入"
                          class="layui-textarea"><?=$model->appeal_model->content?></textarea>
            </td>
        </tr>
        <tr>
            <td colspan="12">
                <label class="layui-form-label leftLable01">申诉人：</label>
                <div class="layui-input-block rightbox01">
                    <input type="text" disabled value="<?=( new userInfo)->getUserName($model->appeal_model->appeal_user)?>" required lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
                </div>
            </td>
        </tr>
    </table>

    <!--申诉结果-->
    <?php $form = LayuiActiveForm::begin([
        'options' => ['class' => 'appeal-form' ],
        'fieldConfig' => [
            'template' => ' {input} ',
        ],
    ]);?>
        <table class="layui-table">
            <tr>
                <th rowspan="3" class="leftTitle">申<br/>诉<br/>结<br/>果</th>
                <td colspan="12">
                    <label class="layui-form-label leftLable01">申诉结果：</label>
                    <div class="layui-input-block rightbox01">
                        <select name="status" lay-verify="required">
                            <?php foreach ( Appeal::$selectList as $key => $val):?>
                                <option value="<?=$key?>"><?=$val?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="12">
                    <textarea name="appeal_message" required lay-verify="required" placeholder="请输入" class="layui-textarea"></textarea>
                </td>
            </tr>
            <tr>
                <td colspan="6" class="noborderRight">
                    <label class="layui-form-label leftLable02">委员会签字：</label>
                    <div class="layui-input-block rightbox02">
                        <input type="hidden" name="relation_id" value="<?=$model->id?>">
                        <input type="hidden" name="appeal_id" value="<?=$model->appeal_model->id?>">
                        <input type="text" name="complaintPerson" disabled value="<?=$userInfo->chinese_name?>" required lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
                    </div>
                </td>
                <td colspan="6" class="noborderLeft">
                    <label class="layui-form-label leftLable02">完成评估日期：</label>
                    <div class="layui-input-block rightbox02">
                        <input type="text" disabled value="<?=date('Y-m-d')?>" required lay-verify="required" placeholder=""
                               autocomplete="off" class="layui-input">
                    </div>
                </td>
            </tr>
        </table>
    <div class="layui-form-item"  style="margin-top: 50px;">
        <label for="" class="layui-form-label">
        </label>
        <?= Html::button('保存', ['class' => 'layui-btn submit-appeal-audit']) ?>
    </div>
    <?php LayuiActiveForm::end();?>

<?php
}

$js = <<<JS
    $(function() {
        let formSelects = layui.formSelects;
        formSelects.on('searchInput', function(id, vals, val, isAdd, isDisabled){
            // window.open('view?id='+val.value); 
            formSelects.render('searchInput');
        
        });
    }); 
    function submitForm(obj, url){
        let form = obj.parents('form').serialize();
        $.ajax({
            url:url,
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
    }

    //提交表单
    $('.submit-appeal').click(function() { 
        layer.load(3);
        submitForm($(this),'submit-appeal');
    }); 
    //提交表单
    $('.submit-appeal-audit').click(function() { 
        layer.load(3);
        submitForm($(this),'submit-appeal-audit'); 
    });
    //显示申诉
    $('.switch').click(function() {
        $('.appeal-form').show();
        $('.appeal-top-form').hide();
    });   
    //隐藏申诉
    $('.close').click(function() {
        $('.appeal-form').hide();
         $('.appeal-top-form').show();
    });
        
JS;

$this->registerJs($js);

?>
