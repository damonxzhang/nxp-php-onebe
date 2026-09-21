<?php

namespace backend\controllers;

use backend\models\Shelf;
use backend\models\ShelfMaterials;
use backend\models\User;
use backend\models\UserInfo;
use backend\models\UserStation;
use common\models\SendMqtt;
use common\models\Utility;
use common\models\phpMQTT;
use Yii;
use yii\helpers\ArrayHelper;

class TestController extends BaseController
{
    /**
     * 测试邮箱
     * @return string
     */
    public function actionEmail()
    {
        if (Yii::$app->request->isPost) {
            $post   = Yii::$app->request->post();
            $result = $this->sendEmail($post['email'], $post['subject'], $post['body']);
            if ($result == 1) {
                return $this->json(200, '邮箱发送成功!');
            }
            return $this->json(100, '邮箱发送失败!');
        }
        return $this->render('email');
    }

    /**
     * @return string
     */
	public function actionMqtt()
    {
        if (Yii::$app->request->isPost) {
            $post   = Yii::$app->request->post();
            $sendMqtt   = new SendMqtt();
			$sendMqtt->publishMessage(1, $post['number'], $post['status']);
            return $this->json(200, '发送成功!');
        }
        return $this->render('mqtt');
    }
	
	
    /**
     * @return string
     * @throws \yii\base\Exception
     */
    public function actionImport()
    {
        if (Yii::$app->request->isPost) {
            $file = $_FILES['file'];
            if (empty($file['tmp_name'])) {
                return $this->json(100, '上传文件不能为空!');
            }

            $tmp    = explode('.', $file['name']);
            $suffix = array_pop($tmp);
            if ('xlsx' !== $suffix) {
                return $this->json(100, '上传文件无法识别，请使用模版!');
            }

            if ($file['size'] > 2097152) {
                return $this->json(100, '上传文件不能超过2MB!');
            }
            $excelData = Utility::getExcelData($file['tmp_name']);
            if (empty($excelData)) {
                return $this->json(100, '上传文件无内容!');
            }
            $return = [
                'successCount' => 0,
                'errorCount'   => 0,
                'errorList'    => '',
            ];

            $errorList = [];
            foreach ($excelData as $key => $item) {
                if (empty($item[0])) break;
                $data  = $this->keyValue($item);
                $model = new UserInfo();
                $res   = $model->create($data);
                if ($res['status'] != 200) {
                    $errorList[] = $item[0];
                    $return['errorCount']++;
                } else {
                    $return['successCount']++;
                }
                //添加密码
                if (!empty($data['english_name'])) {
                    $userData = ['name' => $data['english_name'], 'password' => '123456', 'user_id' => $res['msg']];
                    (new User())->create($userData);
                }
            }
            $return['errorList'] = join("<br>", $errorList);
            return $this->json(200, '上传完成!', $return);
        }
        return $this->render('import');
    }

    /**
     * 数据上传
     * @param $item
     * @return mixed
     */
    public function keyValue($item)
    {
        $data['chinese_name']      = trim($item[0]);
        $data['section']           = ArrayHelper::getValue(self::$sectionArr, trim($item[1]), '');
        $data['meal_card_code']    = trim($item[2]);
        $data['sex']               = ArrayHelper::getValue(self::$sexArr, trim($item[3]), '');
        $data['status']            = ArrayHelper::getValue(self::$statusArr, trim($item[4]), '');
        $data['shift']             = ArrayHelper::getValue(self::$shiftArr, trim($item[5]), '');
        $data['leader_uid']        = $this->getId($item[6]);
        $data['area']              = ArrayHelper::getValue(self::$areaArr, trim($item[7]), '');
        $data['station_id']        = $this->getStation($item[8]);
        $data['tel']               = trim($item[9]);
        $data['email']             = trim($item[10]);
        $data['pid']               = trim($item[11]);
        $data['sfc_number']        = trim($item[12]);
        $data['fsl_number']        = trim($item[13]);
        $data['wbi_code']          = trim($item[14]);
        $data['department_number'] = trim($item[15]);
        $data['position']          = ArrayHelper::getValue(self::$positionArr, trim($item[16]), '');
        $data['english_name']      = trim($item[17]);
        $data['skill']             = $this->getStation($item[18]);
        $data['role_id']           = 2;
        $data['badge_code']        = '';
        return $data;
    }

