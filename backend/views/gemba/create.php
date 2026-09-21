<?php

use backend\models\UserInfo;
use yii\helpers\Url;
use backend\models\Gemba;

?>
    <div class="x-body layui-anim layui-anim-up">
        <form class="layui-form">

            <div class="layui-form-item">
                <label for="nickname" class="layui-form-label">
                    <span class="x-red">*</span>周别:
                </label>
                <div class="layui-input-inline" style="width: 800px;">
                    <input class="layui-input " readonly="readonly" placeholder="周别" lay-verify="required" value="<?=date('W') ?>" name="weeks" >
                </div>
            </div>
            <div class="layui-form-item">
                <label for="nickname" class="layui-form-label">
                    <span class="x-red">*</span>月份:
                </label>
                <div class="layui-input-inline" style="width: 800px;">
                    <input class="layui-input " readonly="readonly" placeholder="月份" lay-verify="required" value="<?=date('m') ?>" name="month" >
                </div>
            </div>

            <div class="layui-form-item">
                <label for="nickname" class="layui-form-label">
                    <span class="x-red">*</span>检查日期:
                </label>
                <div class="layui-input-inline" style="width: 800px;">
                    <input class="layui-input " placeholder="检查日期" lay-verify="required" value="<?=date('Y/m/d') ?>" name="spectionDate" id="spectionDate-time" autocomplete="off">
                </div>
            </div>
            <div class="layui-form-item">
                <label for="nickname" class="layui-form-label">
                    <span class="x-red">*</span>区域:
                </label>
                <div class="layui-input-inline" style="width: 800px;">
                    <select  class="layui-input "  name="section" lay-verify="required" disabled="disabled">
                        <option value="<?=$section['id'] ?>"><?=$section['value']?> </option>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label for="nickname" class="layui-form-label">
                    <span class="x-red">*</span>班别:
                </label>
                <div class="layui-input-inline" style="width: 800px;">
                    <select name="shiftList" lay-verify="required" disabled="disabled">
                        <?php foreach (UserInfo::$shiftListArr as $key => $value):?>
                            <option value="<?=$key?>"<?= $key == $userInfo['shift'] ?'selected ':'' ?>><?=$value?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <label for="nickname" class="layui-form-label">
                    <span class="x-red">*</span>gemba小组:
                </label>
                <div class="layui-input-inline" style="width: 800px;">

                    <select name="team" lay-verify="required">
                        <?php if(in_array($userInfo['team'],array_keys($gembaTeam))): ?>
                            <option value="<?=$gembaTeam[$userInfo['team']]['id']?>" ><?=$gembaTeam[$userInfo['team']]['teamName']?></option>

                        <?php else:?>
                            <option value="">请选择gemba小组</option>
                        <?php
                                foreach ($gembaTeam as $value):
                        ?>
                                    <option value="<?=$value['id']?>" <?= $value['id'] == $userInfo['team'] ?'selected ':'' ?>><?=$value['teamName']?></option>
                        <?php
                                endforeach;
                            endif;
                        ?>
                    </select>

                </div>
            </div>

            <?= \backend\widgets\SelectSearch::widget([
                'name' => 'participateUser',
                'columnName' => 'participateUserId',
                'text' => '参与人',
                'required' => true,
                'section' => $section,
                'options' => [
                    'multiple' => true,
                ],
                'addSelectList' => [
                    'gemba-role' => [
                        'text' =>'请选择角色',
                        'data'=> Gemba::$showGeambaRole,
                    ],
                ]
            ])?>
            <div class="layui-form-item">
                <label for="nickname" class="layui-form-label">
                    <span class="x-red">*</span>位置:
                </label>
                <div class="layui-input-inline">
                    <input type="text" name="location" placeholder="请输入位置"  autocomplete="off" lay-verify="required" class="layui-input" style="width: 800px;">
                </div>
            </div>

            <div class="layui-form-item">
                <label for="nickname" class="layui-form-label">
                    <span class="x-red">*</span>类型:
                </label>
                <div class="layui-input-inline" style="width: 800px;">
                    <select name="type" lay-verify="required">
                        <option value="">请选择类型</option>
                        <?php foreach (Gemba::$type  as $key => $value):?>
                            <option value="<?=$key?>"><?=$value?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <label for="nickname" class="layui-form-label">
                    <span class="x-red">*</span>问题描述:
                </label>
                <div class="layui-input-inline">
                    <input type="text" name="problem" placeholder="请输入问题描述"  autocomplete="off" lay-verify="required" class="layui-input" style="width: 800px;">
                </div>
            </div>
            <div class="layui-form-item">
                <label for="nickname" class="layui-form-label">
                    <span class="x-red">*</span>是否有标准要求:
                </label>
                <div class="layui-input-inline" style="width: 800px;">
                    <select name="isStandard">
                        <option value="0">否</option>
                        <option value="1">是</option>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label for="nickname" class="layui-form-label">
                    <span class="x-red">*</span>是否提交IR:
                </label>
                <div class="layui-input-inline" style="width: 800px;">
                    <select name="submitIR">
                        <option value="0">否</option>
                        <option value="1">是</option>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label for="nickname" class="layui-form-label">
                    <span class="x-red">*</span>是否完成:
                </label>
                <div class="layui-input-inline" style="width: 800px;">
                    <select name="isComplete" lay-filter="isOk" >
                        <option value="0">否</option>
                        <option value="1">是</option>
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <label for="nickname" class="layui-form-label">
                    <span class="x-red">*</span>完成时间:
                </label>
                <div class="layui-input-inline" style="width: 800px;">
                    <input class="layui-input" placeholder="完成时间"  value="" name="completeDate" id="completeDate" autocomplete="off">
                </div>
            </div>

            <?= \backend\widgets\SelectSearch::widget([
                    'name' => 'completeUser',
                    'columnName' => 'completeUserId',
                    'text' => '完成人',
                    'required' => true,
                    'section' => $section,
                    'options' => [
                        'multiple' => true,
                    ],
                    'addSelectList' => [
                        'gemba-role' => [
                            'text' =>'请选择角色',
                            'data'=> Gemba::$showGeambaRole,
                        ],
                    ]
            ]) ?>

            <div class="layui-form-item">
                <label for="nickname" class="layui-form-label">
                    备注:
                </label>
                <div class="layui-input-inline">
                    <input type="text" name="common" placeholder="请输入备注"  autocomplete="off" class="layui-input" style="width: 800px;">
                </div>
            </div>

            <div class="layui-upload" style="margin-left: 110px;">
                <button type="button" class="layui-btn layui-bg-cyan" id="fileList">请选择文件</button>
                <div class="layui-upload-list">
                    <table class="layui-table" style="width: 800px;">
                        <thead>
                        <tr>
                            <th>文件名</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody id="demoList"></tbody>
                    </table>
                </div>
                <button type="button" onclick="uploadFile(this)" class="layui-btn layui-btn-normal" id="testListAction">上传文件</button>
            </div>
            <div class="layui-form-item"  style="margin-top: 50px;">
                <label for="" class="layui-form-label">
                </label>
                <button class="layui-btn" lay-filter="add" lay-submit="">
                    保存
                </button>
            </div>
        </form>
    </div>
