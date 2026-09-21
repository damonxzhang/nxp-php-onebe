<?php

namespace frontend\controllers;

use backend\models\ExternalRelation;
use backend\models\Facility;
use backend\models\FacilityUser;
use backend\models\Inspection;
use backend\models\InspectionContent;
use backend\models\InspectionFile;
use backend\models\InspectionPm;
use backend\models\InspectionPosition;
use backend\models\InspectionQuestionType;
use backend\models\InspectionRevelantPeople;
use backend\models\InspectionService;
use backend\models\InspectionServiceSparepart;
use backend\models\UserInfo;
use Codeception\Util\JsonArrayTest;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use backend\models\InspectionFeedback;

class InspectionApiController extends FrontendBaseController
{
    public $enableCsrfValidation = false;
    /*
     * 首页 - 待处理设备问题列表
     */
    public function actionPendingInspection(){

        $params = $this->validateRequest();

        $query = Inspection::find();
        $query->alias('i');
        $query->select(['f.*','i.user_info_id as user_info_id','f.head_user as head_user','i.id as i_id','i.status as i_status','i.repair_time as i_repair_time','i.question_type as i_question_type']);
        $query->leftJoin(Facility::tableName() . ' f', 'i.facility_id = f.id');
        $query->leftJoin(InspectionRevelantPeople::tableName() . ' irp', 'i.id = irp.inspection_id');
        $query->andFilterWhere(['irp.revelant_people'=>$params['user_id']]);
        $query->andFilterWhere(['<>', 'i.status', 1]);
        $query->asArray();

        $dataProvider = $query->all();

//        echo $query->createCommand()->getRawSql();
//        die;
        $data = [];

        if (!empty($dataProvider)) {
            foreach ($dataProvider as $key => $value) {
                $data[$key]['name']                          = $value['name'];
                $data[$key]['number']                        = $value['number'];
                $data[$key]['status']                        = $value['status'];
                $data[$key]['is_ok']                         = $value['is_ok'];
                $data[$key]['id']                            = $value['id'];
                $data[$key]['head_user']                     = (new UserInfo())->getUserName($value['head_user']);
                $data[$key]['user_info_id']                  = (new UserInfo())->getUserName($value['user_info_id']);
                $data[$key]['inspection_id']                 = $value['i_id'];
                $data[$key]['process']                       = ArrayHelper::getValue(Facility::$processList, $value['process'], '');
                $data[$key]['inspection_status']             = ArrayHelper::getValue(Inspection::$statusList, $value['i_status'], '');
                $data[$key]['inspection_repair_time']        = $value['i_repair_time'];
                $data[$key]['inspection_question_type']      = $this->getQuestionType($value['i_question_type']);
            }
        }

        return $this->outputJsonApp('true','查询成功',$data);

    }

    /**
     * 查询设备列表
     */
    public function actionFacilityList()
    {
        $params = $this->validateRequest();
        $pageSize        = $params['page'] ? $params['page'] : 0;
        $defaultPageSize = $params['default_page'] ? $params['default_page'] : 10;
        $query = Facility::find();
        $query->alias('f');
        $query->select(['f.*','i.id as i_id','i.status as i_status','i.repair_time as i_repair_time','i.question_type as i_question_type']);
        $query->leftJoin(Inspection::tableName() . ' i', 'i.facility_id = f.id');
        $query->andFilterWhere(['=', 'f.process', $params['process']]);
        if ($params['status'] != "1"){
            $query->andFilterWhere(['=', 'i.status', $params['status']]);
        }
        else{
            //获取有问题设备ID列表
            $query_FList = Inspection::find();
            $query_FList->andWhere(['in','status',[0,2,3]]);
            $data_FList = $query_FList->asArray()->all();

            $temp = [];

            foreach ($data_FList as $key=>$value){
                $temp[$key] = $value['facility_id'];
            }

            $query->andFilterWhere(['not in', 'f.id', $temp]);

        }

        if (!empty($params['name'])){
            $query->andWhere(['or', ['like','f.name', $params['name']],['like', 'f.number', $params['name']]]);
        }

        $pagination = new Pagination([
//            'defaultPageSize' => $defaultPageSize,
            'pageSize'   => $defaultPageSize,
            'page'   => $pageSize,
            'totalCount'      => $query->count(),
        ]);
        $query->offset($pagination->offset);
        $query->limit($pagination->limit);
        $dataProvider = $query->asArray()->all();

        // 打印 SQL 语句
//        echo $query->createCommand()->getRawSql();
//        die;

        $data = [];

        if (!empty($dataProvider)) {
            foreach ($dataProvider as $key => $value) {
                $data[$key]['name']                 = $value['name'];
                $data[$key]['number']               = $value['number'];
                $data[$key]['head_user']            = (new UserInfo())->getUserName($value['head_user']);
                $data[$key]['status']               = $value['status'];
                $data[$key]['is_ok']                = $value['is_ok'];
                $data[$key]['id']                   = $value['id'];
                $data[$key]['inspection_id']                 = $value['i_id'];
                $data[$key]['process']              = ArrayHelper::getValue(Facility::$processList, $value['process'], '');
                $data[$key]['inspection_status']             = ArrayHelper::getValue(Inspection::$statusList, $value['i_status'], '正常');
                $data[$key]['inspection_repair_time']        = $value['i_repair_time'];
                $data[$key]['inspection_question_type']      = $this->getQuestionType($value['i_question_type']);
            }
        }

        return $this->outputJsonApp('true','查询成功',$data);

    }

