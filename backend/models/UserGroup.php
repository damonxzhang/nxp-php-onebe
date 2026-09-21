<?php

namespace backend\models;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;


/**
 * This is the model class for table "nxp_user_group".
 *
 * @property string $id
 * @property string $name
 * @property string $group_id
 * @property int $status 状态 1:启用 2:禁用
 * @property int $create_time 创建时间
 */
class UserGroup extends Base
{
    const TYPE_OPEN  = 1;
    const TYPE_CLOSE = 2;

    //启用状态
    public static $statusArr = [
        self::TYPE_OPEN  => '启用',
        self::TYPE_CLOSE => '禁用',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_user_group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['name', 'create_time', 'group_id'], 'string'],
            [['name'], 'unique']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'name'        => '名称',
            'group_id'    => '分组人员id列表',
            'status'      => '状态',
            'create_time' => '创建时间',
        ];
    }

    /**
     * 添加
     * @param $data
     * @return array
     */
    public function create($data)
    {
        $this->name        = trim($data['name']);
        $this->status      = $data['status'];
        $this->group_id    = $data['group_id'];
        $this->create_time = date('Y-m-d H:i:s', time());
        if (!$this->save()) {
            $error = array_values($this->getFirstErrors())[0];
            return $this->arrData(100, $error);
        }
        return $this->arrData(200, '添加成功');
    }

    /**
     * 修改分组
     * @param $data
     * @return array
     * @throws \yii\base\Exception
     */
    public function edit($data)
    {
        $model           = self::findOne($data['id']);
        $model->name     = $data['name'];
        $model->group_id = $data['group_id'];
        $model->status   = $data['status'];
        if (!$model->save()) {
            $error = array_values($model->getFirstErrors())[0];
            return $this->arrData(100, $error);
        }
        return $this->arrData(200, '更新成功');
    }

    /**
     * 检查分组是否存在
     * @param $data
     * @return bool
     */
    public function check($data)
    {
        $query = self::find();
        $query->where(['!=', 'id', $data['id']]);
        $query->andFilterWhere(['name'  => $data['name']]);
        if ($query->one()) {
            return false;
        }
        return true;
    }

    /**
     * 获取分组
     * @return array
     */
    public function getType()
    {
        $query = self::find();
        $query->select(['id', 'name']);
        $query->where(['status' => self::TYPE_OPEN]);
        $query->asArray();
        $data = $query->all();
        return $data ? ArrayHelper::map($data, 'id', 'name') : [];
    }

    /**
     * 获取分组人名称
     * @param $id
     * @return string
     */
    public function getUserName($id)
    {
        $str = '';
        $model = self::findOne(['id' => $id]);
        if(!empty($model)){
            $idList = explode(',', $model->group_id);
            $userNameList = UserInfo::find()->select('chinese_name')->where(['in', 'id', $idList])->asArray()->all();
            if(!empty($userNameList)){
                foreach ($userNameList as $value){
                    $str .= $value['chinese_name'] . ',';
                }
            }
        }
        return $str;
    }

    /**
     * 获取分组人员名称和id
     * @param $id
     * @return array
     */
    public function getGroupIdName($id)
    {
        $model = self::findOne(['id' => $id]);
        if(!empty($model)){
            $idList = explode(',', $model->group_id);
            $userInfo = UserInfo::find()
                ->select(['id','chinese_name as name'])
                ->where(['in', 'id', $idList])
                ->asArray()
                ->all();
            return $userInfo;
        }
        return [];
    }

    /**
     * 获取所有用户分组人员信息
     * @return array
     */
    public function getAllUserGroup()
    {
        $data = [];
        $model = UserGroup::find()->where(['status' => UserGroup::TYPE_OPEN])->asArray()->all();
        if (empty($model)) {
            return $data;
        }
        foreach ($model as $value) {
            $childrenArr = array();
            $idList      = explode(',', $value['group_id']);
            $userList    = UserInfo::find()->select(['id', 'chinese_name'])->where(['in', 'id', $idList])->asArray()->all();
            if (!empty($userList)) {
                foreach ($userList as $val) {
                    $childrenArr[] = ['name' => $val['chinese_name'], 'value' => $val['id'], 'children' => []];
                }
                $data[] = ['name' => $value['name'], 'value' => $value['id'] + 9999, 'children' => $childrenArr];
            }
        }
        return $data;
    }
}


