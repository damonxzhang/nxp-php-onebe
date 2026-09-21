<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Url;

?>
<div class="x-body layui-anim layui-anim-up">

    <form class="layui-form">
        <input type="hidden" name="id" value="<?= $id ?>">
        <table class="layui-table">
            <thead>
            <tr>
                <th>ID</th>
                <th>物料编号</th>
                <th>货架编号</th>
                <th>醒料截止时间</th>
                <th>过期时间</th>
                <th>扫描物料编号</th>
            </thead>
            <tbody>
            <?php if (!empty($dataProvider)): ?>
                <?php foreach ($dataProvider as $key => $value): ?>
                    <tr>
                        <td><?= $value['id'] ?></td>
                        <td><?= $value['cont_no'] ?></td>
                        <td><?= ArrayHelper::getValue($value->shelf, 'shelf_number', '') ?></td>
                        <td><?= date('Y-m-d H:i:s', strtotime($value['end_time'])) ?></td>
                        <td><?= date('Y-m-d H:i:s', strtotime($value['expire_time'])) ?></td>
                        <td>
                            <input type="text" id="cont_no<?= $key ?>" onkeypress="if(event.keyCode==13) nextInputFocus(this);" name="cont_no[]" placeholder="物料编号" data-contno ="<?= $value['cont_no'] ?>"  autocomplete="off" class="layui-input">
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr style="color: #000000">
                    <td colspan="6" style="text-align:center;">
                        <button class="layui-btn" lay-filter="compard" lay-submit="" id="submitform" onclick="subform()" type="button">确定</button>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </form>
    <div style="font-size:80px; text-align: center;margin-top: 5rem;"><?=$_GET['name']?>,请进行二次确认！</div>

</div>

<?php $this->beginBlock('footer') ?>
<script>

    //页面加载完成设置input获取焦点
    $(document).ready(function () {
        setTimeout(function () {
            $('#cont_no0').focus();
        }, 50);
    });

    //提交表单
    layui.use(['form', 'layer'], function () {
        $ = layui.jquery;
        var form = layui.form, layer = layui.layer;
        //监听提交
        form.on('submit(compard)', function (data) {
            var constlist   = [];
            $(".layui-input").each(function () {
                constlist.push($(this).data('contno'));
            });
            layer.load(3);
            data.field.const_list=constlist;
            $.post('<?= Url::to(['shelf-apply/promote']) ?>', data.field, function (res) {
                layer.closeAll();
                if (res.status === 200) {
                    layer.msg(res.msg, {icon: 1, time: 3000}, function () {
                        window.close();
                        // parent.$('.layui-icon-refresh').click();//刷新父页面
                    });
                } else {
                    layer.msg(res.msg, {icon: 2, time: 3000})
                }
            }, 'json');
            return false;
        });
    });

    //输入框焦点自动跳转到下一个
    function nextInputFocus(thisInput){
        var inputs = document.getElementsByTagName("input");
        for(var i = 0;i<inputs.length;i++){
            // 如果是最后一个，则焦点回到第一个
            if(i==(inputs.length-1)){
                subform();
                break;
            }else if(thisInput == inputs[i]){
                inputs[i+1].focus();
                break;
            }
        }
    }

    //修改提交表单button属性
    function subform(){
        $("#submitform").attr("type","submit");
    }

</script>
<?php $this->endBlock() ?>

