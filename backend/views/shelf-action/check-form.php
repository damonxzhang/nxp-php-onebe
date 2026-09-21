<?php
use yii\widgets\ActiveForm;
use yii\helpers\Url;
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">日志管理</a>
        <a href="javascript:;">
          <cite>操作日志</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <?php if($step == 1){ ?>
        <div class="layui-row">

            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'file')->fileInput(); ?>

            <button class="layui-btn">提交</button>
            <?php ActiveForm::end(); ?>
        </div>
    <?php } else { ?>
        <div class="layui-row">
            <div>
                <form class="layui-form" action="">
                    <table class="layui-table">
                        <thead>
                        <tr>
                            <th>批号</th>
                            <th>需求数量</th>
                            <th>变更后数量</th>
                        </thead>
                        <tbody>
                        <?php foreach ($data as $cont => $row): ?>
                            <tr>
                                <td><?= $cont ?><input type="hidden" name="cnt[<?php echo $cont; ?>]" value="<?php echo $row['y']?>" ></td>
                                <td><?= $row['y'] ?></td>
                                <td><input type="text" name="new_cnt[<?php echo $cont; ?>]"></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit lay-filter="cum_form">提交</button>
                            <button type="reset" class="layui-btn layui-btn-primary cancel">取消</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    <?php } ?>
</div>

<?php
$sUrl = Url::to('synchronization');
$export_url = Url::to([$this->context->id . '/save-check-num']) ;
$keys = ['start_time','end_time'];
$show_time = [];

$js =<<<JS

    layui.use(['jquery', 'layer', 'form'], function() {
        var $ = layui.jquery; 
        var upload = layui.upload; 
        var form = layui.form;
        console.log(321)
        //执行实例
        var uploadInst = upload.render({
            elem: '#test1', //绑定元素
            exts: 'xlsx',
            auto: false
            ,done: function(res){
              console.log(res)
            }
        });
        form.on('submit(cum_form)', function(data){
            $.ajax({
                type: 'POST',
                data: data.field,
                dataType: 'json',
                url: "{$export_url}",
                success: function(res) {
                    if (res.status == true){
                        var index = parent.layer.getFrameIndex(window.name);
                        parent.layer.close(index);
                        parent.location.reload();
                    } else {
                        layer.msg(res.msg, {icon: 2, time: 3000})
                    }
                }
            });
            return false
        });
         $(document).on('click','.cancel',function(){
            var index = parent.layer.getFrameIndex(window.name);
            parent.layer.close(index);
        });
    });
    

JS;
$this->registerJs($js,3);
?>

