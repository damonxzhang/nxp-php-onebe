<?php
use backend\models\UserInfo;
/* @var $this yii\web\View */
/* @var $model backend\models\Train */
/* @var $form yii\widgets\ActiveForm */
?>

    <div class="reain-ppt-form">
        主管您好！您班里有以下培训课程，请安排相关人员在结束时间内完成培训。
        <div class="form-group field-train-end_time required">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label"><label class="control-label" for="train-end_time">课程名称</label>:</label>

                    <?= $model->coursesInfo->name?>
                <div class="layui-col-md4" style="color:red"><div class="help-block"></div></div>
            </div>
        </div>
        <div class="form-group field-train-end_time required">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label"><label class="control-label" for="train-end_time">学员</label>:</label>

                    <?php
                        $userNames = UserInfo::getUserNameByIdArray( $model->user_ids);
                        echo implode(',',$userNames);
                    ?>
                <div class="layui-col-md4" style="color:red"><div class="help-block"></div></div>
            </div>
        </div>
        <div class="form-group field-train-end_time required">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label"><label class="control-label" for="train-end_time">主管</label>:</label>
                <?php
                    $directorNames = UserInfo::getUserNameByIdArray( $model->director);
                    echo implode(',',$directorNames);
                ?>
                <div class="layui-col-md4" style="color:red"><div class="help-block"></div></div>
            </div>
        </div>
        <div class="form-group field-train-end_time required">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label"><label class="control-label" for="train-end_time">开始时间</label>:</label>
                    <?php
                        $start_time =  date('Y-m-d',$model->start_time);
                        echo $start_time;
                    ?>
                <div class="layui-col-md4" style="color:red"><div class="help-block"></div></div>
            </div>
        </div>
        <div class="form-group field-train-end_time required">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label"><label class="control-label" for="train-end_time">结束时间</label>:</label>

                <?=date('Y-m-d',strtotime("$start_time + $model->end_time day"))  ?>
                <div class="layui-col-md4" style="color:red"><div class="help-block"></div></div>
            </div>
        </div>
        <div class="form-group field-train-end_time required">
            <div class="layui-form-item">
                <label for="tel" class="layui-form-label"><label class="control-label" for="train-end_time">内容</label>:</label>
                <?=$model->content?>
                <div class="layui-col-md4" style="color:red"><div class="help-block"></div></div>
            </div>
        </div>


    </div>
