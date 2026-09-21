<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">后台测试</a>
        <a href="javascript:;">
          <cite>文件上传</cite>
        </a>
      </span>
</div>
<div class="x-body layui-anim layui-anim-up">
    <table class="table text-center table-bordered">
        <tr>
            <td style="vertical-align: middle;padding: 20px">
                <?php ActiveForm::begin(['action' => 'ajax-upload', 'method' => 'post', 'options' => ['enctype' => 'multipart/form-data', 'id' => 'upload-form']]); ?>
                <?= Html::fileInput('file') ?>
                <?php ActiveForm::end() ?>
            </td>
        </tr>
        <tr>
            <td style="text-align: left;padding: 20px">
                <?= Html::button('导入数据', ['class' => 'btn btn-primary btn-sm', 'id' => 'upload-btn']) ?>
                <p style="margin-top: 20px">
                    <b>操作结果：</b>
                </p>
                <div id="upload-result"
                     style="height:220px;overflow:auto;overflow-x:hidden;border:1px solid #ccc;padding:5px"></div>
            </td>
        </tr>
    </table>
    <?php $form = ActiveForm::begin([
        'method' => 'post',
    ]); ?>
    <input type="hidden" name="errorData" id="error-data">
    <?php ActiveForm::end(); ?>
</div>
<?php $this->beginBlock('footer') ?>
<script>
    $(function () {

        // 导入数据
        $('#upload-btn').click(function () {

            var btn = $('#upload-btn');
            var show = $('#upload-result');
            $.post({
                url: '<?= Url::to(['test/import']) ?>',
                    cache: false,
                    dataType: 'json',
                    data: new FormData($('#upload-form')[0]),
                    processData: false,
                    contentType: false,
                    beforeSend: function () {
                        btn.html('<i class="fa fa-refresh fa-spin"></i> 导入数据中，请勿关闭页面');
                        btn.attr('disabled', true);
                        show.html('');
                    },
                    success: function (result) {
                        if (result.status == 100) {
                            show.css('color', 'red');
                            show.html(result.msg);
                        } else {
                            // 显示导入结果
                            show.css('color', 'red');

                            var tips = '<div style="margin-bottom:5px;font-weight: bold">' +
                                '<span style="color:green">导入成功:' + result.data.successCount + '</span>&nbsp;&nbsp;' +
                                '<span style="color:red">导入失败:' + result.data.errorCount + '</span></div>';
                            show.html(tips + result.data.errorList);
                        }
                        btn.html('导入数据');
                        btn.attr('disabled', false);
                    }
                });
            });
        });
    </script>
    <?php $this->endBlock() ?>

