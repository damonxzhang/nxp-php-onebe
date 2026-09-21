<?php

use yii\helpers\ArrayHelper;
use backend\models\FccfpList;

?>
<div class = "x-body layui-anim layui-anim-up">
    <form class = "layui-form">
        <div class="layui-row">
            <div class="layui-col-sm6">
                <div class = "layui-form-item">
                    <label class = "layui-form-label">批号:</label>
                    <div class = "layui-input-inline">
                        <input type="text" value="<?= $model['containername'] ?>"  autocomplete="off" class="layui-input" readonly="readonly">
                    </div>
                </div>
                <div class = "layui-form-item">
                    <label class = "layui-form-label">工序:</label>
                    <div class = "layui-input-inline">
                        <input type="text" value="<?= $model['nowspecname'] ?>"  autocomplete="off" class="layui-input" readonly="readonly">
                    </div>
                </div>
                <div class = "layui-form-item">
                    <label class = "layui-form-label">出炉时间:</label>
                    <div class = "layui-input-inline">
                        <input type="text" value="<?= $model['nldpmc_trackout'] ?>"  autocomplete="off" class="layui-input" readonly="readonly">
                    </div>
                </div>
                <div class = "layui-form-item">
                    <label class = "layui-form-label">Marking 时间上限:</label>
                    <div class = "layui-input-inline">
                        <input type="text" value="40hrs"  autocomplete="off" class="layui-input" readonly="readonly">
                    </div>
                </div>
                <div class = "layui-form-item">
                    <label class = "layui-form-label">Marking 剩余时间:</label>
                    <div class = "layui-input-inline">
                        <input type="text" value="<?= $model['fcmark_clean_date_left'] ?>"  autocomplete="off" class="layui-input" readonly="readonly">
                    </div>
                </div>
                <div class = "layui-form-item">
                    <label class = "layui-form-label">Marking 开始时间:</label>
                    <div class = "layui-input-inline">
                        <input type="text" value="<?= $model['fcmark_clean_start_time'] ?>"  autocomplete="off" class="layui-input" readonly="readonly">
                    </div>
                </div>
                <div class = "layui-form-item">
                    <label class = "layui-form-label">Marking 结束时间:</label>
                    <div class = "layui-input-inline">
                        <input type="text" value="<?= $model['fcmark_clean_end_time'] ?>"  autocomplete="off" class="layui-input" readonly="readonly">
                    </div>
                </div>
                <div class = "layui-form-item">
                    <label class = "layui-form-label">Marking 已用时间:</label>
                    <div class = "layui-input-inline">
                        <input type="text" value="<?= $model['fcmark_clean_use_date'] ?>"  autocomplete="off" class="layui-input" readonly="readonly">
                    </div>
                </div>
                <div class = "layui-form-item">
                    <label class = "layui-form-label">Marking 过期时间:</label>
                    <div class = "layui-input-inline">
                        <input type="text" value="<?= $model['fcmark_clean_expiration_date'] ?>"  autocomplete="off" class="layui-input" readonly="readonly">
                    </div>
                </div>
                <div class = "layui-form-item">
                    <label class = "layui-form-label">FPF_Reflow 时间上限:</label>
                    <div class = "layui-input-inline">
                        <input type="text" value="40hrs"  autocomplete="off" class="layui-input" readonly="readonly">
                    </div>
                </div>
                <div class = "layui-form-item">
                    <label class = "layui-form-label">FPF_Reflow 剩余时间:</label>
                    <div class = "layui-input-inline">
                        <input type="text" value="<?= $model['fpfreflow_date_left'] ?>"  autocomplete="off" class="layui-input" readonly="readonly">
                    </div>
                </div>
                <div class = "layui-form-item">
                    <label class = "layui-form-label">FPF_Reflow 开始时间:</label>
                    <div class = "layui-input-inline">
                        <input type="text" value="<?= $model['fpfreflow_start_time'] ?>"  autocomplete="off" class="layui-input" readonly="readonly">
                    </div>
                </div>
                <div class = "layui-form-item">
                    <label class = "layui-form-label">FPF_Reflow 结束时间:</label>
                    <div class = "layui-input-inline">
                        <input type="text" value="<?= $model['fpfreflow_end_time'] ?>"  autocomplete="off" class="layui-input" readonly="readonly">
                    </div>
                </div>
                <div class = "layui-form-item">
                    <label class = "layui-form-label">FPF_Reflow 已用时间:</label>
                    <div class = "layui-input-inline">
                        <input type="text" value="<?= $model['fpfreflow_use_date'] ?>"  autocomplete="off" class="layui-input" readonly="readonly">
                    </div>
                </div>
                <div class = "layui-form-item">
                    <label class = "layui-form-label">FPF_Reflow 过期时间:</label>
                    <div class = "layui-input-inline">
                        <input type="text" value="<?= $model['fpfreflow_expiration_date'] ?>"  autocomplete="off" class="layui-input" readonly="readonly">
                    </div>
                </div>
            </div>
            <div class="layui-col-sm6">  
                <div class = "layui-form-item">
                    <label class = "layui-form-label">FPF_Clean 时间上限:</label>
                    <div class = "layui-input-inline">
                        <input type="text" value="8hrs"  autocomplete="off" class="layui-input" readonly="readonly">
                    </div>
                </div>
                <div class = "layui-form-item">
                    <label class = "layui-form-label">FPF_Clean 剩余时间:</label>
                    <div class = "layui-input-inline">
                        <input type="text" value="<?= $model['fpfclean_date_left'] ?>"  autocomplete="off" class="layui-input" readonly="readonly">
                    </div>
                </div>
                <div class = "layui-form-item">
                    <label class = "layui-form-label">FPF_Clean 开始时间:</label>
                    <div class = "layui-input-inline">
                        <input type="text" value="<?= $model['fpfclean_start_time'] ?>"  autocomplete="off" class="layui-input" readonly="readonly">
                    </div>
                </div>
                <div class = "layui-form-item">
                    <label class = "layui-form-label">FPF_Clean 结束时间:</label>
                    <div class = "layui-input-inline">
                        <input type="text" value="<?= $model['fpfclean_end_time'] ?>"  autocomplete="off" class="layui-input" readonly="readonly">
                    </div>
                </div>
                <div class = "layui-form-item">
                    <label class = "layui-form-label">FPF_Clean 已用时间:</label>
                    <div class = "layui-input-inline">
                        <input type="text" value="<?= $model['fpfclean_use_date'] ?>"  autocomplete="off" class="layui-input" readonly="readonly">
                    </div>
                </div>
                <div class = "layui-form-item">
                    <label class = "layui-form-label">FPF_Clean 过期时间:</label>
                    <div class = "layui-input-inline">
                        <input type="text" value="<?= $model['fpfclean_expiration_date'] ?>"  autocomplete="off" class="layui-input" readonly="readonly">
                    </div>
                </div>  
                <div class = "layui-form-item">
                    <label class = "layui-form-label">	BR 时间上限:</label>
                    <div class = "layui-input-inline">
                        <input type="text" value="12hrs"  autocomplete="off" class="layui-input" readonly="readonly">
                    </div>
                </div>
                <div class = "layui-form-item">
                    <label class = "layui-form-label">	BR 剩余时间:</label>
                    <div class = "layui-input-inline">
                        <input type="text" value="<?= $model['br_date_left'] ?>"  autocomplete="off" class="layui-input" readonly="readonly">
                    </div>
                </div>
                <div class = "layui-form-item">
                    <label class = "layui-form-label">	BR 开始时间:</label>
                    <div class = "layui-input-inline">
                        <input type="text" value="<?= $model['br_start_time'] ?>"  autocomplete="off" class="layui-input" readonly="readonly">
                    </div>
                </div>
                <div class = "layui-form-item">
                    <label class = "layui-form-label">	BR 结束时间:</label>
                    <div class = "layui-input-inline">
                        <input type="text" value="<?= $model['br_end_time'] ?>"  autocomplete="off" class="layui-input" readonly="readonly">
                    </div>
                </div>
                <div class = "layui-form-item">
                    <label class = "layui-form-label">	BR 已用时间:</label>
                    <div class = "layui-input-inline">
                        <input type="text" value="<?= $model['br_use_date'] ?>"  autocomplete="off" class="layui-input" readonly="readonly">
                    </div>
                </div>
                <div class = "layui-form-item">
                    <label class = "layui-form-label">	BR 过期时间:</label>
                    <div class = "layui-input-inline">
                        <input type="text" value="<?= $model['br_expiration_date'] ?>"  autocomplete="off" class="layui-input" readonly="readonly">
                    </div>
                </div>  
                <div class = "layui-form-item">
                    <label class = "layui-form-label">Flux_Clean 时间上限:</label>
                    <div class = "layui-input-inline">
                        <input type="text" value="60hrs"  autocomplete="off" class="layui-input" readonly="readonly">
                    </div>
                </div>
                <div class = "layui-form-item">
                    <label class = "layui-form-label">Flux_Clean 剩余时间:</label>
                    <div class = "layui-input-inline">
                        <input type="text" value="<?= $model['fluxclean_date_left'] ?>"  autocomplete="off" class="layui-input" readonly="readonly">
                    </div>
                </div>
                <div class = "layui-form-item">
                    <label class = "layui-form-label">Flux_Clean 开始时间:</label>
                    <div class = "layui-input-inline">
                        <input type="text" value="<?= $model['fluxclean_start_time'] ?>"  autocomplete="off" class="layui-input" readonly="readonly">
                    </div>
                </div>
                <div class = "layui-form-item">
                    <label class = "layui-form-label">Flux_Clean 结束时间:</label>
                    <div class = "layui-input-inline">
                        <input type="text" value="<?= $model['fluxclean_end_time'] ?>"  autocomplete="off" class="layui-input" readonly="readonly">
                    </div>
                </div>
                <div class = "layui-form-item">
                    <label class = "layui-form-label">Flux_Clean 已用时间:</label>
                    <div class = "layui-input-inline">
                        <input type="text" value="<?= $model['fluxclean_use_date'] ?>"  autocomplete="off" class="layui-input" readonly="readonly">
                    </div>
                </div>
                <div class = "layui-form-item">
                    <label class = "layui-form-label">Flux_Clean 过期时间:</label>
                    <div class = "layui-input-inline">
                        <input type="text" value="<?= $model['fluxclean_expiration_date'] ?>"  autocomplete="off" class="layui-input" readonly="readonly">
                    </div>
                </div>  

                <div class = "layui-form-item">
                    <label class = "layui-form-label">状态:</label>
                    <div class = "layui-input-inline">
                        <input type="text" value="<?= ArrayHelper::getValue(FccfpList::$statusList, $model['status'],'') ?>"  autocomplete="off" class="layui-input" readonly="readonly">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
