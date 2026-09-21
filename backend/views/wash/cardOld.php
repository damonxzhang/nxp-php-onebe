<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use backend\models\UserInfo;

?>
<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">静电服清洗</a>
        <a href="javascript:;">
          <cite>静电服刷卡页面</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <div class="layui-row" style="display: flex;">
        <div id="showMessage" style="position: relative; left: 30%;font-family: Arial, sans-serif;font-size: 36px;font-weight: bold;font-style: italic;"></div>
    </div>
    <xblock>
    </xblock>
    <table class="layui-table"  id="data-table">
        <thead style="font-weight: bold;font-size:25px;">
            <th>姓名</th>
            <th>RFID</th>
            <th>刷卡信息</th>
            <th>PID</th>
            <th>今日刷卡时间</th>
            <th>班别</th>
            <th>区域</th>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<?php $this->beginBlock('footer') ?>
<script>
    $(document).ready(function() {
        getCard();
    });

     //扫卡
     function getCard() {
        $.post('<?= Url::to(['wash/get-card'])?>', function (res) {
            var rows = '';
            $.each(res.data, function(index, item) {
                var color = ''
                if(item.status == 200){
                    color = 'color:green;';
                }else{
                    color = 'color:red;';
                }
                rows += '<tr style="font-weight: bold;'+color+'font-size:20px;">' +
                        '<td>' + item.user_name + '</td>' +
                        '<td>' + item.rfid + '</td>' +
                        '<td>' + item.message + '</td>' +
                        '<td>' + item.pid + '</td>' +
                        '<td>' + item.create_time + '</td>' +
                        '<td>' + item.shift + '</td>' +
                        '<td>' + item.area + '</td>' +
                        '</tr>';
            });
            
            $('#data-table tbody').html(rows);
            $("#showMessage").text(res.msg);
            if(res.status == 200){
                $("#showMessage").css("color", "green");
            }else{
                $("#showMessage").css("color", "red");
            }
            setTimeout(getCard, 3*1000);  //设置请求api接口时间3秒
        }, 'json');
    }

</script>
<?php $this->endBlock() ?>
