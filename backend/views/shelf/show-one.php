<?php
use backend\models\search\ShelfSearch;
?>
<style>
    .layui-btn-lg{
        height:250px;
    }
</style>
<div class="x-body">
    <div class="layui-fluid">
        <div class="layui-row layui-col-space15">
            <div class="layui-col-sm12 layui-col-md6">
                <div class="layui-card">
                    <div class="layui-card-header">
                        <button type="button" class="layui-btn layui-btn-primary">空闲</button>
                        <button type="button" class="layui-btn">可领取</button>
                        <button type="button" class="layui-btn layui-btn-danger">过期中</button>
                        <button type="button" class="layui-btn layui-btn-normal">醒料中</button>
                        <button type="button" class="layui-btn layui-btn-warm">预警中</button>
                        <button type="button" class="layui-btn">领取中</button>
                        <button type="button" class="layui-btn layui-btn-danger">异常</button>
                    </div>
                    <div class="layui-card-body" style="min-height: 400px;">
                        <div id="main1" class="layui-col-sm12" >
                            <table class="layui-table" lay-skin="line" style="width:50px;height:600px;overflow:auto">
                                <?php if (!empty($dataProvider)): ?>
                                    <?php foreach ($dataProvider as $data): ?>
                                        <tr>
                                        <?php foreach ($data as $value): ?>
										    <td><?= (new ShelfSearch())->getStatus($value['status'], $value['shelf_number']).'<br>' .(new ShelfSearch())->getPartNo($value['id']).'<br>' .(new ShelfSearch())->getContNo($value['id']).'</button>' ?></td>

                                        <?php endforeach; ?>
                                        </tr>
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
