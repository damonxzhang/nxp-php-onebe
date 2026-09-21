<?php
use yii\helpers\Url;
use backend\models\UserInfo;
use yii\helpers\ArrayHelper;
?>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">

        <?= \backend\widgets\SelectSearch::widget([
            'name' => 'user',
            'text' => '用户',
            'columnName' => 'user_id',
            'value' => $userIdStr,
            'required' => true,
            'selectWidth' => '600px',
            'options' => [
                'multiple' => true,
            ],
            'selectList' => [
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
                <span class="x-red">*</span>选择权限:
            </label>
            <div class="layui-input-inline" style="width:600px">
                <select name="gemba_role" >

                    <?php foreach (\backend\models\Gemba::$showGeambaRole as $key => $value): ?>
                        <option value="<?= $key ?>" ><?= $value ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                <span class="x-red">*</span>选择小组:
            </label>
            <div class="layui-input-inline" style="width:600px">
                <select name="team" id="team"  xm-select='team'  xm-select-search='' xm-select-radio="">

                    <?php foreach ($gembaTeam as $key => $value): ?>
                        <option value="<?= $key ?>" ><?= $value ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="layui-form-item"  style="margin-top: 50px;">
            <label for="" class="layui-form-label">
            </label>
            <button class="layui-btn" lay-filter="add" lay-submit="">
                保存
            </button>
        </div>

    </form>
</div>
<?php $this->beginBlock('footer') ?>
<script>

    layui.use(['form','layer'], function(){
        $ = layui.jquery;
        var form = layui.form ,layer = layui.layer;
        //监听提交
        form.on('submit(add)', function(data){
            layer.load(3);
            $.post('<?= Url::to(['create']) ?>', data.field, function (res) {
                layer.closeAll();
                if (res.status === 200) {
                    layer.msg(res.msg, {icon: 1,time: 1500}, function () {
                        var index = parent.layer.getFrameIndex(window.name);
                        parent.layer.close(index);
                        parent.location.reload();
                    })
                } else {
                    layer.msg(res.msg, {icon: 2, time: 1500})
                }
            }, 'json');
            return false;
        });
    });
</script>
<?php $this->endBlock() ?>
