<?php
use backend\models\search\ShelfSearch;
use yii\helpers\Url;
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
                            <button type="button" class="layui-btn layui-btn-warm">充电中</button>
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
                                                <button type="button" style="width:100px;height:100px" class="layui-btn layui-btn-lg
                                                <?php
                                                if($data['status'] == $data::STATUS_NO){
                                                    echo "layui-btn-primary";
                                                }else
                                                    if($data['type'] == $data::TYPE_USE){//使用中
                                                        echo "layui-btn-normal";
                                                    }elseif($data['type'] == $data::TYPE_ABNORMAL){//异常
                                                        echo "layui-btn-danger";
                                                    }elseif($data['type'] == $data::TYPE_CHARGING){//充电中
                                                        if ($data['end_time'] > date('Y-m-d H:i:s'))
                                                        echo "layui-btn-warm";
                                                    }elseif($data['type'] == $data::TYPE_CHANGE){//充电中
                                                        echo "layui-btn-warm";
                                                    }
                                                ?>" onclick="location.href= '<?= Url::to([$this->context->id . '/update?id='.$data['id']])?>'">
                                                    <?= mb_strlen($data['number'] ) > 1? $data['number'] :'0'.$data['number']?><br>
                                                    <?= (new \backend\models\UserInfo())->getUserName($data->user_id)?>
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
                <div class="layui-col-sm12 layui-col-md6">
                 <div class="layui-card">
                        <div class="layui-card-header">2号充电柜</div>
                        <div class="layui-card-header">
                            <button type="button" class="layui-btn layui-btn-warm">充电中</button>
                            <button type="button" class="layui-btn">可领取</button>
                            <button type="button" class="layui-btn layui-btn-normal">使用中</button>
                            <button type="button" class="layui-btn layui-btn-danger">异常</button>
                            <button type="button" class="layui-btn layui-btn-primary">关闭</button>
                        </div>
                        <div class="layui-card-body" style="min-height: 300px;">
                            <div id="main2" class="layui-col-sm12" >
                                <table class="layui-table" lay-skin="line" style="height: 500px;">
                                    <?php if (!empty($model2)): ?>
                                        <?php foreach ($model2 as $key => $data): ?>
                                            <?php if($key == 0){ echo "<tr>";}?>
                                            <td data-id="<?=$data['id']?>" class="box-btn" data-status="<?=$data['status']?>">
                                                <button type="button" style="width:100px;height:100px" class="layui-btn layui-btn-lg
                                                <?php
                                                if($data['status'] == $data::STATUS_NO){
                                                    echo "layui-btn-primary";
                                                }else
                                                    if($data['type'] == $data::TYPE_USE){//使用中
                                                        echo "layui-btn-normal";
                                                    }elseif($data['type'] == $data::TYPE_ABNORMAL){//异常
                                                        echo "layui-btn-danger";
                                                    }elseif($data['type'] == $data::TYPE_CHARGING){//充电中
                                                        if ($data['end_time'] > date('Y-m-d H:i:s'))
                                                        echo "layui-btn-warm";
                                                    }elseif($data['type'] == $data::TYPE_CHANGE){//充电中
                                                        echo "layui-btn-warm";
                                                    }
                                                ?>" onclick="location.href= '<?= Url::to([$this->context->id . '/update?id='.$data['id']])?>'">
                                                    <?= mb_strlen($data['number'] ) > 1? $data['number'] :'0'.$data['number']?><br>
                                                    <?= (new \backend\models\UserInfo())->getUserName($data->user_id)?>
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
            <div class="layui-row layui-col-space15">
                <div class="layui-col-sm12 layui-col-md6">
                    <div class="layui-card">
                        <div class="layui-card-header">3号充电柜</div>
                        <div class="layui-card-header">
                            <button type="button" class="layui-btn layui-btn-warm">充电中</button>
                            <button type="button" class="layui-btn">可领取</button>
                            <button type="button" class="layui-btn layui-btn-normal">使用中</button>
                            <button type="button" class="layui-btn layui-btn-danger">异常</button>
                            <button type="button" class="layui-btn layui-btn-primary">关闭</button>
                        </div>
                        <div class="layui-card-body" style="min-height: 200px;">
                            <div id="main1" class="layui-col-sm12" >
                                <table class="layui-table" lay-skin="line" style="height: 500px;">
                                    <?php if (!empty($model3)): ?>
                                        <?php foreach ($model3 as $key => $data): ?>
                                            <?php if($key == 0){ echo "<tr>";}?>
                                            <td data-id="<?=$data['id']?>" class="box-btn" data-status="<?=$data['status']?>">
                                                <button type="button" style="width:100px;height:100px" class="layui-btn layui-btn-lg
                                                <?php
                                                if($data['status'] == $data::STATUS_NO){
                                                    echo "layui-btn-primary";
                                                }else
                                                    if($data['type'] == $data::TYPE_USE){//使用中
                                                        echo "layui-btn-normal";
                                                    }elseif($data['type'] == $data::TYPE_ABNORMAL){//异常
                                                        echo "layui-btn-danger";
                                                    }elseif($data['type'] == $data::TYPE_CHARGING){//充电中
                                                        if ($data['end_time'] > date('Y-m-d H:i:s'))
                                                        echo "layui-btn-warm";
                                                    }elseif($data['type'] == $data::TYPE_CHANGE){//充电中
                                                        echo "layui-btn-warm";
                                                    }
                                                ?>" onclick="location.href= '<?= Url::to([$this->context->id . '/update?id='.$data['id']])?>'">
                                                    <?= mb_strlen($data['number'] ) > 1? $data['number'] :'0'.$data['number']?><br>
                                                    <?= (new \backend\models\UserInfo())->getUserName($data->user_id)?>
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
        console(res.data.url)

        $.ajax({
            url:'charge-tank/update',
            type:'post',
            dataType:'json',
            data:{id:id},
            success:function(res) {
                if(res.status == 200){
                    window.location.href=res.data.url;
                }else{
                    layer.msg(res.msg);
                }
            }
        });
    
    });

JS;
$this->registerJs($js );

?>