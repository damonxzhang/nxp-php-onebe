<?php

namespace backend\models\search;

use backend\models\Base;
use backend\models\UserInfo;
use yii\data\ActiveDataProvider;

class UserInfoSearch extends UserInfo
{

    public $badge_code;
    public $chinese_name;
    public $tel;
    public $leader;
    public $position;
    public $station_id;
    public $shift;
	public $role;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['leader', 'position', 'role', 'station_id', 'shift',  'section', 'tel', 'chinese_name', 'badge_code'], 'safe'],
        ];
    }

    public function gembaSearch($params){
        $dataProvider = $this->search($params);
        $dataProvider->query->andWhere(['!=','gemba_role','']);
        return $dataProvider;
    }

    /**
     * 数据查询
     * @param $param
     * @return ActiveDataProvider
     */
    public function search($param)
    {
        $this->setAttributes($param);
        $query = UserInfo::find();
        if(!empty($this->leader)){
            $userInfoModel = UserInfo::findOne(['chinese_name' => trim($this->leader)]);
            if(!empty($userInfoModel->id)){
                $query->andFilterWhere(['leader_uid' => $userInfoModel->id]);
            }
        }
        $uid = (new Base())->getUid();
        // if($uid != 1){
        //     $query->where(['id' => $uid]);
        // }
        $query->andFilterWhere([
            'position'   => $this->position,
            'station_id' => $this->station_id,
            'section'    => $this->section,
            'shift'      => $this->shift,
            'tel'        => $this->tel,
			'role_id'    => $this->role,
        ]);
        $query->andFilterWhere(['like', 'chinese_name', $this->chinese_name]);
        $query->andFilterWhere(['like', 'badge_code', $this->badge_code]);
        $query->orderBy(['id' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }

}