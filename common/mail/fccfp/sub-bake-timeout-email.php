<html>
<style>
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
<h1>Hi All :</h1>
<div></div>
<div></div>
<div>以下物料BAKE结束已超出12h, 请尽快进行PLASMA:</div>
<div>
    <table class="layui-table">
        <tr class="th">
            <th>批号</th>
            <th>SUB_PBAKE TrackOut时间</th>
            <th>距离SUB_PBAKE TrackOut时间</th>
        </tr>
        <?php foreach ($data as $val): ?>
            <tr class="content">
                <td><?= $val['containername'] ?></td>
                <td><?= $val['subpbake_trackout'] ?></td>
                <td><?= $val['usetime'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<div>如有疑问,请联系BE带班处理.</div>
<div>Thanks</div>
</body>
</html>