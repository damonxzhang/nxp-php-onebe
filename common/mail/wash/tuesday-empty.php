<html>
<style>
    body{
    }
    table{
        width: 50%;
        text-align: center;
    }
    th{
        background: #D2D2D2;
    }
    td,th{
        border: 1px solid #F0E5F1;
        height: 30px;
    }
</style>
<body>
<h1>Hi :</h1>
<div></div>
<div>目前仍有下面人员未取走清洗后的静电服，请协助完成并清空洗衣柜。</div>
<div>
    <table class="layui-table">
        <thead>
            <th>姓名</th>
            <th>时间(取走时间)</th>
            <th>完成状态</th>
        </thead>
        <tbody>
        <?php foreach ($data as $list): ?>
            <tr>
                <td><?= $list['username'] ?></td>
                <td><?= $list['receive_time'] ?></td>
                <td><?= $list['status'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>