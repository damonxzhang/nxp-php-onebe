<?php
use yii\helpers\Url;
?>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">
        <iframe src="<?= $path ?>" width="100%" height="400"></iframe>
    </form>
</div>
<?php $this->beginBlock('footer') ?>
<script>
    $("#print-img").click(function(){
       window.print();
    });
</script>
<?php $this->endBlock() ?>
