<?php

namespace backend\models;

/**
 * This is the model class for table "nxp_blog_details".
 *
 * @property string $id
 * @property int $blog_id
 * @property int $accept_user_id
 * @property int $create_user_id
 * @property int $detail_id
 * @property int $type
 * @property string $create_time
 */
class BlogUser extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_blog_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['blog_id', 'create_user_id', 'accept_user_id', 'type', 'detail_id'], 'integer'],
            [['create_time'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'             => 'ID',
            'blog_id'        => '关联日志ID',
            'detail_id'      => '日志详情id',
            'create_user_id' => '发起人ID',
            'accept_user_id' => '接受人ID',
            'type'           => '交接类型',
            'create_time'    => '创建时间',
        ];
    }

    /**
     * 添加人员信息
     * @param $param
     * @return array
     */
    public function create($param)
    {
        $this->create_user_id = $param['create_user_id'];
        $this->accept_user_id = $param['accept_user_id'];
        $this->blog_id        = $param['blog_id'];
        $this->detail_id      = $param['detail_id'];
        $this->type           = $param['type'];
        $this->create_time = date('Y-m-d H:i:s', time());
        if (!$this->save()) {
            $error = array_values($this->getFirstErrors())[0];
            return $this->arrData(100, $error);
        }
        return $this->arrData(200, '添加成功');
    }

    /**
     * 删除日志
     * @param $blogId
     * @return bool
     */
    public function ByBlogIdDeleteUser($blogId){
        $model = self::findAll(['blog_id' => $blogId]);
        if(!empty($model)){
            $isOk = true;
            foreach ($model as $value){
                //如果删除失败
                if(!$value->delete()){

                    $isOk = false;
                    break;
                }
            }
            return $isOk;
        }else{
            return true;
        }

    }

    /**
     * 获取交接人名称
     * @param $id
     * @return string
     */
    public function getUserName($id)
    {
        $str = '';
        $userIdList = self::find()->select('accept_user_id')->where(['detail_id' => $id])->column();
        if(!empty($userIdList)){
            $userNameList = UserInfo::find()->select('chinese_name')->where(['in', 'id', $userIdList])->asArray()->all();
            if(!empty($userNameList)){
                foreach ($userNameList as $value){
                    $str .= $value['chinese_name'] . ',';
                }
            }
        }
        return $str;
    }

    /**
     * 获取与我相关的日志id
     * @param $acceptUserId
     * @return array
     */
    public function getBlogId($acceptUserId)
    {
        return self::find()->select('blog_id')->where(['accept_user_id' => $acceptUserId])->distinct()->column();
    }

    /**
     * 获取交接人数组
     * @param $id
     * @return array
     */
    public function getAcceptUserId($id)
    {
        return self::find()->select('accept_user_id')->where(['blog_id' => $id])->column();
    }

}
