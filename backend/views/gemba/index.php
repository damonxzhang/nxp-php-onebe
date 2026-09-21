<?php

use backend\models\UserInfo;
use yii\helpers\Url;
use backend\models\Gemba;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use backend\widgets\SearchUser;
?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">NXP WAY</a>
        <a href="javascript:;">
          <cite>Gemba记录</cite>
        </a>
      </span>
</div>


<div class="x-body">
    <div class="layui-row">
        <!--小组、区域、检查时间（开始~结束）、状态（完成、未完成）、类型、是否有标准要求、完成人-->
        <form  class="layui-form layui-col-md12 x-so" id="search-form">
            <input type="hidden" name="name" value="<?=!empty($request['name'])?$request['name'] :'' ?>">
            <div class="layui-input-inline" style="width: 150px">
                <?= SearchUser::widget([
                    'name' => 'completeUserId',
                    'url' => '/user-info/search-by-name',
                    'value' => !empty($request['completeUserId'])?$request['completeUserId']:''
                ])?>
            </div>
            <div class="layui-input-inline">
                <?= Html::dropDownList('isComplete', ArrayHelper::getValue($request, 'isComplete'), ArrayHelper::merge(['' => '状态选择'], Gemba::$status), ['name' => 'isComplete' ]); ?>
            </div>
            <div class="layui-input-inline">
                <?= Html::dropDownList('type', ArrayHelper::getValue($request, 'type'), ArrayHelper::merge(['' => '类型选择'], Gemba::$type), ['name' => 'type']); ?>
            </div>
            <div class="layui-input-inline">
                <?= Html::dropDownList('isStandard', ArrayHelper::getValue($request, 'isStandard'), ['' => '是否有标准',1 => '是',0 => '否'], ['name' => 'isStandard']); ?>
            </div>
            <div class="layui-input-inline">
                <?= Html::dropDownList('section', ArrayHelper::getValue($request, 'section'), ArrayHelper::merge(['' => '区域选择'], UserInfo::$sectionArr), ['name' => 'section']); ?>
            </div>
            <input  class="layui-input"  placeholder="开始日" name="start_time" id="start_time" value="<?= !empty($request['start_time']) ? date('Y-m-d H:i:s',strtotime($request['start_time'])) : '' ?>" autocomplete="off">
            <input  class="layui-input" placeholder="截止日" name="end_time" id="end_time" value="<?= !empty($request['end_time']) ? date('Y-m-d H:i:s',strtotime($request['end_time'])) : '' ?>" autocomplete="off">

            <button class="layui-btn"  lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i>查询</button>
            <button class="layui-btn" type="button" onclick="location.href= '<?= Url::to([$this->context->id . '/index'])?>'" >
                <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新
            </button>
        </form>
    </div>

    <xblock>
<!--        <button class="layui-btn" onclick="x_admin_show('新增',' echo  Url::to([$this->context->id . '/create']) ')"><i class="layui-icon"></i>新增</button>-->
        <button class="layui-btn layui-bg-black" onclick="export_excel()"><i class="layui-icon">&#xe62f;</i>导出</button>
        <?php if($userInfo->gemba_role === Gemba::ADMIN_ROLE):?>
        <a class="layui-btn layui-btn-normal"  href="<?=  Url::to(['gemba-user/index']) ?>"> 用户列表</a>
        <a class="layui-btn layui-btn-warm"  href="<?=  Url::to(['team/index']) ?>"> 小组列表</a>
        <?php endif;?>
        <span class="x-right" style="line-height:30px">共有数据：<span class="count_num"><?= $pagination->totalCount ?></span> 条 ( <?= $pagination->getPageCount() ?> 页 )</span>
    </xblock>
    <table class="layui-table">
        <thead>
        <tr>
            <th>
                <div class="layui-unselect header layui-form-checkbox" lay-skin="primary"><i class="layui-icon">&#xe605;</i></div>
            </th>
            <th>检查日期</th>
            <th>参与人员</th>
            <th>位置</th>
            <th>类型</th>
            <th>问题描述</th>
            <th>图片</th>
            <th>操作</th>
        </thead>
        <tbody>
            <?php foreach ($gembaDatas as $value):?>
                <tr  >
                    <td>
                        <div class="layui-unselect layui-form-checkbox" data-id="<?=$value['id']?>" lay-skin="primary"><i class="layui-icon">&#xe605;</i></div>
                    </td>
                    <td> <?=date('Y-m-d',$value['spectionDate'])?> </td>
                    <td> <?=$value->getUserName('participateUserId')?> </td>
                    <td> <?=$value['location']?> </td>
                    <td>  <?=Gemba::$type[$value['type']]?> </td>

                    <td>  <?=$value['problem']?> </td>
                    <td> <i class="layui-icon <?= $value['problemImg'] ? 'layui-icon-ok-circle' :'layui-icon-close-fill' ?>"></i>  </td>


                    <td class="td-manage">
                        <button type="button" class="layui-btn layui-btn-xs" onclick="x_admin_show('编辑','<?= Url::to([$this->context->id . '/update', 'id' => $value['id']]) ?>')">编辑</button>
                    </td>
                </tr>
            <?php endforeach;?>

    </table>
    <?= $this->render('@app/views/layouts/pagination', compact('pagination')) ?>

</div>

<?php $this->beginBlock('footer') ?>
<?php
$url = Url::to([$this->context->id . '/export']) ;

$js =<<<JS
    function export_excel(){
        var fromData  = $('#search-form').serialize();
        $('.layui-form-checked').each(function () {
            if (typeof($(this).data('id')) !="undefined" && $(this).data('id')!=0){
                fromData+= '&idlist[]='+$(this).data('id')  ;
            }
        });

        layui.use(['jquery', 'layer'], function() {
            var $ = layui.jquery;
            $.ajax({
                type: 'GET',
                dataType: 'json',
                url: '{$url}?'+fromData,
                success: function(res) {
                    LAY_EXCEL.exportExcel(res.data, res.msg, 'xlsx');
                }
            });
        });
    }
JS;
$this->registerJs($js,3);

?>
<script>


</script>
<?php $this->endBlock() ?>
