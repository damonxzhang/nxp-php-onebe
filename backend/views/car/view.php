<?php

use backend\models\Car;
use backend\models\UserInfo;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use backend\models\CarJudge;
use backend\models\CarOperatorJudge;

$this->registerCssFile('@css/ir.css');
?>
<div class="x-body layui-anim layui-anim-up">
    <form class="layui-form">
        <div class="layui-row">
            <div class="layui-col-sm4">
               
                <div class="layui-form-item">
                    <label class="layui-form-label">
                    批号:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text"  value="<?php echo $carModel['batch_number']; ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">
                    操作站:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text"  value="<?php echo $carModel['station']; ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>
            <div class="layui-col-sm4">
                <div class="layui-form-item">
                    <label class="layui-form-label">
                    日期:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text"  value="<?php echo $carModel['date']; ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">
                    器件型号:
                    </label>
                    <div class="layui-input-inline">
                       <input type="text"  value="<?php echo $carModel['device_model']; ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
               
            </div>
            <div class="layui-col-sm4">
                <div class="layui-form-item">
                    <label class="layui-form-label">
                    班别:
                    </label>
                    <div class="layui-input-inline">
                      <input type="text" value="<?= ArrayHelper::getValue(UserInfo::$shiftArr, $carModel['shift'], '') ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">
                    封装类型:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text"  value="<?php echo $carModel['package']; ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>
            <div class="layui-col-sm4">
                <div class="layui-form-item">
                    <label class="layui-form-label">
                    机器号:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text"  value="<?php echo $carModel['machine_number']; ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">
                    缺陷项目:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text" value="<?= ArrayHelper::getValue($dafectArr, $carModel['defective_items'], '') ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>
            <div class="layui-col-sm4">
                <div class="layui-form-item">
                    <label class="layui-form-label">
                    操作员:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text"  value="<?php echo $carModel['operator']; ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">
                    次品缺陷数量:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text" value="<?php echo $carModel['defective_number']; ?>"  readonly="readonly" autocomplete="off" class="layui-input" >
                    </div>
                </div>
              
            </div>
            <div class="layui-col-sm4">
                <div class="layui-form-item">
                    <label class="layui-form-label">
                    操作员姓名:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text"  value="<?php echo (new Car())->getOperatorChineseName($carModel['id']); ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">
                    检查员:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text"  value="<?php echo (new UserInfo())->getUserName($carModel['create_id']); ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>
            <div class="layui-col-sm4">
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">
                物料存放位置:
                </label>
                <div class="layui-input-block">
                    <input type="text"  value="<?php echo $carModel['storage_location']; ?>" readonly="readonly" autocomplete="off" class="layui-input" style="width: 800px;">
                </div>
            </div>
            <div class="layui-form-item layui-form-text">
                <label for="desc" class="layui-form-label">
                    缺陷项目描述:
                </label>
                <div class="layui-input-block">
                    <textarea lay-verify="required" class="layui-textarea" readonly="readonly" style="width: 800px;height: 100px;"><?php echo $carModel['description']; ?></textarea>
                </div>
            </div>
            <div class="layui-form-item layui-form-text">
                <label for="desc" class="layui-form-label">
                    上传图片:
                </label>
                <div class="layui-input-block">
                    <div class="layui-upload">
                    <blockquote class="layui-elem-quote layui-quote-nm" style="width: 800px;margin-top: 10px;">
                            预览图：
                            <div class="layui-upload-list" id="image_show">
                                <?php
                                if(!empty($carModel->files_mapping)):
                                    foreach ($carModel->files_mapping as  $val):
                                        if(empty($val)) continue;
                                        ?>
                                        <span class="itemimg">
                                            <img src="<?=$val?>" class="layui-upload-img show-img">
                                            <i class="delicon layui-icon layui-icon-close-fill image-span"></i>
                                        </span>
                                    <?php
                                    endforeach;
                                endif;
                                ?>
                            </div>
                        </blockquote>
                    </div>
                </div>
            </div>
        
            <div class="layui-form-item layui-form-text">
                <label for="desc" class="layui-form-label">
                    历史处理方法:
                </label>
                <div class="layui-input-block">
                    <table class="layui-table" style="width: 800px;">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>处理方法</th>
                            <th>角色(产品工程师/带班)</th>
                            <th>姓名</th>
                            <th>反馈</th>
                            <th>提交时间</th>
                        </thead>
                        <tbody>
                        <?php foreach ($carJudgeModel as $key=> $list): ?>
                            <tr>
                                <td><?= $key+1; ?></td>
                                <td><?= $list['role'] == 2 ? '处理方法' : '反馈结果'; ?></td>
                                <td><?= $list['role'] == 2 ? '带班' : '产品工程师'; ?></td>
                                <td><?= (new UserInfo())->getUserName($list['user_id']); ?></td>
                                <td><?= $list['method'] ?></td>
                                <td><?= $list['create_time']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label"></label>
                    <div class="layui-input-inline" style="font-size:20px;font-weight: bold;">
                    产品工程师判定
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">判定结果:</label>
                    <div class="layui-input-inline">
                        <input type="text" value="<?= ArrayHelper::getValue(CarJudge::$resultArr, $carModel['result'], '') ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">是否记操作员责任:</label>
                    <div class="layui-input-inline">
                        <input type="text" value="<?= ArrayHelper::getValue(CarJudge::$responsibilityArr, $carModel['responsibility'], '') ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">DPR权重(缺陷项目):</label>
                    <div class="layui-input-inline">
                        <input type="text" value="<?= ArrayHelper::getValue(CarJudge::$dprArr, $carModel['dpr'], '') ?>" readonly="readonly" autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"></label>
                <div class="layui-input-inline" style="font-size:20px;font-weight: bold;">
                责任认定
                </div>
            </div>
            <div class="layui-col-sm4">
                <div class="layui-form-item">
                    <label class="layui-form-label">
                    TRACE NO:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text" value="<?php echo $carModel['trace_no']; ?>" readonly="readonly" required lay-verify="required" autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>
            <div class="layui-col-sm4">
                <div class="layui-form-item">
                    <label class="layui-form-label">
                    日期:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text"  value="<?php echo $carModel['date']; ?>"  readonly="readonly" lay-verify="required" autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>
            <div class="layui-col-sm4">
                <div class="layui-form-item">
                    <label class="layui-form-label">
                    领取人:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text" readonly="readonly"  value="<?php echo (new Car())->getOperatorChineseName($carModel['id']); ?>" autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>
            <div class="layui-form-item layui-form-text">
                <label for="desc" class="layui-form-label">
                缺陷原因:
                </label>
                <div class="layui-input-block">
                    <textarea lay-verify="required" class="layui-textarea" readonly="readonly" style="width: 800px;height: 100px;"> <?php echo $carModel['reasons_defects']; ?> </textarea>
                </div>
            </div>
            <div class="layui-form-item layui-form-text">
                <label for="desc" class="layui-form-label">
                改正行动:
                </label>
                <div class="layui-input-block">
                    <textarea lay-verify="required" class="layui-textarea" readonly="readonly" style="width: 800px;height: 100px;"><?php echo $carModel['corrective_action']; ?></textarea>
                </div>
            </div>
            <div class="layui-form-item layui-form-text">
                <label for="desc" class="layui-form-label">
                    上传图片:
                </label>
                <div class="layui-input-block">
                    <div class="layui-upload">
                    <blockquote class="layui-elem-quote layui-quote-nm" style="width: 800px;margin-top: 10px;">
                            预览图：
                            <div class="layui-upload-list">
                                <?php
                                if(!empty($carModel->second_files_mapping)):
                                    foreach ($carModel->second_files_mapping as $val):
                                        if(empty($val)) continue;
                                        ?>
                                        <span class="itemimg">
                                            <img src="<?=$val?>" class="layui-upload-img show-img">
                                            <i class="delicon layui-icon layui-icon-close-fill image-span"></i>
                                        </span>
                                    <?php
                                    endforeach;
                                endif;
                                ?>
                            </div>
                        </blockquote>
                    </div>
                </div>
            </div>
            <div class="layui-col-sm12">
                <div class="layui-form-item">
                    <label class="layui-form-label">
                    带班确认人:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text" readonly="readonly"  value="<?php echo (new CarOperatorJudge())->getUserName($carModel->id,2); ?>" autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>
            <div class="layui-col-sm12">
                <div class="layui-form-item">
                    <label class="layui-form-label">
                    HighLevel确认人:
                    </label>
                    <div class="layui-input-inline">
                        <input type="text" readonly="readonly"  value="<?php echo (new CarOperatorJudge())->getUserName($carModel->id,3); ?>" autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>