    /**
     * 工序列表
     */
    public function actionGetProcess(){
        $query = Facility::$processList;

//        $data['process_list'] = $query;
        $i =0;
        foreach ($query as $key=>$value){
            $data['process_list'][$i] = $value;
            $i++;
        }

        return $this->outputJson('true','查询成功',$data);

    }

    /*
     * 设备详情接口
     */
    public function actionFacility()
    {
        $params = $this->validateRequest();

        $query = Facility::find();
        $query->andFilterWhere(['id' => $params['facility_id']]);
        $query->andFilterWhere(['number' => $params['facility_number']]);
        $dataProvider = $query->all();

        $data = [];

        if (!empty($dataProvider)) {
            foreach ($dataProvider as $key => $value) {
                $data[$key]['name']                 = $value['name'];
                $data[$key]['number']               = $value['number'];
                $data[$key]['create_user']          = (new UserInfo())->getUserName($value['create_user']);
                $data[$key]['model']                = $value['model'];
                $data[$key]['area']                 = $value['area'];
                $data[$key]['shift']                = $value['shift'];
                $data[$key]['serial_no']            = $value['serial_no'];
                $data[$key]['vendor_id']            = $value['vendor_id'];
                $data[$key]['ASSET_NO']             = $value['ASSET_NO'];
                $data[$key]['BUY_OFF_DATE']         = date('Y-m-d H:i:s',($value['BUY_OFF_DATE']));
                $data[$key]['CBA_pass_date']        = date('Y-m-d H:i:s',($value['CBA_pass_date']));
                $data[$key]['head_user']            = (new UserInfo())->getUserName($value['head_user']);
                $data[$key]['relevant_people']      = $this->addUserName($value['relevant_people']);;
                $data[$key]['status']               = $value['status'];
                $data[$key]['is_ok']                = $value['is_ok'];
                $data[$key]['file']                 = trim($value['file']);
                $data[$key]['create_time']          = date('Y-m-d H:i:s',($value['create_time']));
                $data[$key]['update_time']          = date('Y-m-d H:i:s',($value['update_time']));
                $data[$key]['delete_time']          = date('Y-m-d H:i:s',($value['delete_time']));
                $data[$key]['id']                   = $value['id'];
                $data[$key]['process']              = ArrayHelper::getValue(Facility::$processList, $value['process'], '');


                //查找有没有报修信息
                $query_check = Inspection::find();
                $query_check->andFilterWhere(['facility_id'=>$params['facility_id']]);
                $query_check->andWhere(['in','status',[0,2,3]]);
                $data_check = $query_check->asArray()->one();

                if (!empty($data_check)){
//                    $temp = [];
//                    foreach ($data_check as $key=>$value){
//                        $temp[$key] = $value['id'];
//                    }
                    $data[$key]['i_id'] = $data_check['id'];
                    $data[$key]['i_status'] = $data_check['status'];
                }else{
                    $data[$key]['i_id'] = '';
                    $data[$key]['i_status'] = '1';

                }

            }
        }

        return $this->outputJsonApp('true','查询成功',$data);
    }

