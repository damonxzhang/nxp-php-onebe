
<html>
<head>
    <title>jQuery表格轮播滚动功能</title>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="30">

    <link rel="stylesheet" href="https://www.jq22.com/jquery/bootstrap-3.3.4.css">
<!--    <script src="https://www.jq22.com/jquery/jquery-1.10.2.js"></script>-->
    <style>
        thead{
            margin-bottom: 15px;
            height: 70px !important;
            background-color: #009688 !important;
            color: #fff;
            font-size: 30px;
            font-weight: 800;
            z-index: 999;
        }
        #body td{
            font-size: 20px;
            font-weight: 700;
        }

    </style>
</head>
<script>
    function change() {
        var body=$("#body");
        body.append(body.children().first());
    }
    //停止滚动
    // $(document).ready(function() {
    //     $("#roll").mouseenter(function () {
    //         clearInterval(MyMar1)
    //     });
    //     $("#roll").mouseleave(function () {
    //          MyMar1 = setInterval(change, 1000);
    //     });
    // });
    // var MyMar1=setInterval(change,1000);
    var timeOut = 1000 * 60 * 5;//没五分钟刷新一次
    setTimeout(function(){  //使用  setTimeout（）方法设定定时2000毫秒
        window.location.reload();//页面刷新
    },timeOut);
    console.log(timeOut)
</script>
<body>

<!--        <div style="height: 300px;overflow: hidden" id="roll">-->
        <div style=" overflow: hidden" id="roll">
        <table class="table table-bordered" style="text-align: center">
            <thead>
                 <tr style="">
                     <td>发布人</td>
                     <td>交接人</td>
                     <td>标题</td>
                 </tr>
            </thead>

            <tbody id="body">
            <?php if(!empty($logList)):?>
                <?php foreach ($logList as $value):?>
                    <tr>
                        <td width="10%" contenteditable="true"><?= $value['aname']?></td>
                        <td width="10%" contenteditable="true"><?= $value['bname']?></td>
                        <td align="left" width="80%" contenteditable="true"><?= $value['title']?></td>
                    </tr>
                <?php endforeach;?>
            <?php endif;?>
            </tbody>
        </table>
        </div>
</body>
</html>
