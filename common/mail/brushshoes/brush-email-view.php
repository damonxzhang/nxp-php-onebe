<html>
<style>
    body {
    }

    table {
        width: 50%;
        text-align: center;
    }

    th {
        background: #D2D2D2;
    }

    td, th {
        border: 1px solid #F0E5F1;
        height: 30px;
    }
</style>
<body>
<h1>People who have not swiped their cards</h1>
<div>Hello <?= $userName ?></div>
<div>未洗鞋的员工名单如下</div>

<h3>进行中</h3>
<div>
    <table>
        <tr class="th">
            <th>中文名</th>
            <th>区域</th>
            <th>班别</th>
        </tr>
        <?php foreach ($params as $val): ?>
            <tr class="content">
                <td><?= $val['chinese_name'] ?></td>
                <td><?= $val['area'] ?></td>
                <td><?= $val['shift'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>

