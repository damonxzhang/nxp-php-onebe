<form class="layui-form" action="">
    <table class="layui-table">
        <tr style="width: 100%;">
            <th rowspan="4" class="leftTitle">建<br/>议<br/>人<br/>填<br/>写</th>
            <td colspan="8" class="noborderRight">
                <label class="layui-form-label leftLable">主题：</label>
                <div class="layui-input-block rightbox">
                    <input type="text" name="title" required lay-verify="required" autocomplete="off" class="layui-input">
                </div>
            </td>
            <td colspan="4" class="noborderLeft">
                <label class="layui-form-label leftLable01">填表日期：</label>
                <div class="layui-input-block rightbox01">
                    <input type="text" name="tpme" required lay-verify="required" autocomplete="off"
                           class="layui-input">
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="3" class="noborder">
                <label class="layui-form-label leftLable01 ">建议类别：</label>
            </td>
            <td colspan="9" class="noborder">
                <input type="checkbox" name="" title="EHS" lay-skin="primary">
                <input type="checkbox" name="" title="质量" lay-skin="primary">
                <input type="checkbox" name="" title="生产效率" lay-skin="primary">
                <input type="checkbox" name="" title="降低成本" lay-skin="primary">
                <input type="checkbox" name="" title="员工士气" lay-skin="primary">
            </td>
        </tr>
        <tr>
            <td colspan="12">
                <div id="">目前状况：</div>
                <div class="">
                    <textarea name="" rows="7" style="width: 100%;"></textarea>
                </div>
                <div id="">建议内容：（请尽量与相关人员讨论后填写）</div>
                <div class="">
                    <textarea name="" rows="7" style="width: 100%;"></textarea>
                </div>
                <div class="layui-upload">
                    <button type="button" class="layui-btn" id="test2">多图片上传</button>
                    <blockquote class="layui-elem-quote layui-quote-nm" style="margin-top: 10px;">
                        预览图：
                        <div class="layui-upload-list" id="demo2"></div>
                    </blockquote>
                </div>
            </td>

        </tr>
        <tr>
            <td colspan="3" class="noborderRight">
                <label class="layui-form-label leftLable">姓名：</label>
                <div class="layui-input-block rightbox">
                    <input type="text" name="name" required lay-verify="required" autocomplete="off"
                           class="layui-input">
                </div>
            </td>
            <td colspan="3" class="noborderRight noborderLeft">
                <label class="layui-form-label leftLable">CID：</label>
                <div class="layui-input-block rightbox">
                    <input type="text" name="cid" required lay-verify="required" autocomplete="off" class="layui-input">
                </div>
            </td>
            <td colspan="3" class="noborderRight noborderLeft">
                <label class="layui-form-label leftLable">班次：</label>
                <div class="layui-input-block rightbox">
                    <input type="text" name="myclass" required lay-verify="required" autocomplete="off"
                           class="layui-input">
                </div>
            </td>
            <td colspan="3" class="noborderLeft">
                <label class="layui-form-label leftLable">部门：</label>
                <div class="layui-input-block rightbox">
                    <input type="text" name="org" required lay-verify="required" autocomplete="off" class="layui-input">
                </div>
            </td>
        </tr>
    </table>
</form>
<!--主管意见-->
<form class="layui-form" action="">
    <table class="layui-table" width="100%">
        <tr style="width: 100%;">
            <th rowspan="2" class="leftTitle">主<br/>管<br/>意<br/>见</th>
            <td colspan="12">
                <label class="layui-form-label leftLable02">不及格IR评判标准：</label>
                <div class="layui-input-block rightbox02">
                    <select name="judge">
                        <option value="抱怨性质">抱怨性质</option>
                        <option value="重复性建议">重复性建议</option>
                        <option value="属于申请人本职工作，例如机器需要维修等或对团队成员的失误疏忽进行指正的">属于申请人本职工作，例如机器需要维修等或对团队成员的失误疏忽进行指正的</option>
                        <option value="仅指出不合理的地方，但没有相关改善建议">仅指出不合理的地方，但没有相关改善建议</option>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="4" class="noborderRight">
                <label class="layui-form-label leftLable">初评：</label>
                <div class="layui-input-block rightbox">
                    <input type="text" name="intern" required lay-verify="required" autocomplete="off"
                           class="layui-input">
                </div>
            </td>
            <td colspan="4" class="noborderLeft noborderRight">
                <label class="layui-form-label leftLable02">直接主管签字：</label>
                <div class="layui-input-block rightbox02">
                    <input type="text" name="signature" required lay-verify="required" autocomplete="off"
                           class="layui-input">
                </div>
            </td>
            <td colspan="4" class="noborderLeft">
                <label class="layui-form-label leftLable01">评估日期：</label>
                <div class="layui-input-block rightbox01">
                    <input type="text" name="valuationDate" required lay-verify="required" autocomplete="off"
                           class="layui-input">
                </div>
            </td>
        </tr>
    </table>
