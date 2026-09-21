<?php

namespace frontend\controllers;

use backend\models\Facility;
use backend\models\InspectionPmFile;
use backend\models\InspectionPm;
use backend\models\InspectionPmCheck;
use backend\models\InspectionPmCheckPicture;
use backend\models\InspectionPmFacility;
use backend\models\InspectionPmGroupItem;
use backend\models\InspectionPmRule;
use backend\models\InspectionPmRuleSpecial;
use backend\models\UserInfo;
use yii\helpers\ArrayHelper;

class InspectionPmApiController extends FrontendBaseController
{
    public $enableCsrfValidation = false;
    /*
     * 待我处理的周PM检查列表
     */
    public function actionPmList()
    {

        $params = $this->validateRequest();

        $query = InspectionPm::find();
        $query->alias('ip');
        $query->select([
            'ip.*',
            'f.name as f_name',
            'f.head_user as f_head_user',
            'f.process as f_process'
        ]);
        $query->leftJoin(Facility::tableName() . ' f', 'ip.facility_id = f.id');
        $query->andFilterWhere(['f.process' => $params['process']]);

        if ($params['status'] == '') {
            $query->where(['<>', 'ip.status', 2]);
            $query->where(['deal_user_id' => $params['user_id']]);
        } else {
            $query->where(['ip.status' => $params['status']]);

            switch ($params['status']) {
                case 0:
                    $query->andFilterWhere(['ip.user_info_id' => $params['user_id']]);
                    break;
                case 1:
                    $query->andFilterWhere(['f.head_user' => $params['user_id']]);
                    break;
                default :
                    break;
            }
        }

        $query->asArray();
        $dataProvider = $query->all();

        $data = [];

        if (!empty($dataProvider)) {
            foreach ($dataProvider as $key => $value) {
                $data[$key]['id']            = $value['id'];
                $data[$key]['facility_id']   = $value['facility_id'];
                $data[$key]['facility_name'] = $value['f_name'];
                $data[$key]['head_user']     = (new UserInfo())->getUserName($value['f_head_user']);
                $data[$key]['check_time']    = $value['check_time'];
                $data[$key]['status']        = ArrayHelper::getValue(InspectionPm::$statusList, $value['status'], '');
                $data[$key]['process']       = ArrayHelper::getValue(Facility::$processList, $value['f_process'], '');
            }
        }

        return $this->outputJsonApp('true', '查询成功', $data);
    }

    /**
     * pm检查内容
     */
    public function actionPmInfo()
    {
        $params = $this->validateRequest();

        $query = InspectionPmFacility::find();
        $query->alias('ipf');
        $query->select([
            'ipf.id as pm_id',
            'f.id as facility_id',
            'f.name as f_name',
            'f.status as f_status',
            'f.process as f_process',
            'ipgi.id as item_id',
            'ipgi.name as item_name'
        ]);
        $query->leftJoin(InspectionPmGroupItem::tableName() . ' ipgi', 'ipgi.group_id = ipf.group_id');
        $query->leftJoin(Facility::tableName() . ' f', 'ipf.facility_id = f.id');
        $query->andFilterWhere(['ipf.facility_id' => $params['facility_id']]);
        $query->asArray();
        $dataProvider = $query->all();

        $data = [];

        if (!empty($dataProvider)) {
            $data['pm_id']         = $dataProvider[0]['pm_id'];
            $data['facility_id']   = $dataProvider[0]['facility_id'];
            $data['facility_name'] = $dataProvider[0]['f_name'];
            $data['process']       = ArrayHelper::getValue(Facility::$processList, $dataProvider[0]['f_process'], '');

            foreach ($dataProvider as $key => $value) {
                $data['pm_name_list'][$key]['item_id']   = $value['item_id'];
                $data['pm_name_list'][$key]['item_name'] = $value['item_name'];
            }
        }

        return $this->outputJsonApp('true', '查询成功', $data);
    }

