<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "nxp_handover_show".
 *
 * @property int $id
 * @property int $user_id 用户
 * @property int $log_id 日志id
 * @property int $status
 * @property int $updated_at
 * @property int $created_at
 */
class BlogShow extends Base
{
	
    const STATUS_SHOW 	  = 1; //进行中
    const STATUS_HIDDEN   = 2; //已过期

    //查询状态
    public static $statusArr = [
        self::STATUS_SHOW =>  '展示',
        self::STATUS_HIDDEN   => '禁用',
    ];



    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_blog_show';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'log_id' ], 'required'],
            ['log_id', 'unique'],
            [['user_id', 'log_id', 'updated_at', 'created_at','status'], 'integer'],
        ];
    }


    /**
     * 拿到所有展示日志信息
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getShowLogInfo(){
        return self::find()->alias('bs')
            ->select('b.*,bs.id bid,aui.chinese_name aname,bui.chinese_name bname')
            ->innerJoin(['b' => Blog::tableName()],'b.id = bs.log_id')
            ->innerJoin(['bu' => BlogUser::tableName()],'b.id = bu.blog_id')
            ->innerJoin(['aui' => UserInfo::tableName()],'bu.create_user_id = aui.id')
            ->innerJoin(['bui' => UserInfo::tableName()],'bu.accept_user_id = bui.id')
            ->andWhere(['bs.status'=>BlogShow::STATUS_SHOW])
			->asArray()
            ->all();

    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户',
            'log_id' => '日志',
            'status' => '状态',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }
}