</form>
<!--评估人建议-->
<form class="layui-form" action="">
    <table class="layui-table" width="100%">
        <tr style="width: 100%;">
            <th rowspan="3" class="leftTitle">评<br/>估<br/>人<br/>建<br/>议</th>
            <td colspan="6" class="noborderRight">
                <label class="layui-form-label leftLable02">建议是否接受：</label>
                <div class="layui-input-block rightbox02">
                    <select name="acceptAdvice" lay-verify="required">
                        <option value=""></option>
                        <option value="接受">接受</option>
                        <option value="不接受">不接受</option>
                    </select>
                </div>
            </td>
            <td colspan="6" class="noborderLeft">
                <label class="layui-form-label leftLable01">标题搜索：</label>
                <div class="layui-input-block rightbox01">
                    <input type="text" name="searchName" required lay-verify="required" autocomplete="off"
                           class="layui-input searchInput">
                    <a href="javascript:void()" class="layui-btn searchBtn">跳转查看</a>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="12">
                <textarea name="assessmentContent" rows="7" style="width: 100%;"></textarea>
            </td>
        </tr>
        <tr>
            <td colspan="4" class="noborderRight">
                <label class="layui-form-label leftLable01">评估人签字</label>
                <div class="layui-input-block rightbox01">
                    <input type="text" name="assessmentName" required lay-verify="required" autocomplete="off"
                           class="layui-input">
                </div>
            </td>
            <td colspan="4" class="noborderLeft noborderRight">
                <label class="layui-form-label leftLable02">完成评估日期：</label>
                <div class="layui-input-block rightbox02">
                    <div class="layui-inline">
                        <input type="text" class="layui-input" required lay-verify="required" autocomplete="off"
                               id="assessmentTime">
                    </div>
                </div>
            </td>
            <td colspan="4" class="noborderLeft">
                <label class="layui-form-label leftLable02">需要支持部门<br/> /负责人：</label>
                <div class="layui-input-block rightbox02">
                    <select name="head" xm-select="select-principal" xm-select-max="20" xm-select-search='/user-info/search-by-name'></select>
                </div>
            </td>
        </tr>
    </table>
</form>

<!--负责人建议-->
<form class="layui-form" action="">
    <table class="layui-table">
        <tr>
            <th rowspan="2" class="leftTitle">负<br/>责<br/>人<br/>建<br/>议</th>
            <td colspan="12">
                <label class="layui-form-label leftLable01">实施人：</label>
                <div class="layui-input-block rightbox01">
                    <select name="implementation"  xm-select="select-implementation" xm-select-max="20" xm-select-search='/user-info/search-by-name'>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="12">
                <textarea name="implementationContent" required lay-verify="required" placeholder="请输入"
                          class="layui-textarea"></textarea>
            </td>
        </tr>
    </table>
</form>

<!--实施人建议-->
<form class="layui-form" action="">
    <table class="layui-table">
        <tr>
            <th rowspan="2" class="leftTitle">实<br/>施<br/>人<br/>建<br/>议</th>

            <td colspan="4" class="noborderLeft noborderRight">
                <label class="layui-form-label leftLable02">预计完成日期：</label>
                <div class="layui-input-block rightbox02">
                    <div class="layui-inline">
                        <input type="text" class="layui-input" required lay-verify="required" autocomplete="off"
                               id="expectTime">
                    </div>
                </div>
            </td>
            <td colspan="4" class="noborderRight">
                <label class="layui-form-label leftLable01">实施人：</label>
                <div class="layui-input-block rightbox01">
                    <input type="text" name="assessmentName" required lay-verify="required" autocomplete="off"
                           class="layui-input">
                </div>
            </td>

            <td colspan="4" class="noborderLeft">
                <label class="layui-form-label leftLable02">状态：</label>
                <div class="layui-input-block rightbox02">
                    <select name="state">
                        <option value="1">DONE</option>
                        <option value="3">ONGOING</option>
                        <option value="4">CANCEL</option>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="12">
                <textarea name="results" required lay-verify="required" placeholder="请输入"
                          class="layui-textarea"></textarea>
                <div class="layui-upload uploadImg">
                    <button type="button" class="layui-btn" id="test2">多图片上传</button>
                    <blockquote class="layui-elem-quote layui-quote-nm" style="margin-top: 10px;">
                        预览图：
                        <div class="layui-upload-list" id="demo2"></div>
                    </blockquote>
                </div>
            </td>

        </tr>
    </table>
</form>

