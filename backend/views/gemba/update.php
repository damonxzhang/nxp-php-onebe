<?php

use backend\models\UserInfo;
use yii\helpers\Url;
use backend\models\Gemba;
use backend\widgets\FormSelects;

?>
    <div class="x-body layui-anim layui-anim-up">
        <form class="layui-form">

            <div class="layui-form-item">
                <label for="nickname" class="layui-form-label">
                    <span class="x-red">*</span>周别:
                </label>
                <div class="layui-input-inline" style="width: 800px;">
                    <input class="layui-input " readonly="readonly" placeholder="周别" lay-verify="required" value="<?=$model['weeks']?>" name="weeks" >
                </div>
            </div>
            <div class="layui-form-item">
                <label for="nickname" class="layui-form-label">
                    <span class="x-red">*</span>月份:
                </label>
                <div class="layui-input-inline" style="width: 800px;">
                    <input class="layui-input " readonly="readonly" placeholder="月份" lay-verify="required" value="<?= $model["month"]?>" name="month" >
                </div>
            </div>

            <div class="layui-form-item">
                <label for="nickname" class="layui-form-label">
                    <span class="x-red">*</span>检查日期:
                </label>
                <div class="layui-input-inline" style="width: 800px;">
                    <input class="layui-input " placeholder="检查日期" lay-verify="required" value="<?= date('Y/m/d',$model["spectionDate"]) ?>" name="spectionDate" id="spectionDate-time" autocomplete="off">
                </div>
            </div>
            <div class="layui-form-item">
                <label for="nickname" class="layui-form-label">
                    <span class="x-red">*</span>区域:
                </label>
                <div class="layui-input-inline" style="width: 800px;">
                    <select name="section">
                        <option value="<?=$section['id'] ?>"><?=$section['value']?> </option>

                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label for="nickname" class="layui-form-label">
                    <span class="x-red">*</span>班别:
                </label>
                <div class="layui-input-inline" style="width: 800px;">
                    <select name="shiftList">
                        <option value="<?=$shift['id'] ?>"><?=$shift['value']?> </option>

                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <label for="nickname" class="layui-form-label">
                    <span class="x-red">*</span>gemba小组:
                </label>
                <div class="layui-input-inline" style="width: 800px;">
                    <select name="team">
                        <option value="<?=$gembaTeam['id'] ?>"><?=$gembaTeam['value']?> </option>
                    </select>
                </div>
            </div>
            <?= \backend\widgets\SelectSearch::widget([
                'name' => 'participateUser',
                'columnName' => 'participateUserId',
                'text' => '参与人',
                'value' => $model->participateUserId,
                'required' => true,
                'section' => $section,
                'options' => [
                    'class' => 'js-data-example-ajax',
                    'multiple' => true,
                    'style' => ['width'=> '800px'],
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
                    <input type="text" name="location" value="<?= $model["location"] ?>" placeholder="请输入位置"  autocomplete="off" lay-verify="required" class="layui-input" style="width: 800px;">
                </div>
            </div>

            <div class="layui-form-item">
                <label for="nickname" class="layui-form-label">
                    <span class="x-red">*</span>类型:
                </label>
                <div class="layui-input-inline" style="width: 800px;">
                    <select name="type">
                        <option value="">请选择类型</option>
                        <?php foreach (Gemba::$type  as $key => $value):?>
                            <option value="<?=$key?>"  <?= $model['type'] == $key ? ' selected = "selected"' : '' ?>><?=$value?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <label for="nickname" class="layui-form-label">
                    <span class="x-red">*</span>问题描述:
                </label>
                <div class="layui-input-inline">
                    <input type="text" name="problem" value="<?= $model['problem']?>" placeholder="请输入问题描述"  autocomplete="off" lay-verify="required" class="layui-input" style="width: 800px;">
                </div>
            </div>
            <div class="layui-form-item">
                <label for="nickname" class="layui-form-label">
                    <span class="x-red">*</span>是否有标准要求:
                </label>
                <div class="layui-input-inline" style="width: 800px;">
                    <select name="isStandard">
                        <option value="1"  <?= $model['isStandard'] == 1 ? ' selected = "selected"' : '' ?>>是</option>
                        <option value="0"  <?= $model['isStandard'] == 0 ? ' selected = "selected"' : '' ?>>否</option>
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <label for="nickname" class="layui-form-label">
                    <span class="x-red">*</span>是否提交ir:
                </label>
                <div class="layui-input-inline" style="width: 800px;">
                    <select name="submitIR">
                        <option value="1"  <?= $model['submitIR'] == 1 ? 'selected = "selected"' : '' ?>>是</option>
                        <option value="0"  <?= $model['submitIR'] == 0 ? 'selected = "selected"' : '' ?>>否</option>
                    </select>
                </div>
            </div>

            <?php if($model->showPmVisible($userInfo)):?>
                <div class="layui-form-item">
                    <label for="nickname" class="layui-form-label">
                        <span class="x-red">*</span>PM可见:
                    </label>
                    <div class="layui-input-inline" style="width: 800px;">
                        <select name="pmVisible">
                            <?php foreach ($model::$pm_visible_arr as $key => $value):?>
                                <option value="<?=$key?>" <?= $key == $model->pmVisible ? ' selected':''  ?> > <?=$value?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                </div>
            <?php endif;?>

            <div class="layui-form-item">
                <label for="nickname" class="layui-form-label">
                    <span class="x-red">*</span>是否完成:
                </label>
                <div class="layui-input-inline" style="width: 800px;">
                    <select name="isComplete" lay-filter="isOk" >
                        <option value="0"  <?= $model['isComplete'] == 0 ? ' selected = "selected"' : '' ?>>否</option>
                        <option value="1"  <?= $model['isComplete'] == 1 ? ' selected = "selected"' : '' ?>>是</option>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label for="nickname" class="layui-form-label">
                     完成时间:
                </label>
                <div class="layui-input-inline" style="width: 800px;">
                    <input class="layui-input" placeholder="完成时间" value="<?=$model['completeDate'] ?date('Y/m/d',$model['completeDate']) :''?>" lay-verify=" <?= $model['completeDate'] ? 'required' :'' ?> " name="completeDate" id="completeDate" autocomplete="off">
                </div>
            </div>
            <?= \backend\widgets\SelectSearch::widget([
                'name' => 'completeUser',
                'columnName' => 'completeUserId',
                'text' => '完成人',
                'value' => $model->completeUserId,
                'initValueText' => [],
                'required' => true,
                'section' => $section,
                'options' => [
                    'multiple' => true,
                    'style' => ['width'=> '800px'],
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
                    <input type="text" name="common" value="<?= $model['common']?>" placeholder="请输入备注"  autocomplete="off" class="layui-input" style="width: 800px;">
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
                        <tbody id="demoList">
                        <?php if(!empty($model['problemImg'])): foreach ( $model['problemImg'] as $value):?>
                        <tr>
                            <td><img src="<?= Yii::$app->request->getHostInfo().'/'.$value?>" /></td>
                            <td><span style="color: #5FB878;">上传成功</span></td>
                            <td>
                                <a class="layui-btn layui-btn-xs delete-images">删除</a>
                                <input type="hidden" name="upload_file[]" value="<?=$value ?>">
                            </td>
                        </tr>
                        <?php endforeach;endif; ?>
                        </tbody>
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

<?php $this->beginBlock('footer') ?>
    <script>
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
                // layer.load(3);
                console.log(data);
                $.post('<?= Url::to(['gemba/update','id' => $model['id']]) ?>', data.field, function (res) {
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
    </script>

    <script>
        $(function(){
            $(document).on('click','.delete-images',function () {
                $(this).parents('tr').remove();
            })

        })
    </script>

<?php $this->endBlock() ?>