    /**
     * 给USERID加路由
     * @param array $array
     * @param array $param
     * @return array|mixed
     */
    public function addUserName($param)
    {
        $result = [];
        if (empty($param)) return $result;
        $urlLink = \Yii::$app->request->getHostInfo();//获取路由
//        $data = Json::decode($param);
        $data = explode(',',$param);
        if ($data){
            foreach ($data as $id) {
                $file = UserInfo::findOne(['id' => $id]);
                if ($file){
                    $result[] = $file->chinese_name;
                }
            }
        }
        return $result;
    }

    /**
     * 根据用户ID获取问题内容
     * @param $id
     * @return string
     */
    public function getQuestionType($id)
    {
        if(empty($id)){
            return '';
        }
        $query = InspectionQuestionType::findOne(['id' => $id]);
        return empty($query) ? '' : $query->name;
    }

    /*
     * 报修问题类型
     */
    public function actionInspectionQuestionType(){

        $query = InspectionQuestionType::find();
        $query->where(['status'=>1]);
        $query->asArray();

        $dataProvider = $query->all();

        $data = [];

        if (!empty($dataProvider)) {
            foreach ($dataProvider as $key => $value) {
                $data[$key]['id']                   = $value['id'];
                $data[$key]['name']                 = trim($value['name']);
            }
        }

        return $this->outputJsonApp('true','查询成功',$data);

    }


    /*
     * 提交报修信息
     */
    public function actionInspectionCreate(){

        $params = $this->validateRequest();

        //判断，已报修设备不可再次报修
        $query_check = Inspection::find();
        $query_check->andFilterWhere(['facility_id'=>$params['facility_id']]);
        $query_check->andFilterWhere(['in','status',[0,2,3]]);
        $data_check = $query_check->asArray()->all();

        if (!empty($data_check))
            return $this->outputJsonApp('false','该设备已报修');


        $relevant_people = Facility::findOne(['id'=>$params['facility_id']]);

        if (empty($relevant_people))
            return $this->outputJsonApp('false','设备没有相关人，不能提交报修');

        $insert = [
            'facility_id'           =>$params['facility_id'],
            'repair_time'           =>date('Y-m-d H:i:s',$params['repair_time']),
            'explanation'           =>$params['explanation'],
            'created_at'            =>time(),
            'question_type'         =>$params['question_type'],
            'user_info_id'          =>$params['user_id'],
            'part_no'               =>$params['part_no'],
            'repair_file_id'        =>Json::encode($params['pic_id']),
            'status'                =>0,
            'relevant_people'       =>$relevant_people->relevant_people
        ] ;

        $createInsp = new Inspection();
        $createInsp->attributes = $insert;
        if(!$createInsp->save(false)){
			return $this->outputJsonApp('false','添加失败');
        }

        // 添加相关人表
        $temp1 = Json::decode($relevant_people->relevant_people);

        if (is_array($temp1)){
            foreach ($temp1 as $value){
                $createRelevant = new InspectionRevelantPeople();
                $createRelevant->facility_id = $params['facility_id'];
                $createRelevant->inspection_id = $createInsp->id;
                $createRelevant->revelant_people = $value;
                $createRelevant->save();

            }
        }


		return $this->outputJsonApp('true','添加成功');

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
        $path       = 'uploads/APP/' . date('Ymd') . '/camera/';
        if (!is_dir($path)) {
            mkdir($path, 0777, TRUE);
        }
        $fileName = time() . '.png';
        $filePath = $path . $fileName;
        $fp       = fopen($filePath, 'w');
        fwrite($fp, $data);
        fclose($fp);
        $post['file_path'] = '/' . $filePath;
        $fileId            = (new InspectionFile())->create($post);
        if (!$fileId) {
            return $this->outputJsonApp('false', '文件上传失败!');
        }
        return $this->outputJsonApp('true', '拍照完成！', ['file_id' => $fileId, 'img_url' => '/' . $filePath]);
    }


    /*
    * 查询报修详情信息
     */
    public function actionInspectionInfo(){

        $params = $this->validateRequest();

        $query = Inspection::find();
        $query->alias('i');
        $query->select(['i.*','f.name as f_name','f.head_user as f_head_user']);
        $query->leftJoin(Facility::tableName() . ' f', 'i.facility_id = f.id');
        $query->andFilterWhere(['=', 'i.id', $params['inspection_id']]);
        $query->asArray();//这句是说将查询的模型转成数组（不加这句打印的是模型形式）这句加上就行
        $dataProvider = $query->all();

        // 打印 SQL 语句
//        echo $query->createCommand()->getRawSql();
//        die;

        $data = [];

        if (!empty($dataProvider)) {
            foreach ($dataProvider as $key => $value) {
                $data[$key]['id']                   = trim($value['id']);
                $data[$key]['facility_id']          = trim($value['facility_id']);
                $data[$key]['facility_name']        = trim($value['f_name']);
                $data[$key]['part_no']              = trim($value['part_no']);
                $data[$key]['relevant_people']      = $this->addUserName($value['relevant_people']);;
                $data[$key]['explanation']          = trim($value['explanation']);
                $data[$key]['head_user']            = (new UserInfo())->getUserName($value['f_head_user']);
                $data[$key]['repair_time']          = $value['repair_time'];
                $data[$key]['repair_file_id']       = $this->addUrlImage($value['repair_file_id']);

            }
        }

        return $this->outputJsonApp('true','查询成功',$data);

    }


