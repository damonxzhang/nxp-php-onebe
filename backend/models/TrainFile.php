<?php

namespace backend\models;

use Yii;
use yii\base\Model;

/**
 * This is the model class for table "nxp_train_file".
 *
 * @property int $train_id
 * @property string $file_path
 * @property string $file_name
 * @property string $file_extension
 * @property string $create_time 创建时间
 */
class TrainFile extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_train_file';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['train_id'], 'integer'],
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
            'train_id'       => '培训表ID',
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
        $this->train_id       = $data['train_id'];
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
     * 获取培训文件
     * @param $id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getTrainFileData($id)
    {
        $query = self::find();
        $query->where(['train_id' => $id]);
        $query->orderBy(['id'=>SORT_DESC]);
        $query->asArray();
        return $query->all();
    }
}
