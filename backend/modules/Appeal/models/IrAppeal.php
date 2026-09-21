<?php
namespace backend\modules\Appeal\models;

use backend\models\Ir;
use Yii;
use yii\db\Exception;
use backend\models\IrData;
use backend\models\Appeal;

class IrAppeal extends Appeal implements AppealInterface{

    const SCEN_CREATE = 'create';
    const SCEN_AUDIT = 'audit';

    //同意申请则继续执行的类型
    public static $go_appeal = [
        self::IR_DIRECTOR_TYPE,
        self::IR_ASSESS_TYPE
    ];

    /**
     * 申诉类型对应的ir状态
     * @var array
     */
    public static $appeal_type_by_ir_status = [
        self::IR_DIRECTOR_TYPE => Ir::STATUS_AUDIT,
        self::IR_ASSESS_TYPE => Ir::STATUS_EXECUTE,
        self::IR_HEAD_TYPE => Ir::STATUS_EXECUTE,
        self::IR_WORK_TYPE => Ir::STATUS_CARRY_OUT,
        self::IR_CONFIRM_TYPE => Ir::STATUS_CONFIRM
    ];

    //同意申诉则结束执行的类型
    public static $die_appeal = [
        self::IR_HEAD_TYPE,
        self::IR_WORK_TYPE,
        self::IR_CONFIRM_TYPE
    ];

    public function scenarios()
    {
        return [
            self::SCEN_CREATE => ['type', 'content', 'appeal_user','relation_id'],
            self::SCEN_AUDIT => ['status','relation_id','appeal_message'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'content', 'appeal_user', 'relation_id','appeal_message', 'audit_user'],'required'],
            [['content', 'appeal_message'], 'string'],
            [['type', 'status', 'audit_user','appeal_user', 'relation_id', 'complete_date', 'create_time', 'update_time'], 'integer'],
        ];
    }


    /**
     * 新增申诉
     * @return mixed
     */
    public function addAppeal(){

        $tra = Yii::$app->db->beginTransaction();
        try{
            $irModel = $this->findIrModel();
            if($this->appealExist($irModel->id,$this->type)){
                throw new Exception('无法重复提交申诉！');
            }
            $this->appeal_user = Yii::$app->user->identity->user_id;
            $this->status = self::STATUS_APPLY;
            //保存申诉
            if(!$this->save()){
                throw new Exception($this->errorToString());
            }

            $irModel->setScenario($irModel::APPEAL_SCE );
            //更新IR状态为申诉中
            $irModel->status = $irModel::STATUS_APPEAL;
            if(!$irModel->save()){
                throw new Exception($irModel->errorToString());
            }
            $tra->commit();
            return $irModel;
        }catch (\Exception $e){
            $tra->rollBack();
            $this->addError('id',$e->getMessage());
            return false;
        }
    }

    /**
     * 申诉审核
     * @return bool|mixed
     */
    public function auditAppeal(){


        $tra = Yii::$app->db->beginTransaction();
        try{
            $irModel = $this->findIrModel();
            $this->audit_user = Yii::$app->user->identity->user_id;
            $this->complete_date = time();
            //保存申诉
            if(!$this->save()){
                throw new Exception($this->errorToString());
            }

            $irModel->setScenario($irModel::APPEAL_SCE );
            //更新IR状态
            if($this->status == self::STATUS_AGREED){
                if(in_array($this->type,self::$die_appeal) ){
                    //取消
                    $irModel->status = $irModel::STATUS_DIE;
                }elseif (in_array($this->type,self::$go_appeal)){

                    //执行
                    $irModel->status = self::$appeal_type_by_ir_status[$this->type];;
                }

            }else{
                if(in_array($this->type,self::$die_appeal)){

                    //执行
                    $irModel->status = self::$appeal_type_by_ir_status[$this->type];;

                }elseif (in_array($this->type,self::$go_appeal )){
                    //取消
                    $irModel->status = $irModel::STATUS_DIE;
                }
            }
            if(!$irModel->save()){
                throw new Exception($irModel->errorToString());
            }
            $tra->commit();
            return $irModel;

        }catch (\Exception $e){
            $tra->rollBack();
            $this->addError('id', $e->getMessage());
            return false;
        }
    }

    public function getIrStatus(){



    }

    /**
     * 申诉是否重复
     * @param $id
     * @param $type
     * @return bool
     */
    public function appealExist($id,$type){
        return self::find()->where(['relation_id' => $id,'type' => $type])->exists();
    }

    /**
     * @return IrData|null\
     */
    public function findIrModel(){
        $irModel = IrData::findOne($this->relation_id);
        if(isset($irModel)){
            return $irModel;
        }else{
            throw new Exception('服务器错误！请稍后重试！');
        }
    }


}