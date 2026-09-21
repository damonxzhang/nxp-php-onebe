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
<div>目前仍有下面人员未完成送洗静电服，请督促完成或填写未送洗原因。</div>
<div>
    <table class="layui-table">
        <thead>
            <th>姓名</th>
            <th>时间(送洗时间)</th>
            <th>完成状态</th>
            <th>未送洗原因</th>
        </thead>
        <tbody>
        <?php foreach ($data as $list): ?>
            <tr>
                <td><?= $list['username'] ?></td>
                <td><?= $list['invest_time'] ?></td>
                <td><?= $list['status'] ?></td>
                <td><?= $list['common'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>