<?php

use yii\helpers\ArrayHelper;
use backend\models\Blog;
use backend\models\UserInfo;


?>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">
        <div class="layui-form-item">
            <label for="nickname" class="layui-form-label">
                日志标题:
            </label>
            <div class="layui-input-inline" style="width: 800px;">
                <input type="text" value="<?= $model['title'] ?>" readonly="readonly" autocomplete="off" lay-verify="required" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label for="tel" class="layui-form-label">
                发布人:
            </label>
            <div class="layui-input-inline" style="width: 800px;">
                <input type="text" value="<?php echo (new UserInfo())->getUserName($model['user_id']) ?>"  autocomplete="off" class="layui-input" readonly="readonly">
            </div>
        </div>
        <div class="layui-form-item">
            <label for="tel" class="layui-form-label">
                日志状态:
            </label>
            <div class="layui-input-inline" style="width: 800px;">
                <input type="text" value="<?= ArrayHelper::getValue(Blog::$statusArr, $model['status'],'') ?>"  autocomplete="off" class="layui-input" readonly="readonly">
            </div>
        </div>
        <div class="layui-form-item">
            <label for="tel" class="layui-form-label">
                创建时间:
            </label>
            <div class="layui-input-inline" style="width: 800px;">
                <input type="text" value="<?= date('Y-m-d H:i:s',strtotime($model['create_time'])) ?>"  autocomplete="off" class="layui-input" readonly="readonly">
            </div>
        </div>
        <div class="layui-form-item layui-form-text">
            <label for="desc" class="layui-form-label">
                日志内容:
            </label>
            <div class="layui-input-block">
                <textarea  lay-verify="required" readonly="readonly" class="layui-textarea" style="width: 800px;height: 300px;"> <?php echo $model['content_data']; ?></textarea>
            </div>
        </div>
        <?php if (!empty($model['image'])): ?>
            <div class="layui-form-item">
                <label class="layui-form-label">
                    日志图片:
                </label>
                <div class="layui-input-block">
                    <blockquote class="layui-elem-quote layui-quote-nm"  style="width: 768px;margin-top: 10px;">
                        <div class="layui-upload-list uploader-list"  style="overflow: auto;" id="uploader-list">
                            <?php foreach (explode(';',$model['image']) as $value): ?>
                                <div class="file-iteme">
                                    <img style="width: 100px;height: 100px;"  class="ez" alt=""  src= "<?= $baseurl.$value ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </blockquote>
                </div>
            </div>
        <?php endif; ?>
        <?php if (!empty($detailModel)): ?>
            <?php foreach ($detailModel as $value): ?>
                <hr class="layui-bg-red">
                <table class="layui-table" lay-size="sm"  style="width: 800px;margin-left: 110px;margin-top: 30px;">
                    <tbody>
                    <tr style="color: #000000">
                        <td >反馈人</td>
                        <td><?php echo (new UserInfo())->getUserName($value['user_id']) ?></td>
                        <td>反馈时间</td>
                        <td><?= $value['create_time'] ?></td>
                        <td>反馈状态</td>
                        <td><?= ArrayHelper::getValue(Blog::$statusArr, $value['status'],'') ?></td>
                    </tr>
                    <tr style="color: #000000">
                        <td colspan="3" style="text-align:center;">反馈交接类型</td>
                        <td colspan="3" style="text-align:center;"><?= ArrayHelper::getValue($type, $value['type'],'') ?></td>
                    </tr>
                    <tr style="color: #000000">
                        <td colspan="6" style="text-align:center;">反馈内容</td>
                    </tr>
                    <tr style="color: #000000">
                        <td colspan="6">
                            <textarea  lay-verify="required" readonly="readonly" class="layui-textarea" style="width: 800px;height: 300px;"> <?php echo $value['reply']; ?></textarea>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <?php if (!empty($value['image'])): ?>
                    <div class="layui-form-item">
                        <label class="layui-form-label"></label>
                        反馈图片
                        <div class="layui-input-block">
                            <blockquote class="layui-elem-quote layui-quote-nm"  style="width: 768px;margin-top: 10px;">
                                <div class="layui-upload-list uploader-list"  style="overflow: auto;" id="uploader-list">
                                    <?php foreach (explode(';',$value['image']) as $url): ?>
                                        <div class="file-iteme">
                                            <img style="width: 100px;height: 100px;"  class="ez" alt=""  src= "<?= $baseurl.$url ?>">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </blockquote>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </form>
</div>
<?php $this->beginBlock('footer') ?>

<?php $this->endBlock() ?>