    /**
     * 提交检查项
     * @throws \yii\db\Exception
     */
    public function actionPmCheck()
    {
        $params = $this->validateRequest();
        if (empty($params['group_item'])) {
            return $this->outputJsonApp('false', '请选择检查项!');
        }
        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($params['group_item'] as $id) {
            $pmCheck                = new InspectionPmCheck();
            $pmCheck->pm_id         = $params['pm_id'];
            $pmCheck->group_item_id = $id;
            $pmCheck->check_time    = date('Y-m-d H:i:s', time());
            $pmCheck->type          = InspectionPmCheck::TYPE_CHECK;
            $pmCheck->user_info_id  = $params['user_id'];
            if (!$pmCheck->save(false)) {
                $transaction->rollBack();
                return $this->outputJsonApp('false', '添加检查失败!');
            }
        }
        if (!empty($params['pm_file'])) {
            foreach ($params['pm_file'] as $id) {
                $pmCheckPicture              = InspectionPmFile::findOne($id);
                $pmCheckPicture->pm_id       = $params['pm_id'];
                $pmCheckPicture->type        = InspectionPmCheck::TYPE_CHECK;
                if (!$pmCheckPicture->save(false)) {
                    $transaction->rollBack();
                    return $this->outputJsonApp('false', '添加检查图片失败!');
                }
            }
        }

        $transaction->commit();
        return $this->outputJsonApp('true', '成功!');
    }


    /**
     * 提交复查项
     * @throws \yii\db\Exception
     */
    public function actionPmReview()
    {
        $params = $this->validateRequest();
        if (empty($params['review_item'])) {
            return $this->outputJsonApp('false', '请选择复查项!');
        }
        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($params['review_item'] as $key => $value) {
            $pmCheck                = new InspectionPmCheck();
            $pmCheck->pm_id         = $params['pm_id'];
            $pmCheck->group_item_id = $value['group_item_id'];
            $pmCheck->check_time    = date('Y-m-d H:i:s', time());
            $pmCheck->type          = InspectionPmCheck::TYPE_REVIEW;
            $pmCheck->review_type   = ArrayHelper::getValue($value['review_type'], $key, '');
            $pmCheck->user_info_id  = $params['user_id'];
            if (!$pmCheck->save(false)) {
                $transaction->rollBack();
                return $this->outputJsonApp('false', '添加复查失败!');
            }
            foreach ($value['pic_id'] as $key_id => $value_id){
                $pmCheckPicture              = InspectionPmFile::findOne($value_id);
                $pmCheckPicture->pm_id       = $params['pm_id'];
                $pmCheckPicture->type        = InspectionPmCheck::TYPE_REVIEW;
                $pmCheckPicture->pm_check_id = $pmCheck->id;
                if (!$pmCheckPicture->save(false)) {
                    $transaction->rollBack();
                    return $this->outputJsonApp('false', '添加复查图片失败!');
                }
            }

        }

        $transaction->commit();
        return $this->outputJsonApp('true', '成功!');
    }

    /**
     * @return string
     */
    public function actionCamera()
    {
        if(!\Yii::$app->request->isPost){
            return $this->outputJsonApp('false', '请求方式不正确!');
        }
        $post       = \Yii::$app->request->post();
        $imgPicture = str_replace('data:image/png;base64,', '', $post['img_picture']);
        $imgPicture = str_replace(' ', '+', $imgPicture);
        $data       = base64_decode($imgPicture);
        $path       = 'uploads/inspection/' . date('Ymd') . '/camera/';
        if (!is_dir($path)) {
            mkdir($path, 0777, TRUE);
        }
        $fileName = time() . '.png';
        $filePath = $path . $fileName;
        $fp       = fopen($filePath, 'w');
        fwrite($fp, $data);
        fclose($fp);
        $post['file_path'] = '/' . $filePath;
        $fileId            = (new InspectionPmFile())->create($post);
        if (!$fileId) {
            return $this->outputJsonApp('false', '文件上传失败!');
        }
        return $this->outputJsonApp('true', '拍照完成！', ['file_id' => $fileId, 'img_url' => '/' . $filePath]);
    }