    /**
     * 获取直属领导id
     * @param $name
     * @return mixed|string
     */
    public function getId($name)
    {
        $model = UserInfo::findOne(['chinese_name' => trim($name)]);
        return $model ? $model->id : '';
    }

    /**
     * 获取站别
     * @param $station
     * @return mixed|string
     */
    public function getStation($station)
    {
        $model = UserStation::findOne(['station' => trim($station)]);
        return $model ? $model->id : '';
    }


    const STATUS_OPEN  = 1;
    const STATUS_CLOSE = 2;

    //启用状态
    public static $statusArr = [
        '启用' => self::STATUS_OPEN,
        '禁用' => self::STATUS_CLOSE,
    ];

    const SEX_MAN   = 1;
    const SEX_WOMAN = 2;

    //性别
    public static $sexArr = [
        '男' => self::SEX_MAN,
        '女' => self::SEX_WOMAN,
    ];

    const SHIFT_A  = 1; //A班
    const SHIFT_B  = 2; //B班
    const SHIFT_C  = 3; //C班
    const SHIFT_D  = 4; //D班
    const SHIFT_R  = 5; //R班
    const SHIFT_AB = 6; //AB班
    const SHIFT_CD = 7; //CD班

    //班别
    public static $shiftArr = [
        'A'  => self::SHIFT_A,
        'B'  => self::SHIFT_B,
        'C'  => self::SHIFT_C,
        'D'  => self::SHIFT_D,
        'R'  => self::SHIFT_R,
        'AB' => self::SHIFT_AB,
        'CD' => self::SHIFT_CD,
    ];

    const AREA_NON     = 1; //Non-lead
    const AREA_LEADED  = 2; //Leaded
    const AREA_PLATING = 3; //Plating

    //区域
    public static $areaArr = [
        'Non-Lead' => '1',
        'Leaded'   => '2',
        'Plating'  => '3',
    ];

    const SECTION_NON     = 1; //Non-lead
    const SECTION_LEADED  = 2; //Leaded
    const SECTION_PM      = 3; //Leaded
    const SECTION_PLATING = 4; //Plating

    //所属分部
    public static $sectionArr = [
        'Non-Lead' => '1',
        'Leaded'   => '2',
        'PM'       => '3',
        'Plating'  => '4',
    ];

    const POSITION_OPERATOR       = 1; //操作员
    const POSITION_TAKE           = 2; //带班
    const POSITION_PRODUCT        = 3; //产品工程师
    const POSITION_DEVICE         = 4; //设备工程师
    const POSITION_PM             = 5; //PM技术员
    const POSITION_PM_LEADER      = 6; //PM leader
    const POSITION_PRODUCE_LEADER = 7; //生产 leader
    const POSITION_SUB            = 8; //分部经理
    const POSITION_DEPARTMENT     = 9; //部门经理
    const POSITION_CHEMISTRY      = 10;//化学分析
    const POSITION_QA             = 11;//Inline QA
    const POSITION_CHEMISTRY_ENG  = 12;//化学工程师
    const POSITION_TRAINER        = 13;//培训员
    const POSITION_CLERK          = 14;//文员
    const POSITION_PROCESS        = 15;//工艺工程师

    public static $positionArr = [
        '操作员'       => self::POSITION_OPERATOR,
        '带班'        => self::POSITION_TAKE,
        '产品工程师'     => self::POSITION_PRODUCT,
        '设备工程师'     => self::POSITION_DEVICE,
        'PM技术员'     => self::POSITION_PM,
        'PM leader' => self::POSITION_PM_LEADER,
        '生产leader'  => self::POSITION_PRODUCE_LEADER,
        '分部经理'      => self::POSITION_SUB,
        '部门经理'      => self::POSITION_DEPARTMENT,
        '化学分析'      => self::POSITION_CHEMISTRY,
        'Inline QA' => self::POSITION_QA,
        '化学工程师'     => self::POSITION_CHEMISTRY_ENG,
        '培训员'       => self::POSITION_TRAINER,
        '文员'        => self::POSITION_CLERK,
        '工艺工程师'     => self::POSITION_PROCESS,
    ];

}
