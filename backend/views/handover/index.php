<?php

use backend\models\UserInfo;
use yii\helpers\Url;
use backend\models\Blog;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">日志管理</a>
        <a href="javascript:;">
          <cite>今日交接</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <div class="layui-row">
        <form class="layui-form layui-col-md12 x-so">
 
            <input class="layui-input" placeholder="开始日" name="start_time" id="startTime" autocomplete="off">
            <input class="layui-input" placeholder="截止日" name="end_time" id="endTime"  autocomplete="off">
            <button class="layui-btn"  lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i>查询</button>
            <a class="layui-btn yesterday" ><i class="layui-icon">&#xe615;</i>前一天</a>
            <button class="layui-btn" type="button" onclick="location.href= '<?= Url::to([$this->context->id . '/index'])?>'" >
                <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>刷新
            </button>
        </form>
    </div>
    <div class="layui-row layui-form" >
        <div class="layui-input-inline">
            <?= Html::dropDownList('to_shift', '', ArrayHelper::merge(['' => '请选择班别  '], UserInfo::$shiftArr ), ['name' => 'to'])?>
        </div>
        <button class="layui-btn" type="button" onclick="synchronization()" >
          同步to
        </button>
        <button class="layui-btn layui-bg-black" onclick="export_excel()"><i class="layui-icon"></i>导出</button>
    </div>
    <table class="layui-table">
        <thead>
        <tr>
            <th>
                <div class="layui-unselect header layui-form-checkbox" lay-skin="primary"><i class="layui-icon">&#xe605;</i></div>
            </th>
            <th>类型</th>
            <th>标题</th>
            <th>内容</th>
            <th>发布人</th>
            <th>from</th>
            <th>to</th>
            <th>备注</th>
            <th>操作</th>
        </thead>
        <tbody>
        <?php if (!empty($data)): ?>
            <?php foreach ($data as $value): ?>
                <tr >
                    <td>
                        <div class="layui-unselect layui-form-checkbox" lay-skin="primary" data-id=<?= $value['id'] ?>><i class="layui-icon">&#xe605;</i></div>
                    </td>
                    <td><?= $value['type'] ?></td>
                    <td  style="max-width: 310px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;"><?= $value['title'] ?></td>
                    <td style="max-width: 80px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;"><?= $value['content_data'] ?></td>
                    <td><?= (new UserInfo())->getUserName($value['user_id']) ?></td>
                    <td><?= (new UserInfo())->getUserName($value['from_id']) ?></td>
                    <td><?= UserInfo::$shiftArr[$value['shift']]  ?></td>
                    <td style="max-width: 150px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;"><?= $value['comment'] ?></td>
                    <td class="td-manage">
                        <button type="button" class="layui-btn layui-btn-xs" onclick="x_admin_show('编辑','<?= Url::to(['/blog/update', 'id' => $value['log_id']]) ?>')">编辑</button>
                        <?php
                            if($model->isCurrent( $value['createTime'] )){
                                echo '<button type="button" class="layui-btn layui-btn-xs layui-btn-danger delete-handover" data-id="'.$value['id'].'" >删除</button>';
                            }
                        ?>

                        <button type="button" class="layui-btn  layui-btn-normal layui-btn-xs" onclick="x_admin_show('添加备注','<?= Url::to([$this->context->id . '/add-comment', 'id' => $value['id']]) ?>',500)">添加备注</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
    <?= $this->render('@app/views/layouts/pagination', compact('pagination')) ?>
</div>

<?php
$sUrl = Url::to('synchronization');
$export_url = Url::to([$this->context->id . '/export']) ;
$keys = ['start_time','end_time'];
$show_time = [];
foreach ($keys as $val){
    $get = Yii::$app->request->get($val);
    if(!empty($get)){
        $show_time[$val] = $get;
        $show_time['before_'.$val] =  date('Y-m-d H:i:s',strtotime($get. ' -12 hour'));
    }else{
        $show_time[$val] = date('Y-m-d H:i:s',strtotime($queryParams[$val]. ' -12 hour'));
        $show_time['before_'.$val] =  date('Y-m-d H:i:s',strtotime($queryParams[$val]. ' -12 hour'));
    }
}

$js =<<<JS
 
    //日期时间
    layui.use('laydate', function() {
        var laydate = layui.laydate;
        $(this).removeAttr("lay-key");

        laydate.render({
            elem: '#startTime'
            ,trigger : 'click'
            ,type: 'datetime'
            ,value:" {$show_time['start_time']}"
        });
        laydate.render({
            elem: '#endTime'
            ,trigger : 'click'
            ,type: 'datetime'
            ,value:" {$show_time['end_time']}"
        });
    });

    $('.yesterday').click(function(){
 
        location.href="/handover/index?start_time={$show_time['before_start_time']}&end_time={$show_time['before_end_time'] }";
    });


    $('.delete-handover').click(function () {
        var id = $(this).data('id');
        layer.confirm('是否确认删除?', {icon: 3, title:'提示'}, function(index){

            $.get('delete?id='+id,function (res) {
                if( res.status == 200 ){
                    location.reload();
                }else{
                    layer.msg(res.msg);
                }
            },'json');
        })

    });

    function synchronization(){
        var to_shift = $("select[name='to_shift']").val();
        var idlist   = [];
        $('.layui-form-checked').each(function () {
            if (typeof($(this).data('id')) !="undefined" && $(this).data('id')!=0){
                idlist.push($(this).data('id'));
            }
        });
        if(!to_shift){
            return layer.msg('请选择班别');
        }
        if(idlist.length == 0){
            return layer.msg('请选择日志');
        }
        $.post("{$sUrl}",{to_shift:to_shift,idlist:idlist},function (res) {
            if(res.status == 200){
                location.reload();
            }else{
                layer.msg(res.msg);
            }
        },'json');

    }
    function export_excel(){
        var idlist   = [];
        $('.layui-form-checked').each(function () {
            if (typeof($(this).data('id')) !="undefined" && $(this).data('id')!=0){
                idlist.push($(this).data('id'));
            }
        });
   
        layui.use(['jquery', 'layer'], function() {
            var $ = layui.jquery; 
            $.ajax({
                type: 'GET',
                data: {idlist: idlist},
                dataType: 'json',
                url: '{$export_url}',
                success: function(res) {
                    if(res.status == 200){
                        LAY_EXCEL.exportExcel(res.data, res.msg, 'xlsx');
                    }else{
                        layer.msg(res.msg);
                    }
                }
            });
        });
    }

JS;
$this->registerJs($js,3);
?>

