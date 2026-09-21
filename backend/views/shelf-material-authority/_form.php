<?php

use yii\helpers\Html;
use backend\widgets\LayuiActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model backend\models\ShelfMaterialAuthority */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="x-body">
    <div class="maintain-area-head-form">

        <?php $form = LayuiActiveForm::begin(); ?>
        <div class="form-group field-maintainareahead-status">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label">负责人:</label>
                <div class="layui-input-inline" style="width: 300px;">  <?=\backend\widgets\SearchUser::widget([
                        'name' => Html::getInputName($model, 'user_id'),
                        'url' => \yii\helpers\Url::to('/user-info/search-by-name'),
                        'value' => $model->user_id,
                    ]); ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="tel" class="layui-form-label"></label>
            <button  class="layui-btn" lay-filter="add" lay-submit="">
                提交
            </button>
        </div>

        <?php LayuiActiveForm::end(); ?>

    </div>

</div>

<?php $this->beginBlock('footer') ?>
    <script>
        let id = '<?= $model->id ?>'
        let url = ''
        if (!id){
            url = '<?= Url::to(['shelf-material-authority/create']) ?>'
        } else{
            url = '<?= Url::to(['shelf-material-authority/update']) ?>'
        }
        layui.use(['form','layer'], function(){
            $ = layui.jquery;
            var form = layui.form ,layer = layui.layer;
            //监听提交
            form.on('submit(add)', function(data){
                layer.load(3);
                $.post(url, data.field, function (res) {
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