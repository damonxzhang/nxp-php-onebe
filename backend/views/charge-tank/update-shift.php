<?php

use yii\helpers\Url;
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">后台测试</a>
        <a href="javascript:;">
          <cite>充电柜当班同步</cite>
        </a>
      </span>
</div>
<div class="x-body layui-anim layui-anim-up">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
            <div class="layui-input-inline">
                <select name="tank_id" lay-verify="required">
                    <option value="">请选择充电柜编号</option>
                    <?php foreach ($tanks as $val):?>
                        <option value="<?=$val['id']?>"><?=$val['number']?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <div class="layui-input-inline">
                <select name="user_id" lay-verify="required">
                    <option value="">请选择带班</option>
                    <?php foreach ($users as $val):?>
                        <option value="<?=$val['id']?>"><?=$val['chinese_name']?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <button type="button" class="layui-btn submit"  > 同步 </button>

        </form>
    </div>
</div>

<?php
$js = <<<JS
    $('.submit').click(function() {
        let form = $('form').serialize(); 
        $.ajax({
            data:form,
            dataType:'json',
            type:'post',
            success:function(res) {
                if(res.status == 200){
                    
                    window.location.reload();
                }else{
                    layer.msg(res.msg);
                }
            }
        });
    });
JS;
$this->registerJs($js);
?>