    /**
     * 获取检查内容
     */
    public function actionGetPmCheck(){
        $post       = \Yii::$app->request->post();
        $query = InspectionPmCheck::find();
        $query->andFilterWhere(['pm_id'=>$post['pm_id']]);
        $query->andFilterWhere(['type'=>InspectionPmCheck::TYPE_CHECK]);
        $query->asArray();
        $dataProvider = $query->all();

        $data = [];

        if (!empty($dataProvider)) {
            $data['pm_id'] = $dataProvider[0]['pm_id'];
            $data['check_time'] = $dataProvider[0]['check_time'];
            $data['user_info'] = (new UserInfo())->getUserName($dataProvider[0]['user_info_id']);
            foreach ($dataProvider as $key => $value){
                $data['group_item'][$key]['group_item_id']  = $value['group_item_id'];
                $data['group_item'][$key]['group_item_result']  = $value['group_item_result']?$value['group_item_result']:'2';
                $data['group_item'][$key]['group_item']     = (new InspectionPmGroupItem())->getItem($value['pm_id']);

                $query_pic = InspectionPmFile::find();
                $query_pic->andFilterWhere(['pm_check_id'=>$value['id']]);

                $data_query_pic = $query_pic->asArray()->all();

                if (!empty($data_query_pic)){
                    foreach ($data_query_pic as $key_pic => $value_pic){
                        $data['group_item'][$key]['group_item_pic'][$key_pic] = $value_pic['file_path'];
                    }
                }
            }
        }
        return $this->outputJsonApp('true', '查询成功', $data);

    }

    /**
     * 获取复查内容
     */
    public function actionGetPmReview(){
        $post       = \Yii::$app->request->post();
        $query = InspectionPmCheck::find();
        $query->andFilterWhere(['pm_id'=>$post['pm_id']]);
        $query->andFilterWhere(['type'=>InspectionPmCheck::TYPE_REVIEW]);
        $query->asArray();
        $dataProvider = $query->all();

        $data = [];

        if (!empty($dataProvider)) {
            $data['pm_id'] = $dataProvider[0]['pm_id'];
            $data['check_time'] = $dataProvider[0]['check_time'];
            $data['user_info'] = (new UserInfo())->getUserName($dataProvider[0]['user_info_id']);
            foreach ($dataProvider as $key => $value){
                $data['group_item'][$key]['group_item_id']  = $value['group_item_id'];
                $data['group_item'][$key]['group_item_result']  = $value['group_item_result']?$value['group_item_result']:'2';
                $data['group_item'][$key]['group_item']     = (new InspectionPmGroupItem())->getItem($value['pm_id']);

                $query_pic = InspectionPmFile::find();
                $query_pic->andFilterWhere(['pm_check_id'=>$value['id']]);

                $data_query_pic = $query_pic->asArray()->all();

                if (!empty($data_query_pic)){
                    foreach ($data_query_pic as $key_pic => $value_pic){
                        $data['group_item'][$key]['group_item_pic'][$key_pic] = $value_pic['file_path'];
                    }
                }
            }
        }
        return $this->outputJsonApp('true', '查询成功', $data);

    }

