<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Url;

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
    <!-- <div class="layui-row">
        <form class="layui-form">
            <div class="layui-form-item">
                <label for="name" class="layui-form-label">
                    <span class="x-red">*</span>货架编号:
                </label>
                <div class="layui-input-inline">
                    <input type="text" id="autofocus" autocomplete="off" class="layui-input" >
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">
                    <span class="x-red">*</span>物料编号:
                </label>
                <div class="layui-input-inline">
                    <input type="text"  id="cont_no" autocomplete="off" class="layui-input" >
                </div>
            </div>
        </form>
    </div> -->
    <xblock></xblock>
    <table class="layui-table">
        <thead style="font-weight: bold;font-size:25px;">
            <th>姓名</th>
            <th>RFID</th>
            <th>PID</th>
            <th>刷卡时间</th>
        </thead>
        <tbody>
        <tr style="font-weight: bold;font-size:20px;">
            <td id='username'></td>
            <td id='rfid'></td>
            <td id='pid'></td>
            <td id='brushtime'></td>
        </tr>
        </tbody>
    </table>
   
</div>
<?php $this->beginBlock('footer') ?>
<script>

    // $(document).ready(function() {
    //     getCard();
    // });

     //扫卡
    // function getCard() {
    //     $.post('<?= Url::to(['wash-clothes/get-card'])?>', function (res) {
    //         console.log(res.data.rfid);
    //         $("#username").text(res.data.username);
    //         $("#rfid").text(res.data.rfid);
    //         $("#pid").text(res.data.pid);
    //         $("#brushtime").text(res.data.brushtime);
    //         setTimeout(getCard, 3*1000);  //设置请求api接口时间2秒
    //     }, 'json');
    // }
</script>
<?php $this->endBlock() ?>
