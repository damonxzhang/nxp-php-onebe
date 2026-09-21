<?php

namespace backend\models;

use common\models\Utility;
use Yii;
use yii\helpers\ArrayHelper;

class Car extends Base
{
    //审批状态
    const APPROVAL_STATUS_GOING  = 0;//进行中
    const APPROVAL_STATUS_PASS   = 1;//已完成

    const STSTUS_PRODUCT_ENGINEER  = 1;
    const STSTUS_FEEDBACK_CLASS    = 2;
    const STSTUS_CONFIRMATION_DUTY = 3;
    const STSTUS_SUCCESS           = 4;
    //启用状态
    public static $statusArr = [
        self::STSTUS_PRODUCT_ENGINEER     => '待产品工程师填写',
        self::STSTUS_FEEDBACK_CLASS       => '待带班反馈',
        self::STSTUS_CONFIRMATION_DUTY    => '待带班确认【二次提交】',
        self::STSTUS_SUCCESS              => '已完成'
    ];
    

    const STSTUS_SECOND_OPERATOR   = 1;
    const STSTUS_SECOND_TAKE       = 2;
    const STSTUS_SECOND_HIGH_LEVEL = 3;
    const STSTUS_SECOND_SUCCESS    = 4;
    //启用状态
    public static $statusSecondArr = [
        self::STSTUS_SECOND_OPERATOR    => '待操作员填写',
        self::STSTUS_SECOND_TAKE        => '待带班确认',
        self::STSTUS_SECOND_HIGH_LEVEL  => '待HIGH LEVEL确认',
        self::STSTUS_SECOND_SUCCESS     => '已完成'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_car';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'id',
                    'station',
                    'date',
                    'shift',
                    'device_model',
                    'batch_number',
                    'package',
                    'machine_number',
                    'create_id',
                    'defective_items',
                    'inspector',
                    'storage_location',
                    'description',
                    'create_time',
                    'status',
                    'operator',
                    'number',
                    'second_status',
                    'result',
                    'responsibility',
                    'dpr',
                    'type',
                    'approval_status',
                    'trace_no',
                    'files_mapping',
                    'reasons_defects',
                    'corrective_action',
                    'second_files_mapping',
                    'trace_no_number',
                    'first_success_time',
                    'second_success_time',
                    'defective_number'
                ], 'safe'
            ]
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                 => 'ID',
            'station'            => '操作站',
            'date'               => '日期',
            'shift'              => '班别',
            'device_model'       => '器件型号',
            'batch_number'       => '批号',
            'package'            => '封装类型',
            'machine_number'     => '机器号',
            'operator'           => '操作员',
            'create_id'          => '创建人',
            'defective_items'    => '缺陷项目',
            'inspector'          => '检查员',
            'storage_location'   => '物料存放位置',
            'description'        => '描述',
            'create_time'        => '创建时间',
            'status'             => '状态',
            'number'             => '产品工程师审批循环次数',
            'second_status'      => '第二步状态',
            'result'             => '判定结果',
            'responsibility'     => '是否记操作员责任',
            'dpr'                => 'DPR权重',
            'approval_status'    => '审批状态',
            'trace_no'           => 'TRACE NO',
            'type'               => '大小材料',
            'reasons_defects'    => '第二步缺陷原因',
            'corrective_action'  => '第二步改正行动',
            'second_files_mapping'=> '第二步图片',
            'trace_no_number'     => 'TRACE NO Number',
            'first_success_time'  => '第一步结束时间',
            'second_success_time' => '第二步结束时间',
            'defective_number'    => '次品缺陷数量'
        
        ];
    }

    /**
     * 添加
     * @param $data
     * @return array
     */
    public function create($data)
    {
        $this->keyValue($data);
        $this->create_time = date('Y-m-d H:i:s', time());
        $error = $this->chkAttributes($data);
        if ($error) {
            return $this->arrData(100, Utility::arrayToString($error));
        }
        if (!$this->save(false)) {
            $error = array_values($this->getFirstErrors())[0];
            return $this->arrData(100, $error);
        }
        return $this->arrData(200, $this->attributes['id']);
    }

      /**
     * 获取提交的数据
     * @param $data
     */
    public function keyValue($data)
    {
        $this->trace_no         = strtoupper($data['trace_no']);
        $this->trace_no_number  = $data['trace_no_number'];
        $this->type             = $data['type'];
        $this->station          = $data['station'];
        $this->date             = date('Y-m-d', time());
        $this->shift            = $data['shift'];
        $this->device_model     = $data['device_model'];
        $this->batch_number     = strtoupper($data['batch_number']);
        $this->package          = $data['package'];
        $this->machine_number   = $data['machine_number'];
        $this->operator         = $data['operator'];
        $this->create_id        = $data['create_id'];
        $this->defective_items  = $data['defective_items'];
        $this->inspector        = $data['inspector'];
        $this->storage_location = $data['storage_location'];
        $this->description      = $data['description'];
        $this->defective_number = $data['defective_number'];
        $this->status           = self::STSTUS_PRODUCT_ENGINEER;
        $this->create_time      = date('Y-m-d H:i:s', time());
        $this->number           = 0;
        $this->second_status    = 0;
        $this->result           = 0;
        $this->responsibility   = 0;
        $this->dpr              = 0;
        $this->approval_status  = 0;
        $this->files_mapping    = $data['files_mapping'];
        $this->reasons_defects      = '';
        $this->corrective_action    = '';
        $this->second_files_mapping = '';
    }


    public function setImagesPath(){
        if(!empty($this->files_mapping)){
            $this->files_mapping = json_decode($this->files_mapping,1);
        }
    }

    public function setSecondImagesPath(){
        if(!empty($this->second_files_mapping)){
            $this->second_files_mapping = json_decode($this->second_files_mapping,1);
        }
    }

    public function validateUser($id){
        $isOk        = false;
        $model       = self::findOne($id);
        $createUserInfo = UserInfo::findOne($model->create_id);
        $loginUserId = Yii::$app->user->identity->user_id;
        $userInfo    = UserInfo::findOne($loginUserId);
        $position    = $userInfo->position;
        $pid         = $userInfo->pid;
        switch ($model->status){
            //步骤1只有指定产品工程师可操作
             case self::STSTUS_PRODUCT_ENGINEER:
                //if(in_array(trim($userInfo->pid),$engineerPidArr) || ($createUserInfo->section == $userInfo->section && $userInfo->position == UserInfo::POSITION_PRODUCT)){
                if($createUserInfo->section == $userInfo->section && $userInfo->position == UserInfo::POSITION_PRODUCT){
                    $isOk = true;
                }
                break;
            //步骤2只有带班可操作
            case self::STSTUS_FEEDBACK_CLASS:
                if($createUserInfo->leader_uid == $userInfo->id || ($createUserInfo->section == $userInfo->section && $userInfo->position == UserInfo::POSITION_TAKE)){
                    $isOk = true;
                }
                break;
            //步骤3只有带班可操作
            case self::STSTUS_CONFIRMATION_DUTY:
                if($createUserInfo->leader_uid == $userInfo->id || ($createUserInfo->section == $userInfo->section && $userInfo->position == UserInfo::POSITION_TAKE)){
                    $isOk = true;
                }
                break;
           
            case self::STSTUS_SUCCESS:
                $isOk = false;
                break;

        };
        return $isOk;
    }

    public function getOperatorChineseName($id){
        $model       = self::findOne($id);
        $operatorUserInfo = UserInfo::findOne(['wbi_code' => $model->operator]);
        return $operatorUserInfo ? $operatorUserInfo->chinese_name : '';
    }

    public function getLeaderName($id){
        $model       = self::findOne($id);
        $query = UserInfo::findOne(['wbi_code' => $model->operator]);
        if(empty($query)){
            return '';
        }
        $model = UserInfo::findOne(['id' => $query->leader_uid]);
        return empty($model) ? '' : $model->chinese_name;
    }

    public function validateSecondUser($id){
        $isOk        = false;
        $model       = self::findOne($id);
        $operatorUserInfo = UserInfo::findOne(['wbi_code' => $model->operator]);
        $loginUserId = Yii::$app->user->identity->user_id;
        $userInfo    = UserInfo::findOne($loginUserId);
       
        if(!$operatorUserInfo){
            return false;
        }
        switch ($model->second_status){
            //步骤1待操作员填写操作
             case self::STSTUS_SECOND_OPERATOR:
                if($operatorUserInfo->id == $loginUserId){
                    $isOk = true;
                }
                break;
            //步骤2待带班确认操作
            case self::STSTUS_SECOND_TAKE:
                if($operatorUserInfo->leader_uid == $userInfo->id){
                    $isOk = true;
                }
                break;
            //步骤3待HIGH LEVEL确认操作
            case self::STSTUS_SECOND_HIGH_LEVEL:
                $carHighLevel = CarHighLevel::findOne(['user_id' => $loginUserId]);
                if($carHighLevel && $carHighLevel->status == CarHighLevel::STATUS_ON){
                    $isOk = true;
                }else{
                    $isOk = false;
                }
                break;
           
            case self::STSTUS_SUCCESS:
                $isOk = false;
                break;

        };
        return $isOk;
    }

   
    public function getUserNameByIdArray($ids){
        if(empty($ids)){
            return false;
        }
        if(!is_array($ids)){
            $ids = explode(',',$ids);
        }
        $userNames = UserInfo::find()->select('chinese_name')->where(['id' => $ids])->column();
        if($userNames){
           return $userNames;
        }
        return false;
    }
}