<!--结果确认-->
<form class="layui-form" action="">
    <table class="layui-table">
        <tr>
            <th rowspan="3" class="leftTitle">结<br/>果<br/>确<br/>认</th>
            <td colspan="12">
                <label class="layui-form-label leftLable">结果：</label>
            </td>
        </tr>
        <tr>
            <td colspan="12">
                <textarea name="resultsContent" required lay-verify="required" placeholder="请输入"
                          class="layui-textarea"></textarea>
            </td>
        </tr>
        <tr>
            <td colspan="12">
                <label class="layui-form-label leftLable01">确认人：</label>
                <div class="layui-input-block rightbox01">
                    <input type="text" name="complaintPerson" required lay-verify="required" placeholder=""
                           autocomplete="off" class="layui-input">
                </div>
            </td>
        </tr>
    </table>
</form>

<!--申诉-->
<form class="layui-form" action="">
    <table class="layui-table">
        <tr>
            <th rowspan="3" class="leftTitle">申<br/>诉</th>
            <td colspan="12">
                <label class="layui-form-label leftLable">申诉：</label>
            </td>
        </tr>
        <tr>
            <td colspan="12">
                <textarea name="complaint" required lay-verify="required" placeholder="请输入"
                          class="layui-textarea"></textarea>
            </td>
        </tr>
        <tr>
            <td colspan="12">
                <label class="layui-form-label leftLable01">申诉人：</label>
                <div class="layui-input-block rightbox01">
                    <input type="text" name="complaintPerson" required lay-verify="required" placeholder=""
                           autocomplete="off" class="layui-input">
                </div>
            </td>
        </tr>
    </table>
</form>

<!--申诉结果-->
<form class="layui-form" action="">
    <table class="layui-table">
        <tr>
            <th rowspan="3" class="leftTitle">申<br/>诉<br/>结<br/>果</th>
            <td colspan="12">
                <label class="layui-form-label leftLable01">申诉结果：</label>
                <div class="layui-input-block rightbox01">
                    <select name="complaintSelect" lay-verify="required">
                        <option value="接受">接受</option>
                        <option value="不接受">不接受</option>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="12">
                <textarea name="complaint" required lay-verify="required" placeholder="请输入"
                          class="layui-textarea"></textarea>
            </td>
        </tr>
        <tr>
            <td colspan="6" class="noborderRight">
                <label class="layui-form-label leftLable02">委员会签字：</label>
                <div class="layui-input-block rightbox02">
                    <input type="text" name="memberSign" required lay-verify="required" placeholder=""
                           autocomplete="off" class="layui-input">
                </div>
            </td>
            <td colspan="6" class="noborderLeft">
                <label class="layui-form-label leftLable02">完成评估日期：</label>
                <div class="layui-input-block rightbox02">
                    <input type="text" name="completionDate" required lay-verify="required" placeholder=""
                           autocomplete="off" class="layui-input">
                </div>
            </td>
        </tr>
    </table>
</form>
<form class="layui-form" action="">
    <table class="layui-table">
        <tr>
            <th rowspan="1" class="leftTitle">奖<br/>励</th>
            <td colspan="6" class="noborderRight">
                <label class="layui-form-label leftLable02">建议奖项：</label>
                <div class="layui-input-block rightbox02">
                    <select name="award">
                        <option value="Bravo!（积分兑换）">Bravo!（积分兑换）</option>
                        <option value="PE家1分">PE家1分</option>
                        <option value="PE家0.5分">PE家0.5分</option>
                        <option value="PE家0.4分">PE家0.4分</option>
                        <option value="PE家0.3分">PE家0.3分</option>
                        <option value="PE家0.2分">PE家0.2分</option>
                        <option value="PE家0.1分">PE家0.1分</option>
                        <option value="PE家不加分">PE家不加分</option>
                    </select>
                </div>
            </td>
            <td colspan="6" class="noborderLeft">
                <label class="layui-form-label leftLable02">建议类型：</label>
                <div class="layui-input-block rightbox02">
                    <select name="suggestedType">
                        <option value="Break fix">Break fix</option>
                        <option value="Improvement IR">Improvement IR</option>
                    </select>
                </div>
            </td>
        </tr>
    </table>
</form>
<?php
$js = <<<JS

    layui.use('upload', function() {
        var $ = layui.jquery,
            upload = layui.upload;
        //多图片上传
        upload.render({
            elem: '#test2',
            url: '/upload/',
            multiple: true,
            before: function(obj) {
                //预读本地文件示例，不支持ie8
                obj.preview(function(index, file, result) {
                    console.log('result:', result);
                    $('#demo2').append('<img src="' + result + '" alt="' + file.name + '" class="layui-upload-img">')
                });
            },
            done: function(res) {
                //上传完毕
            }
        });
    });
    layui.use('laydate', function() {
        var laydate = layui.laydate;
        //执行一个laydate实例
        laydate.render({
            elem: '#assessmentTime' //指定元素
        });
    });
    layui.use('laydate', function() {
        var laydate = layui.laydate;
        //执行一个laydate实例
        laydate.render({
            elem: '#expectTime' //指定元素
        });
    });
    
  
JS;

$this->registerJs($js);

?>
