<?php

use yii\helpers\Html;
use yii\helpers\Url;
use backend\widgets\LayuiGridView;

?>
    <style>
        .btn-div {
            width: 100%;
            height: 50px;
            background: #e6e6e6;
            text-align: center;
            line-height: 50px;
        }
    </style>
    <div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">COMPOUND货架</a>
        <a href="javascript:;">
          <cite>领取列表</cite>
        </a>
      </span>
    </div>
    <form class="layui-form" lay-filter="form-data">
        <div class="x-body">
            <?= LayuiGridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    'id',
                    'cont_no',
                    [
                        'attribute' => 'shelf_id',
                        'value' => function ($row) {
                            return $row->shelfNumber;
                        }
                    ],
                    [
                        'attribute' => 'grid_id',
                        'value' => function ($row) {
                            return $row->gridNumber;
                        }
                    ],
                    [
                        'label' => '醒料截止时间',
                        'value' => function ($row) {
                            return date('Y-m-d H:i:s', $row->end_time);
                        }
                    ],
                    [
                        'label' => '过期时间',
                        'value' => function ($row) {
                            return date('Y-m-d H:i:s', $row->expire_time);
                        }
                    ],
                    [
                        'label' => '扫描物料编号',
                        'format' => 'raw',
                        'value' => function ($row) {
                            return Html::input('text', 'cont_no[]', '', ['class' => 'layui-input ','data-id'=>$row->cont_no, 'placeholder' => '物料编号']);
                        }
                    ]
                ]
            ]) ?>
            <div class="btn-div">
                <?= Html::button('确认', ['class' => 'layui-btn submit']) ?>
                <?= Html::button('位置指引', ['class' => 'layui-btn layui-btn-warm']) ?>
            </div>

        </div>
    </form>
    <?php if(!empty($_GET['chinese_name'])):?>
    <div style="font-size:80px; text-align: center;margin-top: 5rem;"><?=$_GET['chinese_name']?>,请进行二次确认！</div>
    <?php endif;?>
<?php

$js = <<<JS
    var form = '';
    $(function(){
        $('tbody').find('input:first').focus() ;  
    });
    layui.use('form', function(){
        form = layui.form; 
    });

    function subform(){
        let isSub = true;
        let const_list = [];
        let cont_no = [];
        $('tbody').find('input').each(function(){ 
            if(!$(this).val()){
                layer.msg('请扫描输入物料扁号！');
                isSub = false; 
                return false;
            }
            const_list.push($(this).data('id'));
            cont_no.push($(this).val());
        });
 
        if(isSub === true){
            $.post('',{ cont_no:cont_no,const_list:const_list },function(res){
                if(res.status == 200){
                    window.location.reload(); 
                }else{
                    layer.msg(res.msg);
                }
            },'json');
        }
      
    }

    $('input').keydown(function(event){
        if(event.keyCode==13){
            var inputs = document.getElementsByTagName("input");
             for(var i = 0;i<inputs.length;i++){
           
                // 如果是最后一个，则焦点回到第一个
                if(i==(inputs.length-1)){
                   subform();
                    break;
                }else if(this == inputs[i]){
                    inputs[i+1].focus();
                    break;
                }
            }
        }
       
　　});
    
    $('.submit').click(function(){ 
        subform(); 
    });
    
  
JS;
$this->registerJs($js);
?>