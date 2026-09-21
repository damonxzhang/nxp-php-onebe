<?php
namespace  backend\models\search;

use backend\models\Menu;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class MenuSearch extends Menu {

    public $create_time;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['create_time'], 'safe'],
        ];
    }

    /**
     * 数据查询
     * @param $param
     * @return ActiveDataProvider
     */
    public function search($param)
    {
        $this->load($param);
        $createTime = ArrayHelper::getValue($this, 'create_time');
        if (!empty($createTime)) {
            $createTimeList = explode(' 至 ', $createTime);
            $startCreateTime = date('Y-m-d H:i:s', strtotime($createTimeList[0]));
            $endCreateTime = date('Y-m-d 23:59:59', strtotime($createTimeList[1]));
        } else {
            $startCreateTime = $endCreateTime = '';
        }
        $query = Menu::find();
        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['>=', 'create_time', $startCreateTime]);
        $query->andFilterWhere(['<=', 'create_time', $endCreateTime]);
        $query->orderBy('id desc');
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }

}