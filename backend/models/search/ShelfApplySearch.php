<?php

namespace backend\models\search;

use backend\models\ShelfApply;
use backend\models\ShelfMaterials;
use backend\models\UserInfo;
use yii\data\ActiveDataProvider;

class ShelfApplySearch extends ShelfApply
{

    public $badge_code;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['badge_code'], 'safe'],
        ];
    }

    /**
     * 数据查询
     * @param $param
     * @return ActiveDataProvider
     */
    public function search($param)
    {
        $this->setAttributes($param);
        $query = ShelfApply::find();
        $query->leftJoin(UserInfo::tableName() . ' u', 'nxp_shelf_apply.user_id = u.id');
        $query->where([
            'u.badge_code' => trim($this->badge_code),
            'nxp_shelf_apply.status' => ShelfApply::STATUS_UNRECEIVE, //申请状态为未领取
        ]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }

}