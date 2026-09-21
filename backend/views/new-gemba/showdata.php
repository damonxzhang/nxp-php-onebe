<div class="x-nav">
      <span class="layui-breadcrumb">
        <a href="javascript:;">首页</a>
        <a href="javascript:;">NXP WAY</a>
        <a href="javascript:;">
          <cite>关键词展示</cite>
        </a>
      </span>
</div>
<div class="x-body">
    <table class="layui-table">
        <thead>
        <tr>
            <th>#</th>
            <th>分词</th>
            <th>数量</th>
        </thead>
        <tbody>
        <?php foreach ($data as $key => $list): ?>
            <tr>
                <td><?= $key+1 ?></td>
                <td><?= $list['keys'] ?></td>
                <td><?= $list['number'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>