<?php

use backend\models\MachineModel;
use backend\models\MachineNumber;
use yii\helpers\ArrayHelper;
use backend\models\MachineOption;
use backend\models\MachineProcess;
?>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">
        <div class="layui-row">
            <div class="layui-col-sm6">
                <div class="layui-form-item">
                    <label class="layui-form-label">
                        位置编号:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text"  value="<?= $model['position'] ?>" readonly="readonly" lay-verify="required" autocomplete="off" class="layui-input" style="width: 650px;">
                    </div>
                </div>
                <div class="layui-form-item layui-form-text">
                    <label for="desc" class="layui-form-label">
                        标准:
                    </label>
                    <div class="layui-input-block">
                        <textarea  lay-verify="required" readonly="readonly" class="layui-textarea" style="width: 650px;height: 200px;"> <?php echo $model['standard']; ?></textarea>
                    </div>
                </div>
                <?php if (!empty($optionFile)): ?>
                    <div class="layui-upload-list">
                        <table class="layui-table" style="margin-left: 110px;">
                            <thead>
                            <tr><th>检查项</th></tr>
                            </thead>
                            <tbody >
                            <?php foreach ($optionFile as $list): ?>
                                <tr>
                                    <td>
                                        <img src="<?= $baseUrl.$list['file_path']?>" class="ez" alt="" style=" max-width: 400px;max-height: 300px;vertical-align: middle;">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<?php $this->beginBlock('footer') ?>
<script>
</script>
<?php $this->endBlock() ?>