<?php
$js =<<<JS
    //日期时间
    layui.use('laydate', function() {
        var laydate = layui.laydate;
        $(this).removeAttr("lay-key");

        laydate.render({
            elem: '#spectionDate-time'
            ,trigger : 'click'
            ,type: 'datetime'
            ,format: 'yyyy/MM/dd'
        });
        laydate.render({
            elem: '#completeDate'
            ,trigger : 'click'
            ,type: 'datetime'
            ,format: 'yyyy/MM/dd'
        });

    });
    layui.use(['form','layer'], function(){
        $ = layui.jquery;
        var form = layui.form ,layer = layui.layer;

        //监听提交
        form.on('submit(add)', function(data){ 
            layer.load(3);
            $.post( 'create' , data.field, function (res) {
                layer.closeAll();
                if (res.status === 200) {
                    layer.msg(res.msg, {icon: 1,time: 1500}, function () {
                        var index = parent.layer.getFrameIndex(window.name);
                        parent.layer.close(index);
                        parent.location.reload();
                    })
                } else {
                    layer.msg(res.msg, {icon: 2, time: 3000})
                }
            }, 'json');
            return false;
        });

        form.on('select(isOk)',function(data){
            if(data.value == '0'){

                $('#completeDate').attr('lay-verify',false);
                $('#completeUserId').attr('_lay-verify',false);
                $("input[name='completeUserId']").attr('lay-verify',false);

            }else if(data.value == '1'){

                $('#completeDate').attr('lay-verify','required');
                $('#completeUserId').attr('_lay-verify','required');
                $("input[name='completeUserId']").attr('lay-verify','required');


            }
        });
    });
JS;
$this->registerJs($js,3);

?>
