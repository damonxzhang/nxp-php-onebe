<?php
use backend\models\UserInfo;
use backend\widgets\LayuiGridView;
?>

<div class="x-body">
    <?= LayuiGridView::widget([
        'dataProvider' => $models ,
        'columns' => [
            [
                'attribute' => 'original_area',
                'value' => function($row){
                    return UserInfo::$areaArr[$row->original_area];
                }
            ],
            [
                'attribute' => 'new_area',
                'value' => function($row){
                    return UserInfo::$areaArr[$row->new_area];
                }
            ],
            [
                'attribute' => 'create_user',
                'value' => function($row){
                    return (new UserInfo())->getUserName($row->create_user);
                }
            ],
            'content',
            [
                'attribute' => 'create_time',
                'value' => function($row){
                    return date('Y-m-d H:i',$row->create_time);
                }
            ],

        ]
    ])?>
</div>
