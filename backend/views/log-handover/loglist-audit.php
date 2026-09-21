<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>生产日志</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .nav-buttons {
            display: flex;
            gap: 10px;
        }

        .nav-button {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .nav-button.primary {
            background-color: #8bc34a;
            color: white;
        }

        .nav-button.secondary {
            background-color: #4caf50;
            color: white;
        }

        .info-section {
            display: flex;
            justify-content: space-between;
            color: #666;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .info-label {
            color: #ff9800;
        }

        .title-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .title {
            font-size: 24px;
            color: #333;
            position: relative;
            padding-left: 20px;
        }

        .title::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 20px;
            background-color: #8bc34a;
            border-radius: 2px;
        }

        .create-button {
            padding: 8px 16px;
            background-color: #8bc34a;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background-color: #f8f9fa;
            padding: 12px;
            text-align: left;
            color: #333;
            font-weight: 500;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            color: #666;
        }

        .action-link {
            color: #2196f3;
            text-decoration: none;
            cursor: pointer;
        }

        .action-link:hover {
            text-decoration: underline;
        }

        .footer-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .footer-buttons-right {
            display: flex;
            gap: 10px;
        }

        .footer-button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
        }

        .footer-button.green {
            background-color: #8bc34a;
            color: white;
        }

        .footer-button.orange {
            background-color: #ff9800;
            color: white;
        }

        .footer-button.blue {
            background-color: #2196f3;
            color: white;
        }

        .footer-button:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        .ws-docs-anim > div{padding: 16px; transition: all .3s;}
        .ws-docs-anim > div .layui-anim{  
            width: 100px;
            height: 100px;
            line-height: 100px;
            margin: 50% auto 50px;
            text-align: center;
            cursor: pointer;
            color: #fff;
            border-radius: 50%;
            user-select: none;
            font-size: 18px;
        }
        .ws-docs-anim > div .layui-anim-material{
            background-color: #16b777;
        }
        .ws-docs-anim > div .layui-anim-appliances{
            background-color: #ffb800 ;
        }
        .ws-docs-anim > div .layui-anim-comment{
            background-color: #31bdec;
        }
        .ws-docs-anim > div > div{text-align: center; white-space: nowrap;}
        .layui-form-label-layer {
            position: relative;
            float: left;
            display: block;
            /* padding: 9px 15px; */
            width: 80px;
            font-weight: 400;
            line-height: 20px;
            text-align: right
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="info-section" style="margin: 0 auto;">
                <div class="info-item" style="margin-right: 30px;">
                    <span class="info-label"> From : </span>
                    <span><?= $userStr ?></span> 
                </div>
                <div class="info-item" style="margin-right: 30px;">
                    <span class="info-label"> Date : </span>
                    <span><?= $date ?></span>
                </div>
                <button type="button" id="printbutton"  style="display: inline-block;vertical-align: middle;height: 30px;;line-height: 30px;border: 1px solid transparent;padding: 0 18px;background-color: #16baaa;color: #fff;" onclick="printFunction()">下载/打印</button>
            </div>
        </div>
        <div style="margin-left:30%;">
            <div style="margin: 0 auto;">
                <span class="info-label"> 确认交接人 : </span>
                <span><?= $copulateStr ?></span> 
            </div>
        </div>
        <div class="title-section">
            <h1 class="title">生产日志</h1>
        </div>
        <div class="layui-table-body layui-table-main" style="height: 100%;">
            <table cellspacing="0" cellpadding="0" class="layui-table" style="table-layout: fixed;" lay-even>
                <thead>
                    <tr>
                        <th style="width: 10%;"> ID </th>
                        <th style="width: 90%;"> 日志内容描述 </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($dataProvider)): ?>
                        <?php foreach ($dataProvider as $key => $value): ?>
                            <tr >
                                <td><?= $key+1 ?></td>
                                <td><?= $value['content_audit'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr ><td colspan="3" style="font-size: 16px;color:red;text-align: center;">暂无交接数据</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
      function printFunction() {
        var button = document.getElementById('printbutton');
        button.style.display = "none";
        window.print(); 
      }
    </script>
</body>
</html>