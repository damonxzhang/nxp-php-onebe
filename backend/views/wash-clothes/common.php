<?php
use yii\helpers\Url;
use backend\models\UserInfo;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;"> 静电服清洗</a>
        <a href="javascript:;">
          <cite>未送洗原因</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
            <div class="layui-input-inline">
                <?= Html::dropDownList('area', ArrayHelper::getValue($request, 'area'), ArrayHelper::merge(['' => '区域选择'], UserInfo::$areaArr), ['name' => 'area']); ?>
            </div>
            <input class="layui-input" placeholder="开始日" name="start_time" id="start_time" value="<?= !empty($request['start_time']) ? date('Y-m-d H:i:s',strtotime($request['start_time'])) : '' ?>" autocomplete="off">
            <input class="layui-input" placeholder="截止日" name="end_time" id="end_time" value="<?= !empty($request['end_time']) ? date('Y-m-d H:i:s',strtotime($request['end_time'])) : '' ?>" autocomplete="off">
            <button class="layui-btn"  lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i>查询</button>
            <button class="layui-btn" type="button" onclick="location.href= '<?= Url::to([$this->context->id . '/user'])?>'" >
                <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新
            </button>
        </form>
    </div>
    <xblock>
        <?php if ($userInfo->id == 1): ?>
            <button class="layui-btn layui-bg-black export_excel" data-query='<?=json_encode(Yii::$app->request->queryParams)?>'><i class="layui-icon">&#xe62f;</i>导出数据</button>
        <?php endif; ?>
    </xblock>
    <table class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>姓名</th>
            <th>PID</th>
            <th>班别</th>
            <th>区域</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php if (!empty($dataProvider)): ?>
            <?php foreach ($dataProvider as $key => $value): ?>
                <tr>
                    <td><?= $key+1 ?></td>
                    <td><?= $value['chinese_name'] ?></td>
                    <td><?= $value['pid'] ?></td>
                    <td><?= ArrayHelper::getValue(UserInfo::$shiftArr, $value['shift'],'') ?></td>
                    <td><?= ArrayHelper::getValue(UserInfo::$areaArr, $value['area'],'') ?></td>
                    <td class="td-manage">
                    <button class="layui-btn layui-btn-xs" onclick="x_admin_show('送洗原因','<?=  Url::to([$this->context->id . '/create-common', 'pid' => $value['pid']]) ?>',500,400)"><i class="layui-icon"></i>送洗原因</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php $this->beginBlock('footer') ?>
<script>
    $('.export_excel').click(function(){ 
        let query = $(this).data('query');
        query = JSON.stringify(query);
        if(query == "[]"){
            layer.msg('请选择查询条件,并点击查询按钮后再导出操作!');
        }else{
            layui.use(['jquery', 'layer'], function() {
                var $ = layui.jquery;
                $.ajax({
                    type: 'GET',
                    data: {query: query},
                    dataType: 'json',
                    url: '<?= Url::to([$this->context->id . '/exprot-common?'])?>',
                    success: function(res) {
                        LAY_EXCEL.exportExcel(res.data, res.msg, 'xlsx');
                    }
                });
            });
           // window.location.href='exprot-common?query='+query;
        }
    }); 
    
</script>
<?php $this->endBlock() ?>