    /**
     * 给图片加路由
     * @param array $array
     * @param array $param
     * @return array|mixed
     */
    public function addUrlImage($param)
    {
        $result = [];
        if (empty($param)) return $result;
        $urlLink = \Yii::$app->request->getHostInfo();//获取路由
        $data = Json::decode($param);
        if ($data){
            foreach ($data as $id) {
                $file = InspectionFile::findOne(['id' => $id]);
                if ($file){
                    $result[] = $urlLink .'/api'. $file->file_path;
                }
            }
        }
        return $result;
    }


    /*
     * 查询报修列表信息
     */
    public function actionInspectionList(){

        $params = $this->validateRequest();

        $query = Inspection::find();
        $query->alias('i');
        $query->select(['i.*','f.name as f_name','f.number as f_number','f.head_user as f_head_user']);
        $query->leftJoin(Facility::tableName() . ' f', 'i.facility_id = f.id');
        $query->andFilterWhere(['>=', 'i.repair_time', $params['repair_start_time']]);
        $query->andFilterWhere(['<=', 'i.repair_time', $params['repair_end_time']]);
        $query->andFilterWhere(['=', 'f.id', $params['facility_id']]);
        $query->asArray();
        $dataProvider = $query->all();

        // 打印 SQL 语句
//        echo $query->createCommand()->getRawSql();
//        die;

        $data = [];

        if (!empty($dataProvider)) {
            foreach ($dataProvider as $key => $value) {
                $data[$key]['id']                   = $value['id'];
                $data[$key]['facility_id']          = $value['facility_id'];
                $data[$key]['facility_name']        = $value['f_name'];
                $data[$key]['facility_number']        = $value['f_number'];
                $data[$key]['head_user']            = (new UserInfo())->getUserName($value['f_head_user']);
                $data[$key]['relevant_people']      = (new UserInfo())->getUserName($value['relevant_people']);
                $data[$key]['repair_time']          = $value['repair_time'];
                $data[$key]['question_type_id']        = $value['question_type'] ;
                $data[$key]['question_type_name']        = InspectionQuestionType::findOne($value['question_type'])->name ;
                $data[$key]['status']               = ArrayHelper::getValue(Inspection::$statusList, $value['status'], '');
            }
        }

        return $this->outputJsonApp('true','查询成功',$data);

    }

    /*
     * 无需维修 - 修改维修状态
     */
    public function actionInspectionServiceUpdate(){
		
		$params = $this->validateRequest();
		
		$CheckModel 			= Inspection::findOne($params['inspection_id']);
        $CheckModel->status   	= 1;
        if (!$CheckModel->save()) {
            return $this->outputJsonApp('false','更新失败');
        }
		return $this->outputJsonApp('true','更新成功');
		
	}

    /*
     * 提交维修信息
     */
    public function actionInspectionServiceCreate(){
		
        $params = $this->validateRequest();

        $insert = [
            'inspection_id'=>$params['inspection_id'],
            'content_id'=>$params['content_id'],
            'position_id'=>$params['position_id'],
            'user_info_id'=>$params['user_id'],
            'description'=>$params['description'],
            'created_at'=>date('Y-m-d H:i:s',time()),
            'service_time'=>date('Y-m-d H:i:s',time()),
            'file_id'=>Json::encode($params['pic_id']),
            'status'=>$params['status'],
            'facility_id'=>$params['facility_id'],

        ] ;

        $createInsp = new InspectionService();
        $createInsp->attributes = $insert;
        if(!$createInsp->save(false)){
			return $this->outputJsonApp('false','添加失败');
        }

        //存备件列表
        if (!empty($params['sparepart_list'])){
            foreach ($params['sparepart_list'] as $key => $value){
                $createSpareList = new InspectionServiceSparepart();
                $createSpareList->inspection_service_id = $createInsp->id;
                $createSpareList->s_id = $value['s_id'];
                $createSpareList->e_id = $value['e_id'];
                $createSpareList->o_amount = $value['o_amount'];
                if(!$createSpareList->save(false)){
                    return $this->outputJsonApp('false','添加失败');
                }
            }
        }

        if ($params['status'] == "1"){
            $query = Inspection::findOne($params['inspection_id']);
            $query->status = 1;
            $query->save();
        }

		return $this->outputJsonApp('true','添加成功');

    }