    /**
     * 获取PM内容
     */
    public function actionGetPmInfo(){
        $post       = \Yii::$app->request->post();
        $query = InspectionPmCheck::find();
        $query->andFilterWhere(['pm_id'=>$post['pm_id']]);
        $query->andFilterWhere(['type'=>$post['type']]);
        $query->asArray();
        $dataProvider = $query->all();

        $data = [];

        if (!empty($dataProvider)) {
            $data['pm_id'] = $dataProvider[0]['pm_id'];
            $data['check_time'] = $dataProvider[0]['check_time'];
            $data['user_info'] = (new UserInfo())->getUserName($dataProvider[0]['user_info_id']);
            foreach ($dataProvider as $key => $value){
                $data['group_item'][$key]['group_item_id']  = $value['group_item_id'];
                $data['group_item'][$key]['group_item_result']  = $value['group_item_result']?$value['group_item_result']:'2';
                $data['group_item'][$key]['group_item']     = (new InspectionPmGroupItem())->getItem($value['pm_id']);

                $query_pic = InspectionPmFile::find();
                $query_pic->andFilterWhere(['pm_check_id'=>$value['id']]);

                $data_query_pic = $query_pic->asArray()->all();

                if (!empty($data_query_pic)){
                    foreach ($data_query_pic as $key_pic => $value_pic){
                        $data['group_item'][$key]['group_item_pic'][$key_pic] = $value_pic['file_path'];
                    }
                }
            }
        }
        return $this->outputJsonApp('true', '查询成功', $data);

    }

    /**
     * 周PM列表
     */
    public function actionAllPmList()
    {
        $params = $this->validateRequest();
        $facility_id = $params['facility_id'];

        $queryPm = InspectionPm::find();
        $queryPm->andWhere(['facility_id'=>$facility_id]);
        $queryPm->andWhere(['type'=>1]);
        $queryPm->andWhere(['in','status',[1,3]]);
        $queryPm->orderBy('check_time desc');
        $queryPmData = $queryPm->asArray()->all();

        $data = [];

        if ($queryPmData){

        }
    }

