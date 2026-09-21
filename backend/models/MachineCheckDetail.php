<?php

namespace backend\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * This is the model class for table "nxp_machine_check_detail".
 *
 * @property int $id
 * @property int|null $check_id
 * @property int|null $facility_id
 * @property int|null $option_id
 * @property int|null $createby
 * @property string|null $score
 * @property string $file_path_id 文件上传路径
 * @property string|null $create_time 创建时间
 */
class MachineCheckDetail extends Base
{
    
    const ELIGIBILITY_ZERO  = 0;
    const ELIGIBILITY_ONE   = 1;
    const ELIGIBILITY_THREE = 3;

    public static $scoreList = [
        self::ELIGIBILITY_ZERO   => '0分',
        self::ELIGIBILITY_ONE    => '1分',
        self::ELIGIBILITY_THREE  => '3分'
    ];


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_machine_check_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['check_id', 'facility_id', 'option_id','createby'], 'integer'],
            [['file_path_id','score','remarks'], 'string'],
            [['create_time'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'             => 'ID',
            'check_id'       => '检查表id',
            'facility_id'    => '设备id',
            'option_id'      => '检查项id',
            'createby'       => '发布人',
            'score'          => '得分',
            'remarks'        => '备注',
            'file_path_id'   => '文件上传路径',
            'create_time'    => '创建时间',
        ];
    }

    /**
     * 获取 by id
     * 预留批量获取，目前只取最新的一个
     * @param $check_id
     * @return string
     */
    public function getPathById($check_id)
    {
        if (empty($check_id)) {
            return '';
        }
        $query = self::find();
        $query->select('file_path');
        $query->where(['=', 'check_id', $check_id]);
        $query->orderBy('id desc');
        $query->asArray();
        return $query->all();
    }

    /**
     * 获取文件
     * @param array $where
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getFileData($where = [])
    {
        $query = self::find();
        $query->where($where);
        $query->orderBy(['id' => SORT_DESC]);
        $query->asArray();
        return $query->all();
    }
}