    /*
     * 查看维修详情
     *
     */
    public function actionInspectionServiceInfo(){

        $params = $this->validateRequest();

        $query = InspectionService::find();
        $query->alias('is');
        $query->select([
			'is.*',
			'ip.position as ip_position',
			'ic.content as ic_content',
			'f.name as f_name',
			'f.head_user as f_head_user'
		]);
        $query->leftJoin(Facility::tableName() . ' f', 'is.facility_id = f.id');
        $query->leftJoin(InspectionPosition::tableName() . ' ip', 'is.inspection_id = ip.id');
        $query->leftJoin(InspectionContent::tableName() . ' ic', 'is.content_id = ic.id');
        $query->andFilterWhere(['=', 'is.id', $params['inspection_service_id']]);
        $query->asArray();
        $dataProvider = $query->all();

        $data = [];

        if (!empty($dataProvider)) {
            foreach ($dataProvider as $key => $value) {
                $data[$key]['id']                   = $value['id'];
                $data[$key]['facility_id']          = $value['facility_id'];
                $data[$key]['facility_name']        = $value['f_name'];
                $data[$key]['position_id']          = $value['position_id'];
                $data[$key]['position']        		= $value['ip_position'];
                $data[$key]['content_id']           = $value['content_id'];
                $data[$key]['content']        		= $value['ic_content'];
                $data[$key]['explanation']          = $value['description'];
                $data[$key]['head_user']            = (new UserInfo())->getUserName($value['f_head_user']);
                $data[$key]['service_time']         = $value['service_time'];
                $data[$key]['end_time']             = $value['end_time'] ? date('Y-m-d H:i:s',($value['end_time'])) :'';
                $data[$key]['service_file_id']      = $this->addUrlImage($value['file_id']);

            }
        }

        return $this->outputJsonApp('true','查询成功',$data);


    }


    /*
     * 查询维修列表信息
     */
    public function actionInspectionServiceList(){

        $params = $this->validateRequest();

        $query = InspectionService::find();
        $query->alias('is');
        $query->select([
            'is.*',
            'f.name as f_name',
            'f.number as f_number',
            'f.head_user as f_head_user',
            'f.process as f_process',
            'i.repair_time as i_repair_time'
        ]);
        $query->leftJoin(Facility::tableName() . ' f', 'is.facility_id = f.id');
        $query->leftJoin(Inspection::tableName() . ' i', 'is.inspection_id = i.id');
        $query->andFilterWhere(['=', 'f.id', $params['facility_id']]);
        $query->asArray();
        $dataProvider = $query->all();

        // 打印 SQL 语句
//        echo $query->createCommand()->getRawSql();
//        die;

        $data = [];

        if (!empty($dataProvider)) {
            foreach ($dataProvider as $key => $value) {
                $data[$key]['id']                   = $value['id'];
                $data[$key]['facility_id']          = $value['facility_id'];
                $data[$key]['facility_name']        = $value['f_name'];
                $data[$key]['facility_number']        = $value['f_number'];
                $data[$key]['head_user']            = (new UserInfo())->getUserName($value['f_head_user']);
                $data[$key]['repair_time']          = $value['i_repair_time'];
                $data[$key]['status']               = ArrayHelper::getValue(InspectionService::$statusList, $value['status'], '');
                $data[$key]['process']              = ArrayHelper::getValue(Facility::$processList, $value['f_process'], '');
            }
        }

        return $this->outputJsonApp('true','查询成功',$data);

    }

