<?php

namespace backend\models;

/**
 * This is the model class for table "nxp_blog_file".
 *
 * @property int $id
 * @property int $blog_id
 * @property int $detail_id
 * @property string $file_path
 * @property string $file_name
 * @property string $file_extension
 * @property string $create_time 创建时间
 */
class BlogFile extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_blog_file';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['blog_id','detail_id'], 'integer'],
            [['file_extension'], 'string', 'max' => 10],
            [['file_path', 'file_name', ], 'string', 'max' => 255],
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
            'blog_id'        => '日志表ID',
            'detail_id'      => '日志详情表ID',
            'file_path'      => '文件存放路径',
            'file_name'      => '文件名称',
            'file_extension' => '文件后缀',
            'create_time'    => '创建时间',
        ];
    }

    /**
     * 添加数据
     * @param $data
     * @return bool
     */
    public function create($data)
    {
        $this->blog_id        = empty($data['blog_id']) ? '' : $data['blog_id'];
        $this->detail_id      = empty($data['detail_id']) ? '' : $data['detail_id'];
        $this->file_path      = trim($data['file_path']);
        $this->file_name      = trim($data['file_name']);
        $this->file_extension = $data['file_extension'];
        $this->create_time    = date('Y-m-d H:i:s', time());
        if (!$this->save()) {
            return false;
        }
        return true;
    }

    /**
     * 获取日志文件
     * @param array $where
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getBlogFileData($where = [])
    {
        $query = self::find();
        $query->where($where);
        $query->orderBy(['id'=>SORT_DESC]);
        $query->asArray();
        return $query->all();
    }

    /**
     * 检查上传文件是否存在
     * @param $id //日志表id
     * @return bool
     */
    public function checkBlogFileData($id)
    {
        $where = ['blog_id' => $id];
        $detailModel   = BlogDetails::find()->where(['blog_id' => $id])->orderBy(['create_time' => SORT_DESC])->all();
        if ($detailModel){
            $idList = array_column($detailModel, 'id');
            $where = ['in','detail_id', $idList];
        }
        $query = self::find();
        $query->where($where);
        $query->orderBy(['id'=>SORT_DESC]);
        $query->asArray();
        $result = $query->all();
        return !empty($result) ? true : false;
    }

    /**
     * 根据日志id删除图片数据
     * @param $blogId
     * @return bool
     */
    public function ByBlogIdDeleteFile($blogId){
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
}
