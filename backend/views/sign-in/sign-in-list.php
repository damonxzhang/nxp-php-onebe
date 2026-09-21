<?php

use backend\models\TrainUser;
use yii\helpers\Html;
$baseUrl = Yii::$app->params['base_url'];
$yes = count($data['yes']);
$no  = count($data['no']);

?>
<style>
    .text-list span{
        margin: 0 2rem;
        font-size: 20px;
    }
    .yes-label{
        background-color: #404553;

    }
    .no-label{
        background-color: #009688;
    }
    .else-label{
        background-color: #3b324b4a;
    }
    .table-label{
        width: 90%;margin: 0 auto;padding: 10px 0;text-align: center;
        color:#FFFFFF;
    }

    table tbody {
        display: block;
        height: 300px;
        overflow-y: scroll;
    }
    .status{
        width: 30%;
        float: right;
    }
    .duration{
        float: right;
    }
    table thead,
    tbody tr {
        display: table;
        width: 99%;
        table-layout: fixed;/*重要  表格固定算法*/
    }

    table thead {/*留出滚动条的位置*/
        width: 100%
    }

    table thead th {
        background: #ccc;
    }
</style>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">培训考核</a>
        <a href="javascript:;">培训列表</a>
        <a href="javascript:;">
          <cite>培训签到</cite>
        </a>
      </span>
</div>
<div class="x-body layui-anim layui-anim-up">
    <div class="layui-row">
        <div class="layui-col-md6" style="text-align: right">
            <div class="grid-demo">
                <div class="layui-input-inline">
                    <?= Html::input('text','code','',[
                        'style' => '',
                        'lay-vertype'=>"tips",
                        'class' => 'layui-input layui-col-md4',
                        'autocomplete'=>"off"
                    ]) ?>
                </div>

                <?= Html::button('签到', ['class' => 'layui-btn sign-in-sub' ])?>
            </div>
        </div>

        <div class="layui-col-md4" style="text-align: center;">
            <div class="grid-demo grid-demo-bg1 text-list " >
                <strong><span>已签到:<?= $yes ?></span></strong>
                <strong><span>未签到:<?= $no ?></span></strong>
                <strong><span>签到率:<?= $model->progress ?>% </span></strong>
            </div>
        </div>
    </div>
    <div style="height:3rem"></div>
    <div class="layui-row">

        <?php foreach ((array) $data as $key => $value):?>
            <div class=" layui-col-md4" >
                <?php
                    if($key == 'yes'){
                        $text = '已签到';
                    }elseif($key == 'no'){
                        $text = '未签到';
                    }elseif($key == 'else'){
                        $text = '异常';
                    }
                ?>
                <div class="<?=$key?>-label table-label" style=""><?= $text?></div>
                <table class="layui-table" style="width: 90%;margin: 0 auto;" lay-skin="line">
      
                    <thead>
                    <tr>
                        <td>姓名</td>
                    </tr>
                    </thead>
                    <tbody>
                        <?php foreach ((array) $value as $user): ?>
                            <tr>
                                <td>
                                    <?=$user['chinese_name']?>
                                    <?php if($user['sign_in'] == TrainUser::SIGN_IN_YES):?>
                                        <span class="duration">
                                            <?= $user['study_date'].'分钟'; ?>
                                        </span>
                                    <?php else:?>
                                        <span class="status" data-id="<?=$user['id']?>">
                                            <?php
                                                if(!empty($user['status'])){
                                                    $key = $user['status'];
                                                }else{
                                                    $key = TrainUser::STATUS_NORMAL;
                                                }
                                                echo Html::dropDownList('状态', $key ,TrainUser::$statusArr,['class' => 'user-status']);
                                            ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        <?php endforeach;?>

    </div>
</div>
<?php
$train_id = Yii::$app->request->get('id');

$js = <<<JS

    $(function(){
        $("input[name='code']").focus();
    });
    function sign_in(){
         var code = $('input[name="code"]').val();
        var train_id = "{$train_id}";
        if(!code){
            layer.msg('请输入卡号！');return false;
        }
        
        $.post('sign-in',{code:code,train_id:train_id},function(res) {
            if(res.status == 200){
                window.location.reload();
            }else{
                layer.msg(res.msg);
                window.setTimeout(function() {
                    window.location.reload();   
                },1000)
            }
        },'json')
        
    }
    /**
    *修改签到状态 
    */
    $(document).on('change','.user-status',function(){
        var id = $(this).parents('span').data('id');
        var status = $(this).val();
        $.post('update-user-status',{id:id,status:status},function(res){
            if(res.status == 200){
                window.location.reload();
            }else{
                layer.msg(res.msg);
            }
        },'json')
    });
    
    $('.sign-in-sub').click(function() {
        sign_in();
    });
    
    $("input[name='code']").keydown(function(event) {
         if (event.keyCode == 13) {
              sign_in(); 
        }
    });
    
JS;

$this->registerJs($js);

?>
