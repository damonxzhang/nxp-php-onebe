<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use backend\models\UserInfo;
?>
<div class="x-body layui-anim layui-anim-up">
    <?php $form=ActiveForm::begin(['options' => ['class' => 'layui-form','id' => 'comment-form']])?>

        <div class="layui-form-item">
            <label for="nickname" class="layui-form-label">
                <span class="x-red">*</span>交接班别:
            </label>
            <div class="layui-input-block"  >
                <select name="<?= Html::getInputName($model,'shift')?>">
                    <option value="">请选择班别</option>
                    <?php foreach ( UserInfo::$shiftArr as $key => $value):?>
                        <option value="<?=$key?>" <?= $model->shift == $key ? 'selected':''?>><?=$value?></option>
                    <?php endforeach;?>
                </select>

            </div>
        </div>
        <div class="layui-form-item layui-form-text">
            <label for="nickname" class="layui-form-label">
                <span class="x-red">*</span>备注信息:
            </label>
            <div class="layui-input-block">
                <textarea name="<?=Html::getInputName($model,'comment')?>" placeholder="请输入日志标题"  autocomplete="off" lay-verify="required" class="layui-textarea" ><?=$model->comment?></textarea>
            </div>
        </div>
        <div class="layui-form-item"  style="margin-top: 50px;">
            <label for="" class="layui-form-label">
            </label>
            <?=Html::button('保存',['class' =>'layui-btn add-comment' ]);?>

        </div>
    <?php ActiveForm::end()?>
</div>
<?php $this->beginBlock('footer') ?>
<script>
 
    $('.add-comment').click(function(){
        var form = $('#comment-form').serialize();

        $.post('',form,function(res){
                if(res.status == 200){
                    var index = parent.layer.getFrameIndex(window.name);
                    parent.layer.close(index);
                    parent.location.reload();
                }else{
                    layui.msg(res.msg);
                }
        },'json');

        
    });
</script>

<?php $this->endBlock()?>