    /*
     * 待我处理的周PM检查列表
     */
    public function actionPmList(){

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
        $query->andFilterWhere(['f.process'=>$params['process']]);

        if ($params['status'] == ''){
            $query->where(['<>','ip.status',2]);
            $query->where(['deal_user_id'=>$params['user_id']]);
        }else{
            $query->where(['ip.status'=>$params['status']]);

            switch ($params['status']){
                case 0:
                    $query->andFilterWhere(['ip.user_info_id'=>$params['user_id']]);
                    break;
                case 1:
                    $query->andFilterWhere(['f.head_user'=>$params['user_id']]);
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
                $data[$key]['id']                   = $value['id'];
                $data[$key]['facility_id']          = $value['facility_id'];
                $data[$key]['facility_name']        = $value['f_name'];
                $data[$key]['head_user']            = (new UserInfo())->getUserName($value['f_head_user']);
                $data[$key]['repair_time']          = date('Y-m-d H:i:s',($value['check_time']));
                $data[$key]['status']               = ArrayHelper::getValue(InspectionPm::$statusList, $value['status'], '');
                $data[$key]['process']              = ArrayHelper::getValue(Facility::$processList, $value['f_process'], '');
            }
        }

        return $this->outputJsonApp('true','查询成功',$data);
    }

    /**
     * 位置列表
     */
    public function actionGetPosition(){

        $query = InspectionPosition::find();
        $query->where(['status'=>1]);
        $query->asArray();

        $dataProvider = $query->all();

        $data = [];

        if (!empty($dataProvider)) {
            foreach ($dataProvider as $key => $value) {
                $data[$key]['id']                   = $value['id'];
                $data[$key]['name']                 = trim($value['position']);
            }
        }

        return $this->outputJsonApp('true','查询成功',$data);
    }

    /**
     * 内容列表
     */
    public function actionGetContent(){

        $params = $this->validateRequest();

        $query = InspectionContent::find();
        $query->where(['status'=>1]);
        $query->andFilterWhere(['position_id'=>$params['position_id']]);
        $query->asArray();

        $dataProvider = $query->all();

        $data = [];

//        print_r($dataProvider);die;
        if (!empty($dataProvider)) {
            foreach ($dataProvider as $key => $value) {
                $data[$key]['id']                   = $value['id'];
                $data[$key]['name']                 = trim($value['content']);
            }
        }

        return $this->outputJsonApp('true','查询成功',$data);
    }

    /**
     * 提交IR
     */
    public function actionSubmitIr(){

        $params = $this->validateRequest();

        $inspection_id = $params['inspection_id'];


    }


    /**
     * 判断是否有权限操作
     */

    public function actionCheckRule()
    {
        $params = $this->validateRequest();
        $inspection_id = $params['inspection_id'];
        $facility_id = $params['facility_id'];
        $user_id = $params['user_id'];
        $operate = $params['operate'];// 0:维修；3:待确认

        if ($operate == 0){
            $query0 = FacilityUser::find();
            $query0->andWhere(['facility_id'=>$facility_id]);
            $query0->andWhere(['user_id'=>$user_id]);
            $query0Data = $query0->asArray()->all();

            if ($query0Data)
                return $this->outputJsonApp('true','成功');
            else
                return $this->outputJsonApp('false','无权限');

        }

        if ($operate == 3){
//            获取报修人ID
            $query3 = Inspection::findOne($inspection_id);
            $create_user_id = $query3->user_info_id;
            if (!$create_user_id)
                return $this->outputJsonApp('false','报修人员有误');

            $queryE1 = ExternalRelation::find();
            $queryE1->andWhere(['user_id'=>$create_user_id]);
            $queryE1->andWhere(['system_id'=>3]);
            $queryE1Data = $queryE1->asArray()->all();

            $queryE2 = ExternalRelation::find();
            $queryE2->andWhere(['user_id'=>$user_id]);
            $queryE2->andWhere(['system_id'=>3]);
            $queryE2Data = $queryE2->asArray()->all();

            foreach ($queryE1Data as $key1=>$value1){
                foreach ($queryE2Data as $key2=>$value2){
                    if ($value1['authority_id'] == $value2['authority_id']){
                        return $this->outputJsonApp('true','成功');
                    }
                }
            }

            return $this->outputJsonApp('false','无权限');


        }


    }
	
	/**
     * 意见反馈
     */
    public function actionFeedback(){
        $params = $this->validateRequest();

        $user_id = $params['user_id'];
        $feedback = $params['feedback'];

        $query = new InspectionFeedback();
        $query->status = 1;
        $query->feedback = $feedback;
        $query->user_id = $user_id;
        if ($query->save(false))
            return $this->outputJsonApp('true','成功');
        else
            return $this->outputJsonApp('false','失败');

    }
	
}


