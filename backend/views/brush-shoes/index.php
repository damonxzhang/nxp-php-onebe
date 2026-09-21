<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use backend\models\UserInfo;
use backend\models\Blog;
use yii\helpers\Html;
?>

<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">洗鞋机数据</a>
        <a href="javascript:;">
          <cite>刷卡记录</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
            <input class="layui-input" id="autofocus" value="<?php echo ArrayHelper::getValue($request, 'chinese_name') ?>" autocomplete="off" placeholder="姓名" name="chinese_name">
            <input class="layui-input" id="autofocus" value="<?php echo ArrayHelper::getValue($request, 'meal_card_code') ?>" autocomplete="off" placeholder="卡号" name="meal_card_code">
            <div class="layui-input-inline">
                <?= Html::dropDownList('area', ArrayHelper::getValue($request, 'area'), ArrayHelper::merge(['' => '区域选择'], UserInfo::$areaArr), ['name' => 'area']); ?>
            </div>
            <div class="layui-input-inline">
                <?= Html::dropDownList('shift', ArrayHelper::getValue($request, 'shift'), ArrayHelper::merge(['' => '班别选择'], UserInfo::$shiftArr), ['name' => 'shift']); ?>
            </div>
            <input class="layui-input" placeholder="开始日" name="start_time" id="start_time" value="<?= !empty($request['start_time']) ? $request['start_time'] : '' ?>" autocomplete="off">
            <input class="layui-input" placeholder="截止日" name="end_time" id="end_time" value="<?= !empty($request['end_time']) ? $request['end_time'] : '' ?>" autocomplete="off">
            <button class="layui-btn"  lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i>查询</button>
            <button class="layui-btn" type="button" onclick="location.href= '<?= Url::to([$this->context->id . '/index'])?>'" >
                <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新
            </button>
        </form>
    </div>
    <xblock>
        <?= Html::a('<i class="layui-icon">&#xe62f;</i>导出', 'javascript:;', ['class' => 'layui-btn layui-bg-black export-excel']) ?>

        <span class="x-right" style="line-height:30px">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <table class="layui-table">
        <thead>
        <tr>
            <th>
                <div class="layui-unselect header layui-form-checkbox" lay-skin="primary"><i class="layui-icon">&#xe605;</i></div>
            </th>
            <th>ID</th>
            <th>扫卡人姓名</th>
            <th>刷卡卡号</th>
            <th>区域</th>
            <th>班别</th>
            <th>扫卡时间</th>
        </thead>
        <tbody>
        <?php foreach ($dataProvider as $value): ?>
            <tr>
                <td>
                    <div class="layui-unselect layui-form-checkbox" lay-skin="primary" data-id=<?= $value['id'] ?>><i class="layui-icon">&#xe605;</i></div>
                </td>
                <td><?= $value['id'] ?></td>
                <td><?= $value['chinese_name'] ?></td>
                <td><?= $value['meal_card_code'] ?></td>
                <td><?= ArrayHelper::getValue(UserInfo::$areaArr, $value['area'],'') ?></td>
                <td><?= ArrayHelper::getValue(UserInfo::$shiftArr, $value['shift'],'') ?></td>
                <td><?= $value['brush_date'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php if (empty($dataProvider)): ?>
        <p class="text-empty">-- 暂无数据 --</p>
    <?php endif; ?>
    <?= $this->render('@app/views/layouts/pagination', compact('pagination')) ?>
</div>

<?php $this->beginBlock('footer') ?>


<?php

$exportUrl = \yii\helpers\Url::to(array_merge([$this->context->id . '/export'],Yii::$app->request->queryParams));
$js = <<<JS
    $('.export-excel').click(function(){
        var idlist   = [];
        $('.layui-form-checked').each(function () {
            if (typeof($(this).data('id')) !="undefined" && $(this).data('id')!=0){
                idlist.push($(this).data('id'));
            }
        });
        console.log(idlist);
        layui.use(['jquery', 'layer'], function() {
            var $ = layui.jquery;
            $.ajax({
                type: 'GET',
                data: {idlist: idlist},
                dataType: 'json',
                url: '{$exportUrl}',
                success: function(res) {
                    LAY_EXCEL.exportExcel(res.data, res.msg, 'xlsx');
                }
            });
        });
    });

 
JS;
$this->registerJs($js); ?>


<?php $this->endBlock() ?>

