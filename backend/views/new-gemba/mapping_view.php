<?php
use yii\helpers\Html;
use backend\models\NewGembaData;
use backend\models\UserInfo;
use yii\helpers\ArrayHelper;

$this->registerCssFile('@css/ir.css');
?>
<style>
    .auto-input{
        display: none;
    }
</style>

<?php if($model->steps >= NewGembaData::CREATE_STEPS){ ?>
<form class="layui-form">
    <table class="layui-table">
        <tr style="width: 100%;">
            <th rowspan="7" class="leftTitle">创<br/>建<br/>人<br/>填<br/>写</th>
            <td colspan="7" class="noborderRight">
                <label class="layui-form-label leftLable">主题：</label>
                <div class="layui-input-block rightbox">
                    <input class="layui-input" value="<?=$model->title?>">
                </div>
            </td>

            <td colspan="5" class="noborderLeft">
                <label class="layui-form-label leftLable01">填表日期：</label>
                <div class="layui-input-block rightbox01">
                    <input type="text"  disabled class="layui-input" value="<?= !empty($model->create_time) ? date('Y-m-d',$model->create_time) : date('Y-m-d' )?>" >
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="noborder">
                <label class="layui-form-label" style="width: 120px;">Gemba类别：</label>
            </td>
            <td colspan="9" class="noborder">
                <?php foreach (\backend\models\NewGemba::$typeList as  $key => $val): ?>
                    <input type="radio"   <?= $model->type == $key  ? 'checked': '';?> disabled value="<?=$key?>" title="<?= $val ?>">
                <?php endforeach;?>
            </td>
        </tr>


        <tr>
            <td colspan="12">

                <div >Gemba问题描述：</div>
                <div class="">
                    <textarea style="width: 100%;" disabled rows="7"><?=$model->content?></textarea>

                </div>
                <div class="layui-upload">
                    
                    <blockquote class="layui-elem-quote layui-quote-nm" style="margin-top: 10px;">
                        预览图：
                        <div class="layui-upload-list" id="image_show">
                            <?php

                            if(!empty($model->files)):
                                $model->files = json_decode($model->files,1);
                                foreach ($model->files as  $val):
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
            </td>

        </tr>

        <tr>
            <td colspan="1" class="boderstyle ">
                <label >是否自主完成：</label>
            </td>
            <td class="boderstyle">
                <div >
                    <?= Html::dropDownList('auto_complete',$model->auto_complete, NewGembaData::$autoCompleteList,['disabled' => ''])?>
                </div>
            </td>
            <td class="boderstyle ">
                <label >是否有标准：</label>
            </td>
            <td class="boderstyle">
                <div >
                    <?= Html::dropDownList('auto_complete',$model->norm, NewGembaData::$normList,['disabled' => ''])?>
                </div>
            </td>
            <td class="boderstyle">
                <label >选择区域：</label>
            </td>
            <td class="boderstyle">
                <div >
                    <?= Html::dropDownList('area',$model->area, UserInfo::$areaArr ,['disabled' => ''])?>
                </div>
            </td>
            <td class="boderstyle" colspan="5"></td>
        </tr>

        <tr class="auto-input" style="<?= $model->auto_complete == NewGembaData::AUTO_COMPLETE_YES?'display:inline;' :''  ?>" >
            <td class="boderstyle ">
                <label >是否完成：</label>
            </td>
            <td class="boderstyle">
                <div >
                    <?= Html::dropDownList('auto_complete',$model->confirm, NewGembaData::$autoCompleteList,['disabled' => ''])?>
                </div>
            </td>
            <td class="boderstyle ">
                <label >完成时间：</label>
            </td>
            <td class="boderstyle">
                <div >
                    <input class="layui-input" disabled value="<?= date('Y-m-d',$model->confirm_time)?>">
                </div>
            </td>
            <?php if(!empty($model->leader_status)):?>
            <td class="boderstyle ">
                <label >主管审批结果：</label>
            </td>
            <td colspan="8" class="boderstyle ">
                <div >
                    <input class="layui-input" disabled value="<?= !empty(NewGembaData::$leaderStatusList[$model->leader_status])?NewGembaData::$leaderStatusList[$model->leader_status]:'' ?>">
                </div>
            </td>
        <?php endif;?>

    </tr>

        <tr>
            <td colspan="2" class="noborderRight noborderLeft">
                <label class="layui-form-label leftLable">周别：</label>
                <div class="layui-input-block rightbox">
                    <input class="layui-input" disabled value="<?=$model->week?>">
                </div>
            </td>
            <td colspan="2" class="noborderRight noborderLeft">
                <label class="layui-form-label leftLable">月份：</label>
                <div class="layui-input-block rightbox">
                    <input class="layui-input" disabled value="<?=$model->month?>">

                </div>
            </td>
            <td class="boderstyle noborderRight noborderLeft">
                <label class="layui-form-label leftLable">建议实施人：</label>
            </td>
            <td colspan="1" class="boderstyle noborderRight noborderLeft">
                <?= \backend\widgets\SearchUser::widget([
                    'name' => 'participant',
                    'url' => \yii\helpers\Url::to('/user-info/search-by-name'),
                    'value' => $model->participant,
                    'options' => [
                        'multiple' => true,
                        'class' => 'participant',
                        'disabled' => true
                    ],
                    'pluginOptions' => [
                        'closeOnSelect' => false,
                    ]
                ])?>
            </td>
            <td class="boderstyle" colspan="5"></td>

        </tr>

        <tr>
            <td colspan="2" class="noborderRight">
                <label class="layui-form-label leftLable">姓名：</label>
                <div class="layui-input-block rightbox">
                    <input type="text" name="" disabled required lay-verify="required" value="<?=(new UserInfo())->getUserName($model->create_user)?>" autocomplete="off" class="layui-input">

                </div>
            </td>
            <td colspan="2" class="noborderRight noborderLeft">
                <label class="layui-form-label leftLable">CID：</label>
                <div class="layui-input-block rightbox">
                    <input type="text" name="" disabled required lay-verify="required"  value="<?=$userInfo['badge_code']?>"  autocomplete="off" class="layui-input">
                </div>
            </td>

            <td class="boderstyle">
                <label class="layui-form-label leftLable">gemba小组：</label>
            </td>
            <td  class="boderstyle">
                <input type="text" name="" disabled required lay-verify="required"  value="<?=\backend\models\Team::getTeamName($model->team) ?>"  autocomplete="off" class="layui-input">
            </td>
            <td colspan="2" class="noborderRight noborderLeft">
                <label class="layui-form-label leftLable">班次：</label>
                <div class="layui-input-block rightbox">
                    <input type="text" name="" disabled required lay-verify="required"  value="<?= ArrayHelper::getValue(\backend\models\UserInfo::$shiftArr, $model->shift,'') ?>"  autocomplete="off"
                           class="layui-input">
                </div>
            </td>
            <td colspan="2" class="noborderLeft">
                <label class="layui-form-label leftLable">部门：</label>
                <div class="layui-input-block rightbox">
                    <input type="text" name="" disabled required lay-verify="required"  value="<?= ArrayHelper::getValue(\backend\models\UserInfo::$sectionArr, $model->section,'') ?>"  autocomplete="off" class="layui-input">
                </div>
            </td>
        </tr>
    </table>

</form>

<?php
}
if($model->steps >= NewGembaData::ASSESS_STEPS  && !empty($model->assess_user)){
?>
<form class="layui-form">

        <table class="layui-table" width="100%">
            <tr style="width: 100%;">
                <th rowspan="6" class="leftTitle">审<br/>核<br/>人<br/>填<br/>写</th>
            </tr>
            <tr>
                <td colspan="12">
                    <div >审核人意见：</div>
                    <div class="">

                        <textarea rows="7" disabled style="width: 100%;"><?=$model->assess_advice?></textarea>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="1" class="boderstyle ">
                    <label >是否接受：</label>
                </td>
                <td class="boderstyle">
                    <div >
                        <?= Html::dropDownList('',$model->assess_status,NewGembaData::$selectList,['disabled' => '']); ?>
                    </div>
                </td>
                <td class="boderstyle ">
                    <label >审核日期：</label>
                </td>
                <td class="boderstyle">
                    <div >
                        <input class="layui-input" disabled value="<?=date('Y-m-d',$model->assess_time)?>">
                    </div>
                </td>

                <td class="boderstyle noborderRight noborderLeft">
                    <label class="layui-form-label leftLable">实施人：</label>
                </td>
                <td colspan="1" class="boderstyle noborderRight noborderLeft">
                    <?= \backend\widgets\SearchUser::widget([
                        'name' => Html::getInputName($model,'work_user'),
                        'url' => \yii\helpers\Url::to('/user-info/search-by-name'),
                        'value' => $model->work_user,
                        'options' => [
                            'disabled' => true
                        ]
                    ])?>
                </td>

                <td class="boderstyle">
                    <label class="layui-form-label leftLable">审核人签字：</label>
                </td>
                <td  class="boderstyle">
                    <input type="text" name="" disabled required lay-verify="required"  value="<?=(new \backend\models\UserInfo())->getUserName($model->assess_user)?>"  autocomplete="off" class="layui-input">
                </td>

            </tr>
        </table>

    </form>

<?php
}
if($model->steps >= NewGembaData::WORK_STEPS && !empty($model->work_user)){
?>

<form class="layui-form">
    <table class="layui-table" width="100%">
        <tr style="width: 100%;">
            <th rowspan="6" class="leftTitle">实<br/>施<br/>人<br/>填<br/>写</th>

        </tr>
        <tr>
            <td colspan="12">
                <div >实施人意见：</div>
                <div class="">
                    <?= Html::textarea('',$model->work_advice,['rows' => 7 ,'style' => 'width: 100%;','disabled' => '']) ?>
                </div>
            </td>
        </tr>
        <?php if(!empty($model->work_two_advice )):?>
            <tr>
                <td colspan="12">
                    <div >再次实施意见：</div>
                    <div class="">
                        <?= Html::textarea('',$model->work_two_advice,['rows' => 7 ,'style' => 'width: 100%;','disabled' => '']) ?>
                    </div>
                </td>
            </tr>
        <?php endif;?>
        <tr>
            <td colspan="1" class="boderstyle ">
                <label >是否接受：</label>
            </td>
            <td class="boderstyle">
                <div >
                    <?=Html::dropDownList('',$model->is_accept,NewGembaData::$selectList,['disabled' => ''])?>
                </div>
            </td>
            <td colspan="1" class="boderstyle ">
                <label >完成状态：</label>
            </td>
            <td class="boderstyle">
                <div >
                    <?=Html::dropDownList('',$model->work_status,NewGembaData::$implementStatus,['disabled' => ''])?>
                </div>
            </td>

            <td class="boderstyle ">
                <label >预计完成日期：</label>
            </td>
            <td class="boderstyle">
                <div >
                    <input type="text" disabled class="layui-input" name="<?=Html::getInputName($model,'end_time')?>" value="<?= !empty($model->end_time)? date('Y-m-d',$model->end_time):''?>">
                </div>
            </td>


            <td class="boderstyle">
                <label class="layui-form-label leftLable">实施人签字：</label>
            </td>
            <td  class="boderstyle">
                <input type="text" name="" disabled required lay-verify="required"  value="<?=(new \backend\models\UserInfo())->getUserName($model->work_user)?>"  autocomplete="off" class="layui-input">
            </td>

        </tr>
    </table>

</form>

<?php } ?>
<?php if($model->steps >= NewGembaData::COMPLETE_STEPS && !empty($model->confirm_user) && !empty($model->work_user)){?>
<form class="layui-form">
    <table class="layui-table" width="100%">
        <tr style="width: 100%;">
            <th rowspan="6" class="leftTitle">创<br/>建<br/>人<br/>填<br/>写</th>
        </tr>
        <tr>
            <td colspan="12" class="boderstyle">
                <label class="layui-form-label leftLable01">是否有效：</label>
                <div class="layui-input-block rightbox01 boderstyle" style="width: 300px;">
                    <?=Html::dropDownList('',$model->confirm,$model::$draftList,['disabled' => ''])?>

                </div>
            </td>
        </tr>
        <tr>
            <td colspan="12">
                <div >建议人意见：</div>
                <div class="">
                    <?= Html::textarea('',$model->confirm_content,['rows' => 7 ,'style' => 'width: 100%;','disabled' => '']) ?>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="12">
                <label class="layui-form-label leftLable01">建议人：</label>
                <div class="layui-input-block rightbox01">
                    <input type="text" disabled value="<?=(new \backend\models\UserInfo())->getUserName($model->confirm_user)?>"  placeholder=""  autocomplete="off" class="layui-input">
                </div>
            </td>
        </tr>
    </table>
</form>


<?php } ?>
<div class="layui-form-item"  style="margin-top: 50px;">
    <label for="" class="layui-form-label">
    </label>
    <?= $model->getButtonBySteps(); ?>
</div>
