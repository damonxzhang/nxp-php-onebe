<?php

namespace frontend\controllers;

use backend\models\AppVersion;
use backend\models\Blog;
use backend\models\Facility;
use backend\models\Inspection;
use backend\models\Maintain;
use backend\models\service\MaintainService;
use backend\models\UserInfo;
use backend\modules\MaintainApi\models\SendEmail;
use common\models\Utility;
use Yii;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

class MaintainController extends FrontendBaseController
{

    /**
     * APP提交产线异常
     */
    public function actionCreate()
    {
//        $model = new MaintainService();
//        $model->setScenario(MaintainService::CREATE_SCENARIOS);
        $params = $this->validateRequest();

        $model = new Maintain();
        $model->number = $this->getNumber(8,false,$params['user_id']);

        //获取创建人信息
        $user_info = UserInfo::findOne(['id'=>$params['user_id']]);
        if (empty($user_info)){
            return $this->outputJson('false','创建人不存在');
        }
        $model->create_user = $user_info->id;
        $model->operation_user = $user_info->id;
        $model->director_user = $user_info->leader_uid;
        $model->area = (new UserInfo())->getAreaById($user_info->id);

        //获取报修信息
        $inspe_info = Inspection::findOne(['id'=>$params['inspection_id']]);
        if (empty($inspe_info)){
            return $this->outputJson('false','报修信息为空');
        }
        $model->things = $inspe_info->question_type;
        $model->description = $inspe_info->explanation;
        $model->f_id = $inspe_info->facility_id;
        $model->material_number = $inspe_info->part_no;
        $model->happen_date = $inspe_info->created_at;
//        $model->solve_status = 2;
        $model->status = 2;

        //报修图片转到产线异常中
        $model->files = $inspe_info->repair_file_id;
        $model->source = 2;


        //获取设备信息
        $facility_info = Facility::findOne(['id'=>$inspe_info->facility_id]);
        if (empty($facility_info)){
            return $this->outputJson('false','没有对应的设备信息');
        }
        $model->feedback_user = $facility_info->head_user;


        $model->create_time = time();
        $model->type = 1;
        $model->other_feedback_user = $params['other_feedback_user'];
//        print_r($model);die;

        if($model->save()){
            return $this->outputJson('true','添加成功');

        }else{
            return $this->outputJson('false','添加失败');

        }

//        if ($model->load($params)) {
//
//            $tra = Yii::$app->db->beginTransaction();
//            try{
//                if ($model->newMaintain()) {
//                    $this->sendRemindByEmail($model);
//                    $tra->commit();
//                    return $this->outputJson('true','成功');
//                } else {
//                    throw new Exception($model->errorToString());
//                }
//            }catch (Exception $e){
//                $tra->rollBack();
//                return $this->json(100,$e->getMessage());
//            }
//        }



    }

    /**
     * 生成编号
     * @param int $len
     * @return int|string
     */
    public function getNumber($len = 8,$str = false,$userId){
        $upId = Maintain::find()->select('id')->limit(1)->orderBy('id desc')->column();
        if($upId){
            $id = $upId[0] + 1;
        }else{
            $id = 1;
        }
        $idLen = strlen($id);

        $newLen = $len - $idLen;

        if($newLen > 0 && $idLen < $len){
            for ($i = 1;$i<=$newLen;$i++){
                $id = '0'.$id;
            }
        }
        if(!$str){
            $str = ArrayHelper::getValue(UserInfo::$areaArr, (new UserInfo())->getAreaById($userId), '');
            $str = substr($str,0,1);
        }

        return $str.$id;
    }


    /**
     *
     * @param Maintain $maintain
     */
    public function sendRemindByEmail(Maintain $maintain){
        $user = new UserInfo();
        $ids = $user->getLeaderUids($maintain->create_user);
        if(empty($ids[0])){
            throw new Exception('当前用户没有直属领导！请检在用户管理中添加后再进行此操作！');
        }
        $maintain->feedback_user        && $ids[] = $maintain->feedback_user;
        $maintain->other_feedback_user  && $ids[] = $maintain->other_feedback_user;
        //查询用户的名称和邮箱
        $userEmail = $user->getUserEmail($ids);
        if(!empty($userEmail)){
            $params = $maintain->attributes;
            foreach ($userEmail as $key => $val){
                $params['name'] = $val['chinese_name'];
                if (empty($val['email'])){
                    throw new Exception('用户'.$params['name'].'未输入邮箱！请完善用户邮箱！');
                }

                if(!$this->sendRemindEmail($val['email'],$params)){
                    throw new Exception('用户'.$params['name'].'邮件发送失败！请检查邮箱的有效性！');
                }
            }
        }
        return false;
    }


    /**
     * 发送邮件
     * @param $email
     * @param $params
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function sendRemindEmail($email,$params){

        return Yii::createObject([
            'class' => SendEmail::className(),
            'to' => trim($email,"\r\n"),
            'view' => SendEmail::CREATE_EMAIL_VIEW,
            'subject' => 'New Anomaly Record',
            'renderParams' => $params
        ])->send();
    }

    /**
     * 获取用户列表
     */
    public  function actionGetUserList(){
        $params = $this->validateRequest();


        //返回所有该工序的人
        $query = Inspection::find();
        $query->alias('i');
        $query->select(['f.area']);
        $query->leftJoin(Facility::tableName() . ' f', 'i.facility_id = f.id');
        $query->andWhere(['i.id'=>$params['inspection_id']]);
        $get_data = $query->asArray()->all();

        $process_list = [];

        foreach ($get_data as $key=>$value){
            $process_list[$key] = $value;
        }
//        print_r($process_list);die;

        $get_list = UserInfo::find();
        $get_list->select(['id','chinese_name']);
//        $get_list->andWhere(['shift'=>'6']);
        $get_list->andWhere(['position'=>'3']);
        $get_list->andWhere(['in','area',$process_list]);
        $get_list_result = $get_list->asArray()->all();

        return $this->outputJson('true','查询成功',$get_list_result);
    }

}


