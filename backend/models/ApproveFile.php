<?php

namespace backend\models;

/**
 * This is the model class for table "nxp_approve_file".
 *
 * @property int $id
 * @property int $approve_id
 * @property int $detail_id
 * @property string $file_path
 * @property string $file_name
 * @property string $file_extension
 * @property string $create_time 创建时间
 */
class ApproveFile extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_approve_file';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['approve_id', 'detail_id'], 'integer'],
            [['file_extension'], 'string', 'max' => 10],
            [['file_path', 'file_name',], 'string', 'max' => 255],
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
            'approve_id'     => '表ID',
            'detail_id'      => '详情表ID',
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
        $this->approve_id     = empty($data['approve_id']) ? '' : $data['approve_id'];
        $this->detail_id      = empty($data['detail_id']) ? '' : $data['detail_id'];
        $this->file_path      = trim($data['file_path']);
        $this->file_name      = trim($data['file_name']);
        $this->file_extension = $data['file_extension'];
        $this->create_time    = date('Y-m-d H:i:s', time());
        if (!$this->save()) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * 获取文件
     * @param array $where
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getApproveFileData($where = [])
    {
        $query = self::find();
        $query->where($where);
        $query->orderBy(['id' => SORT_DESC]);
        $query->asArray();
        return $query->all();
    }

    /**
     * 检查上传文件是否存在
     * @param $id //日志表id
     * @return bool
     */
    public function checkApproveFileData($id)
    {
        $where       = ['blog_id' => $id];
        $detailModel = ApproveDetails::find()->where(['approve_id' => $id])->orderBy(['create_time' => SORT_DESC])->all();
        if ($detailModel) {
            $idList = array_column($detailModel, 'id');
            $where  = ['in', 'detail_id', $idList];
        }
        $query = self::find();
        $query->where(['approve_id' => $id]);
        $query->orderBy(['id' => SORT_DESC]);
        $query->asArray();
        $result = $query->all();
        return !empty($result) ? TRUE : FALSE;
    }

    /**
     * 根据消息id删除图片数据
     * @param $id
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function deleteFileById($id)
    {
        $model = self::findAll(['approve_id' => $id]);
        if (!empty($model)) {
            $isOk = TRUE;
            foreach ($model as $value) {
                //如果删除失败
                if (!$value->delete()) {
                    $isOk = FALSE;
                    break;
                }
            }
            return $isOk;
        } else {
            return TRUE;
        }

    }
}

