<?php
use yii\helpers\Url;
?>
<div class="x-nav"></div>
<div class="x-body">
    <div class="layui-row" style="display: flex;">
        <div id="showMessage" style="position: relative; left: 30%;font-family: Arial, sans-serif;font-size: 36px;font-weight: bold;font-style: italic;"></div>
    </div>
    <table class="layui-table"  id="data-table">
        <thead style="font-weight: bold;font-size:25px;">
            <th>姓名</th>
            <th>PID</th>
            <th>区域</th>
            <th>帽子刷卡时间</th>
            <th>帽子刷卡信息</th>
            <th>静电服刷卡时间</th>
            <th>静电服刷卡信息</th>
            <th>裤子刷卡时间</th>
            <th>裤子刷卡信息</th>
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
        $.post('<?= Url::to(['wash/sync-and-get-records'])?>', function (res) {
            if (!res || res.length === 0) {
                $("#showMessage").text('今日暂无人员刷卡记录');
                $("#showMessage").css("color", "red");
                $('#data-table tbody').html('<tr style"text-align: center; padding: 20px;"><td colspan="9">今日暂无人员刷卡记录</td></tr>');
                return;
            }
            if(res.code > 200){
                $("#showMessage").text(res.msg);
                $("#showMessage").css("color", "red");
                $('#data-table tbody').html('<tr style"text-align: center; padding: 20px;"><td colspan="9">今日暂无人员刷卡记录</td></tr>');
                return;
            }

            let html = '';
            $.each(res.data, function(index, item) {
                html += '<tr style="font-weight: bold;font-size:20px;">' +
                        '<td>' + item.user_name + '</td>' +
                        '<td>' + item.pid + '</td>' +
                        '<td>' + item.area + '</td>' +
                        '<td style="color:'+item.hat.color+'">' + item.hat.wash_time + '</td>' +
                        '<td style="color:'+item.hat.color+'">' + item.hat.info + '</td>' +
                        '<td style="color:'+item.cloth.color+'">' + item.cloth.wash_time + '</td>' +
                        '<td style="color:'+item.cloth.color+'">' + item.cloth.info + '</td>' +
                        '<td style="color:'+item.pants.color+'">' + item.pants.wash_time + '</td>' +
                        '<td style="color:'+item.pants.color+'">' + item.pants.info + '</td>' +
                        '</tr>';
            });
            $('#data-table tbody').html(html);
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
