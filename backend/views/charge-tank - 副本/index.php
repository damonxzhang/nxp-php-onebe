<?php
use backend\models\search\ShelfSearch;
?>
<style>
    .layui-btn-primary{
        background-color: #ccc2c2;
    }
</style>
<div class="x-body">
    <div class="layui-fluid">
        <div class="layui-row layui-col-space15">
            <div class="layui-col-sm12 layui-col-md6">
                <div class="layui-card">
                    <div class="layui-card-header">1号充电柜</div>
                    <div class="layui-card-header">
                        <button type="button" class="layui-btn">可领取</button>
                        <button type="button" class="layui-btn layui-btn-normal">使用中</button>
                        <button type="button" class="layui-btn layui-btn-danger">异常</button>
                        <button type="button" class="layui-btn layui-btn-primary">关闭</button>
                    </div>
                    <div class="layui-card-body" style="min-height: 200px;">
                        <div id="main1" class="layui-col-sm12" >
                            <table class="layui-table" lay-skin="line" style="height: 500px;">
                                <?php if (!empty($model)): ?>
                                    <?php foreach ($model as $key => $data): ?>
                                        <?php if($key == 0){ echo "<tr>";}?>
                                            <td data-id="<?=$data['id']?>" class="box-btn" data-status="<?=$data['status']?>">
                                                <button type="button" class="layui-btn layui-btn-lg
                                                <?php
                                                    if($data['status'] == $data::STATUS_NO){
                                                        echo "layui-btn-primary";
                                                    }else
                                                    if($data['type'] == $data::TYPE_USE){//使用中
                                                        echo "layui-btn-normal";
                                                    }elseif($data['type'] == $data::TYPE_ABNORMAL){//异常
                                                        echo "layui-btn-danger";
                                                    }
                                                ?>">
                                                    <?= mb_strlen($data['number'] ) > 1? $data['number'] :'0'.$data['number']?>
                                                </button>
                                            </td>

                                        <?php if($key != 0 && ($key+1)%5 == 0){ echo "</tr><tr> ";}?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<meta http-equiv="refresh" content="30">
<?php
$js = <<<JS
    $('.box-btn').click(function() {
        var id = $(this).data('id');
        var msg = '是否开启充电仓？';
        var status = $(this).data('status');
        if (status > 0){
            msg = '是否关闭充电仓？';
        }
        layer.confirm(msg,function(index) {
            $.ajax({
                url:'edit',
                dataType:'json',
                type:'POST',
                data: {id:id,status:status},
                success:function(res) {
                    if(res.status == 200){
                        window.location.reload();
                    }else{
                        layer.msg(res.msg)
                    }
                } 
            });
            
            layer.close(index);
        }); 
    
    });

JS;
$this->registerJs($js );

?>