    /**
     * 定时任务，生成每日检查项
     */
    public function actionEverydayCreatePmCheck()
    {
//        如果应检查时间是当前日期，则系统生成对应的检查项
        $query = InspectionPm::find();
        $query->andWhere(['status'=>1]);
        // $query->andWhere(['type'=>2]);
        // $query->andWhere(['check_time'=>'2022-10-06']);
        $query->andWhere(['check_time'=>date('Y-m-d', time())]);
        $queryData = $query->asArray()->all();
        
        if ($queryData){
            foreach ($queryData as $key=>$value){
//                获取对应的检查组
                $queryFacility = InspectionPmFacility::find();
                $queryFacility->andWhere(['facility_id'=>$value['facility_id']]);
                $queryFacilityData = $queryFacility->asArray()->all();

                $groupId = 1;
                foreach ($queryFacilityData as $valueGroup){
                    $groupId = $valueGroup['group_id'];
                }

                $queryGroupItem = InspectionPmGroupItem::find();
                $queryGroupItem->andWhere(['group_id'=>$groupId]);
                $queryGroupItem->andWhere(['item_status'=>1]);
                $queryGroupItemData = $queryGroupItem->asArray()->all();

                $checkInsert = InspectionPmCheck::find();
                $checkInsert->andWhere(['pm_id'=>$value['id']]);
                $checkInsertData = $checkInsert->asArray()->all();

                if (!$checkInsertData) {
                        
                    foreach ($queryGroupItemData as $valueGroupItem){
                        
                            # code...
                            $insert = new InspectionPmCheck();
                            $insert->pm_id = $value['id'];
                            $insert->group_item_id = $valueGroupItem['id'];
                            $insert->type = $value['type'];
                            $insert->created_at = time();
                            if ($value['type'] == 1)
                                $insert->user_info_id = $value['user_info_id'];
                            else
                                $insert->user_info_id = $value['review_user_info_id'];

                            $insert->save();
        
                        

                    }
                }

            }
        }
    }
    /**
     * 定时任务，检查是否逾期
     */
    public function actionCheckPmOverdue()
    {
//        判断复查，按照每月一号来检查
//        判断今天是否为一号
        
    //    print_r(date('Y-m-01', strtotime(date("Y-m-d"))));
    //    print_r(date('Y-m-d', time()));
        if (date('Y-m-01', strtotime(date("Y-m-d"))) == date('Y-m-d', time())){
            $query = InspectionPm::find();
            $query->andWhere(['status'=>1]);
            $query->andWhere(['type'=>2]);
            $queryData = $query->asArray()->all();

            if ($queryData){
                foreach ($queryData as $value){
                    $updateStatus = InspectionPm::findOne($value['id']);
                    $updateStatus->status = 3;
                    $updateStatus->save();
                }
            }
        }


//        判断周PM


        $queryRule = InspectionPmRule::find();
        // $queryRule->andWhere(['facility_id'=>17]);
        $queryRule->andWhere(['status'=>1]);
        $queryRule->andWhere(['type'=>1]);
        $queryRuleData = $queryRule->asArray()->all();


        if ($queryRuleData){
            foreach ($queryRuleData as $key=>$valueRule){
                $ruleDate = 0;
                if ($valueRule['cycle']){
                        $ruleDate = $valueRule['cycle'] / 10;
                }


                if ($ruleDate > 0){

                    $query = InspectionPm::find();
                    $query->andWhere(['facility_id'=>$valueRule['facility_id']]);
                    $query->andWhere(['type'=>1]);
                    // $query->andWhere(['<','check_time',date('Y-m-d', time())]);
                    $query->orderBy('id desc');
                    $query->limit(1);
                    $queryData = $query->asArray()->all();

                    if ($queryData){
                        foreach($queryData as $key=>$value){
                            // print_r(date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s', strtotime($value['check_time']))."+".$ruleDate." day")));
                            // print_r($ruleDate);
                            // die;

                            if (strtotime($value['check_time']) < strtotime("-".$ruleDate." day")){
                                if($value['status'] == 1){
                                    $updateStatus = InspectionPm::findOne($value['id']);
                                    $updateStatus->status = 3;
                                    $updateStatus->updated_at = time();
                                    $updateStatus->save();
                                }
                                
                                // 需要判断指定日期是否已存在
                                $checkInsert = InspectionPm::find();
                                $checkInsert->andWhere(['facility_id'=>$value['facility_id']]);
                                $checkInsert->andWhere(['type'=>$value['type']]);
                                $checkInsert->andWhere(['user_info_id'=>$valueRule['user_id']]);
                                // $checkInsert->andWhere(['user_info_id'=>$value['user_info_id']]);
                                $checkInsert->andWhere(['check_time'=>date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s', strtotime($value['check_time']))."+".($ruleDate)." day"))]);
                                $checkInsert->andWhere(['status'=>1]);
                                $checkInsert->andWhere(['deal_user_id'=>$value['deal_user_id']]);
                                $checkInsertData = $checkInsert->asArray()->all();
                                if(!$checkInsertData){
                                    
                                    $insert = new InspectionPm();
                                    $insert->facility_id = $value['facility_id'];
                                    $insert->type = $value['type'];
                                    $insert->user_info_id = $valueRule['user_id'];
                                    // $insert->user_info_id = $value['user_info_id'];
                                    $insert->check_time = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s', strtotime($value['check_time']))."+".($ruleDate)." day"));
                                    $insert->deal_user_id = $value['deal_user_id'];
                                    $insert->status = 1;
                                    $insert->created_at = time();
                                    $insert->save();
                                }
        
        
                            }
        
                        }
                    }
                }

            }
        }
		
		

//        判断每年跳日期
        $querySpecial = InspectionPmRuleSpecial::findOne(1);
        // print_r(abs((8+$querySpecial->day)));
        if ($querySpecial){
			            
            // 当前时间 - 指定时间 >= 8 + 制定跳转天数
            if (abs((time() - strtotime($querySpecial->date))/86400) < abs(8+$querySpecial->day)){
            // if ($querySpecial->date == date('Y-m-d', time())){
                $queryDouble = InspectionPm::find();
                $queryDouble->alias('ip');
                $queryDouble->select(['ip.*']);
                $queryDouble->leftJoin(InspectionPmRule::tableName() . ' ipr', 'ip.facility_id = ipr.facility_id');
                $queryDouble->andWhere(['ip.status'=>1]);
                $queryDouble->andWhere(['ip.type'=>1]);
                $queryDouble->andWhere(['check_time'=>date('Y-m-d', time())]);
                $queryDouble->andWhere(['>','created_at',strtotime(date('Y-m-d', time()))]);
                $queryDouble->andWhere(['ipr.cycle'=>80]);
                $queryDoubleData = $queryDouble->asArray()->all();

                foreach ($queryDoubleData as $valueDouble){
                    $queryChange = InspectionPm::findOne($valueDouble['id']);
                    $queryChange->check_time = date('Y-m-d H:i:s', strtotime("+".$querySpecial->day." day",strtotime($valueDouble['check_time'])));
                    $queryChange->save();
                }
            }
        }
    }

    /**
     * 添加一个接口用于跑数据
     */
    
    public function actionNewtest()
    {
        $queryDouble = InspectionPmRule::find();
        $queryDouble->andWhere(['in','facility_id',['8','246','40','41','42','43','6','44','45','46','13','47','48','49','14','51','52','212','252','9','10','84','232','7','82','83','53','171','114','173','25','115','165','172','221','169','170','17','118','122','168','219','167','113','119','222','117','123','120','220','112','250','28','166','26','100','101','107','19','102','197','103','104','106','156','157','158','159','150','151','152','153','154','162','163','192','178','179','193','194','195','187','132','188','133','134','136','137','138','139','140','141','142','143','144','189','225','226','190','191','59','60','61','20','62','63','64','65','67','68','69','70','71','72','21','73','74','75','76','77','78','22','79','80','213','214','81','215','228','229','177','176','174','148','128','127','149','224','217','129','125','124','130','131','126','16','30','31','29','32','35','34','37','181','36','39','24','180','211','182','231','230','25','116','33','26','249']]);
        $queryDoubleData = $queryDouble->asArray()->all();

        foreach ($queryDoubleData as $key => $value) {
            # code...

            $facility_info = Facility::findOne($value['facility_id']);

            $insert = new InspectionPm();
            $insert->facility_id = $value['facility_id'];
            $insert->type = 1;
            $insert->user_info_id = $value['user_id'];
            $insert->check_time = $value['begin_time'];
            $insert->deal_user_id = $facility_info->review_user;
            $insert->status = 1;
            $insert->save();
        }
        
    }

    /**
     * 获取最近一次的PM信息
     */
    
    public function actionGetPmUser()
    {
//        获取最近一次的PM信息
        $post       = \Yii::$app->request->post();
        $query = InspectionPm::findOne($post['pm_id']);

        if(!$query)
            return $this->outputJsonApp('false', '没有此PM信息!');

        $queryGet = InspectionPm::find();
        $queryGet->andWhere(['facility_id'=>$query->facility_id]);
        $queryGet->andWhere(['type'=>1]);
        $queryGet->andWhere(['status'=>2]);
        $queryGet->andWhere(['<','check_time',date('Y-m-d H:i:s', time())]);
        $queryGet->orderBy('real_check_time desc');
        $queryGet->limit(1);
        $queryGetData = $queryGet->asArray()->all();

//        print_r($queryGetData);die;

        $data = [];

        if ($queryGetData){
            $getUserInfo = UserInfo::findOne($queryGetData[0]['user_info_id']);

            $data['user_name'] = $getUserInfo->chinese_name;
            $data['time'] = $queryGetData[0]['real_check_time'];
        }else{
            $data['user_name'] = '';
            $data['time'] = '';
        }
        return $this->outputJsonApp('true', '查询成功', $data);

    }


}


