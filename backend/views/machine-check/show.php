<?php
use yii\helpers\Url;
use backend\models\MachineCheck;
?>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">
        <div class="layui-col-sm11">
            <input type="hidden" name="check_id" value="<?= $checkId ?>">
            <div class="layui-form-item">
                <label class="layui-form-label">
                    机器编号:
                </label>
                <div class="layui-input-inline">
                    <input type="text" value="<?= $number ?>" readonly="readonly" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-upload-list">
                <table class="layui-table" style="margin-left: 110px;">
                    <thead>
                    <tr>
                        <th>序号</th>
                        <th>位置编号</th>
                        <th>位置图</th>
                        <th>标准</th>
                        <th>得分</th>
                        <th>检查图片</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($optionArr)): ?>
                        <?php foreach ($optionArr as $key => $value): ?>
                            <tr>
                                <td style="width: 2%"><?= $key+1 ?></td>
                                <td style="width: 5%"><?= $value['position'] ?></td>
                                <td style="width:20%;height:100%;border: 1px #c0c0c0 solid;margin:0 ;padding:0 ;">
                                    <?php if (!empty($value['file_path'])): ?>
                                        <?php foreach ($value['file_path'] as $path): ?>
                                            <img src="<?= $baseUrl.$path?>" class="ez" alt="" style="margin:0;padding:0;float:left;">
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </td>
                                <td style="width: 20%"><?=$value['standard'] ?></td>
                                <td style="width: 5%">
                                      <?= $value['score'] ?>分
                                </td>
                                <td style="width:20%;height:100%;border: 1px #c0c0c0 solid;margin:0 ;padding:0 ;">
                                    <?php if (!empty($value['check_file_path'])): ?>
                                        <?php foreach ($value['check_file_path'] as $path): ?>
                                            <img src="<?= $baseUrl.$path?>" class="ez" alt="" style="margin:0;padding:0;float:left;">
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>
<?php $this->beginBlock('footer') ?>
<?php $this->endBlock() ?>
