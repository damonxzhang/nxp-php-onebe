<?php
use yii\helpers\Html;
use backend\models\Appeal;
use backend\models\UserInfo;
use backend\widgets\LayuiActiveForm;
?>
<!--申诉-->
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

<?php


$js = <<<JS
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
