<?php

namespace backend\models\search;

use backend\models\Base;
use backend\models\Blog;
use backend\models\BlogMaterial;
use backend\models\BlogUser;
use backend\models\UserInfo;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class BlogSearch extends Blog
{
    public $title;
    public $status;
    public $start_time;
    public $end_time;
    public $associate;
    public $area;
    public $matter_number;
    public $device_number;
    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['title', 'status', 'start_time', 'end_time', 'associate','matter_number','device_number', 'area'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * 查询日志
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = $this->getSearchQuery($params);
        $query->andWhere([ 'nxp_blog.is_draft' => self::IS_DRAFT_NO]);
        if($this->matter_number || $this->device_number){
            $query->leftJoin(['bm' => BlogMaterial::tableName()],self::tableName().'.id = bm.blog_id');
            $query->andFilterWhere(['like','bm.matter_number',$this->matter_number]);
            $query->andFilterWhere(['like','bm.device_number',$this->device_number]);
        }

        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }
    /**
     * 查询日志
     * @param $params
     * @return ActiveDataProvider
     */
    public function searchDraft($params)
    {
        $this->setAttributes($params);
        $query = Blog::find();
        $uid   = (new Base())->getUid();
        //查看自己的
        $query->where(['nxp_blog.user_id' => $uid]);

        $startTime = ArrayHelper::getValue($this, 'start_time', '');
        $endTime   = ArrayHelper::getValue($this, 'end_time', '');
        $query->andFilterWhere(['>=', 'nxp_blog.create_time', $startTime]);
        $query->andFilterWhere(['<=', 'nxp_blog.create_time', $endTime]);
        $query->andFilterWhere(['like', 'nxp_blog.title', trim($this->title)]);
        $query->orderBy(['nxp_blog.create_time' => SORT_DESC]);
        $query->andWhere([ 'nxp_blog.is_draft' => self::IS_DRAFT_YES]);
        $dataProvider = new ActiveDataProvider([
            'query'     => $query,
            'pagination' => [
                'pageSize' => 20,
             ],
        ]);
        return $dataProvider;
    }

    /**
     * 查询方法
     * @param $params
     * @return \yii\db\ActiveQuery
     */
    public function getSearchQuery($params)
    {
        $this->setAttributes($params);
        $query = Blog::find();
        $uid   = (new Base())->getUid();
        $query->leftJoin(UserInfo::tableName() . ' u', 'nxp_blog.user_id = u.id');
        if ($uid > 1) {
            $userInfo = UserInfo::findOne(['id' => $uid]);
            $query->where(['u.area' => $userInfo->area]);
        } else {
            $query->andFilterWhere(['u.area' => $this->area]);
        }
        if ($this->associate == Blog::SEND_MYSELF) {
            $query->where(['nxp_blog.user_id' => $uid]);
        } elseif ($this->associate == Blog::ASSOCIATE) {
            $blogIdList = (new BlogUser())->getBlogId($uid);
            $query->where(['in', 'nxp_blog.id', $blogIdList]);
        }
        if ($this->status) {
            $query->andFilterWhere(['nxp_blog.status' => $this->status]);
        } else {
            $query->andFilterWhere(['<', 'nxp_blog.status', Blog::STATUS_SUCCESS]);
        }

        $startTime = ArrayHelper::getValue($this, 'start_time', '');
        $endTime   = ArrayHelper::getValue($this, 'end_time', '');
        $query->andFilterWhere(['>=', 'nxp_blog.create_time', $startTime]);
        $query->andFilterWhere(['<=', 'nxp_blog.create_time', $endTime]);
        $query->andFilterWhere(['like', 'nxp_blog.title', trim($this->title)]);
        $query->orderBy(['nxp_blog.create_time' => SORT_DESC]);
        return $query;
    }

    /**
     * 获取日志状态
     * @param $status
     * @return string
     */
    public function getStatus($status)
    {
        $result = '';
        switch ($status) {
            case Blog::STATUS_PROCESSING:
                $str    = Blog::$statusArr[$status]; //进行中
                $result = '<button type="button" class="layui-btn layui-btn-xs layui-bg-orange">' . $str . '</button>';
                break;
            case Blog::STATUS_DEFERRED:
                $str    = Blog::$statusArr[$status]; //已延期
                $result = '<button type="button" class="layui-btn layui-btn-xs layui-bg-blue">' . $str . '</button>';
                break;
            case Blog::STATUS_SUCCESS:
                $str    = Blog::$statusArr[$status];  //已完成
                $result = '<button type="button" class="layui-btn layui-btn-xs layui-bg-cyan">' . $str . '</button>';
                break;
        }
        return $result;
    }
}