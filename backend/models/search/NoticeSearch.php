<?php
namespace  backend\models\search;

use backend\models\Base;
use backend\models\Notice;
use backend\models\NoticeUser;
use backend\models\UserInfo;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class NoticeSearch extends Notice {

    public $title;
    public $name;
    public $start_time;
    public $end_time;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['title', 'name', 'start_time', 'end_time'], 'safe'],
        ];
    }

    /**
     * 数据查询
     * @param $param
     * @return ActiveDataProvider
     */
    public function search($param)
    {
        $uId   = (new Base())->getUid();
        $this->setAttributes($param);
        $noticeIdList = Notice::find()->select('id')->where(['create_user_id' => $uId])->column();
        $noticeUserList = NoticeUser::find()->select('notice_id')->where(['user_id' => $uId])->column();
        foreach ($noticeIdList as $id){
            $noticeUserList[] = $id;
        }
        $query = new Query();
        $query->select([
            'n.*',
            'u.chinese_name',
        ]);
        $query->from(Notice::tableName() . ' n');
        $query->leftJoin(UserInfo::tableName() . ' u', 'n.create_user_id = u.id');
        $query->where(['in', 'n.id', $noticeUserList]);
        $query->andFilterWhere(['like', 'u.chinese_name', trim($this->name)]);
        $query->andFilterWhere(['like', 'n.title', trim($this->title)]);
        $startCreateTime = ArrayHelper::getValue($this, 'start_time');
        $endCreateTime   = ArrayHelper::getValue($this, 'end_time');
        $query->andFilterWhere(['>=', 'n.create_time', $startCreateTime]);
        $query->andFilterWhere(['<=', 'n.create_time', $endCreateTime]);
        $query->orderBy(['n.status' => SORT_DESC,'n.id' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }

}
