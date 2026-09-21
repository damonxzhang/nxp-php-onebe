<?php
use yii\helpers\Url;
$this->title = '人员刷卡记录管理';
?>
<style>
    .container { margin: 20px auto; max-width: 1200px; }
    .sync-tip { text-align: center; color: #666; margin: 10px 0; font-size: 14px; }
    .swipe-result-area { padding: 15px; background: #f8f9fa; border-radius: 5px; margin-bottom: 20px; border: 1px solid #dee2e6; }
    .swipe-result-area h4 { margin: 0 0 10px 0; color: #333; }
    .result-card { 
        display: inline-block; 
        padding: 8px 15px; 
        margin: 0 8px 8px 0; 
        border-radius: 4px; 
        color: #fff; 
        font-size: 14px;
    }
    .result-last-undeploy { background-color: #17a2b8; } /* 上周未投放 */
    .result-receive-success { background-color: #28a745; } /* 领取成功 */
    .result-last-cleaned { background-color: #ffc107; color: #333 !important; } /* 上周清洗过 */
    .result-deploy-success { background-color: #28a745; } /* 投放成功 */
    .result-deploy-fail { background-color: #dc3545; } /* 投放失败 */
    table { width: 100%; text-align: center; }
    table th { background: #f5f5f5; vertical-align: middle !important; }
    table td { vertical-align: middle !important; }
    .no-data { color: #999; }
    .loading { text-align: center; padding: 20px; color: #666; }
</style>
<div class="container">
    <h2 class="text-center">人员刷卡记录展示</h2>
    <div class="sync-tip">
        最后同步时间：<span id="lastSync" style="color: #0066cc;">--</span> | 每2分钟自动刷新
    </div>

    <!-- 刷卡结果展示区 -->
    <div class="swipe-result-area">
        <h4>本次人员刷卡结果</h4>
        <div id="swipeResultList" class="loading">加载中...</div>
    </div>

    <!-- 人员刷卡详情表格 -->
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>姓名</th>
                <th>PID</th>
                <th>区域</th>
                <th>帽子刷卡时间</th>
                <th>帽子刷卡信息</th>
                <th>静电服刷卡时间</th>
                <th>静电服刷卡信息</th>
                <th>裤子刷卡时间</th>
                <th>裤子刷卡信息</th>
            </tr>
        </thead>
        <tbody id="userList">
            <tr><td colspan="9" class="loading">加载中...</td></tr>
        </tbody>
    </table>
</div>
<?php $this->beginBlock('footer') ?>
<script>
$(function() {
    // 首次加载数据
    loadData();
    // 每2分钟定时刷新
    setInterval(loadData, 2 * 60 * 1000);

    /**
     * 加载并渲染所有数据
     */
    function loadData() {
        // 显示加载状态
        $('#swipeResultList').html('<span class="loading">加载中...</span>');
        $('#userList').html('<tr><td colspan="9" class="loading">加载中...</td></tr>');

        $.ajax({
            url: "<?= Url::to(['wash/sync-and-get-records']) ?>",
            type: "GET",
            dataType: "json",
            timeout: 10000, // 10秒超时
            success: function(res) {
                if (res.code === 200) {
                    $('#lastSync').text(res.last_sync);
                    renderSwipeResults(res.swipe_results);
                    renderUserTable(res.swipe_results);
                } else {
                    renderShow(res.show);
                }
            },
            error: function(xhr, status, error) {
                renderError('网络错误：' + error);
            }
        });
    }

    /**
     * 渲染刷卡结果卡片
     * @param {Array} results 结果列表
     */
    function renderSwipeResults(results) {
        if (!results || results.length === 0) {
            $('#swipeResultList').html('<span class="no-data">今日暂无刷卡结果</span>');
            return;
        }

        let html = '';
        $.each(results.data, function(index, item) {
            let className = '';
            switch (item.result) {
                case '上周未投放': className = 'result-last-undeploy'; break;
                case '本周领取成功': className = 'result-receive-success'; break;
                case '上周已经清洗过': className = 'result-last-cleaned'; break;
                case '本周投放成功': className = 'result-deploy-success'; break;
                case '本周投放失败': className = 'result-deploy-fail'; break;
                default: className = '';
            }
            html += `<div class="result-card ${className}">${item.name}（${item.type}）：${item.result}</div>`;
        });
        $('#swipeResultList').html(html);
    }

    /**
     * 渲染人员刷卡表格
     * @param {Array} data 人员数据
     */
    function renderUserTable(data) {
        if (!data || data.length === 0) {
            $('#userList').html('<tr><td colspan="9" class="no-data">今日暂无人员刷卡记录</td></tr>');
            return;
        }

        let html = '';
        $.each(data, function(index, item) {
            html += `<tr>
                <td>${item.user_name}</td>
                <td>${item.pid}</td>
                <td>${item.area}</td>
                <td>${item.hat.wash_time}</td>
                <td>${item.hat.info}</td>
                <td>${item.cloth.wash_time}</td>
                <td>${item.cloth.info}</td>
                <td>${item.pants.wash_time}</td>
                <td>${item.pants.info}</td>
            </tr>`;
        });
        $('#userList').html(html);
    }

    /**
     * 渲染错误信息
     * @param {String} msg 错误信息
     */
    function renderError(msg) {
        $('#swipeResultList').html(`<span style="color: red;">${msg}</span>`);
        $('#userList').html(`<tr><td colspan="9" style="color: red;">${msg}</td></tr>`);
    }

    /**
     * 渲染错误信息
     * @param {String} msg 错误信息
     */
    function renderShow(msg) {
        $('#swipeResultList').html(`<span style="color: red;">${msg}</span>`);
    }
});
</script>
<?php $this->endBlock() ?>