<?php

namespace backend\models;

use common\models\Utility;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "nxp_user_info".
 *
 * @property int $id
 * @property int $leader_uid
 * @property int $role_id
 * @property int $sex
 * @property int $status
 * @property int $position
 * @property int $shift
 * @property int $station_id
 * @property string $email
 * @property string $pid
 * @property string $tel
 * @property string $chinese_name
 * @property string $english_name
 * @property string $area
 * @property string $sfc_number
 * @property string $fsl_number
 * @property string $meal_card_code
 * @property string $wbi_code
 * @property string $badge_code
 * @property string $skill
 * @property string $department_number
 * @property string $section
 * @property string $create_time
 * @property string $update_time
 * @property string $shoe_type
 */
class UserInfo extends Base
{
    const STATUS_OPEN  = 1;
    const STATUS_CLOSE = 2;

    //启用状态
    public static $statusArr = [
        self::STATUS_OPEN  => '启用',
        self::STATUS_CLOSE => '禁用',
    ];

    const SHOE_TYPE_YES  = 1;
    const SHOE_TYPE_NO = 2;

    //是否校验洗鞋机
    public static $shoeArr = [
        self::SHOE_TYPE_YES  => '需要',
        self::SHOE_TYPE_NO => '不需要',
    ];

    const SEX_MAN   = 1;
    const SEX_WOMAN = 2;

    //性别
    public static $sexArr = [
        self::SEX_MAN   => '男',
        self::SEX_WOMAN => '女',
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
        self::SHIFT_A  => 'A班',
        self::SHIFT_B  => 'B班',
        self::SHIFT_C  => 'C班',
        self::SHIFT_D  => 'D班',
        self::SHIFT_R  => 'R班',
        self::SHIFT_AB => 'AB班',
        self::SHIFT_CD => 'CD班',
    ];

     //班别
    public static $shiftListArr = [
        self::SHIFT_A  => 'A',
        self::SHIFT_B  => 'B',
        self::SHIFT_C  => 'C',
        self::SHIFT_D  => 'D',
        self::SHIFT_R  => 'R',
        self::SHIFT_AB => 'AB',
        self::SHIFT_CD => 'CD',
    ];

    const AREA_NON     = 1; //Non-lead
    const AREA_LEADED  = 2; //Leaded
    const AREA_PLATING = 3; //Plating
    const AREA_PM = 4; //PM

    //区域
    public static $areaArr = [
        self::AREA_PM      => 'PM',
        self::AREA_NON     => 'Non-lead',
        self::AREA_LEADED  => 'Leaded',
        self::AREA_PLATING => 'Plating',
    ];
	
    //区域Gemba
    public static $areaGembaArr = [
        self::AREA_NON     => 'Non-lead',
        self::AREA_LEADED  => 'Leaded',
        self::AREA_PLATING => 'Plating',
    ];
	
    const SECTION_NON     = 1; //Non-lead
    const SECTION_LEADED  = 2; //Leaded
    const SECTION_PM      = 3; //Leaded
    const SECTION_PLATING = 4; //Plating

    //所属分部
    public static $sectionArr = [
        self::SECTION_NON     => 'Non-Lead',
        self::SECTION_LEADED  => 'Leaded',
        self::SECTION_PM      => 'PM',
        self::SECTION_PLATING => 'Plating',
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

    //所属分部
    public static $positionArr = [
        self::POSITION_OPERATOR       => '操作员',
        self::POSITION_TAKE           => '带班',
        self::POSITION_PRODUCT        => '产品工程师',
        self::POSITION_DEVICE         => '设备工程师',
        self::POSITION_PM             => 'PM技术员',
        self::POSITION_PM_LEADER      => 'PM leader',
        self::POSITION_PRODUCE_LEADER => '生产leader',
        self::POSITION_SUB            => '分部经理',
        self::POSITION_DEPARTMENT     => '部门经理',
        self::POSITION_CHEMISTRY      => '化学分析',
        self::POSITION_QA             => 'Inline QA',
        self::POSITION_CHEMISTRY_ENG  => '化学工程师',
        self::POSITION_TRAINER        => '培训员',
        self::POSITION_CLERK          => '文员',
        self::POSITION_PROCESS        => '工艺工程师',
    ];

    //直属领导
    public static $leaderArr = [
        self::POSITION_TAKE,
        self::POSITION_PM_LEADER,
        self::POSITION_PRODUCE_LEADER,
        self::POSITION_SUB,
        self::POSITION_DEPARTMENT,
        self::POSITION_DEVICE,
    ];

    const NLD_PM_DL    = 1;
    const LEADED_PM_DL = 2;
    const NLD_PM_IDL   = 3;
    const LEADED_PM_IDL= 4;
    public static $sectionIrArr = [
        self::NLD_PM_DL      => 'NLD PM DL',
        self::LEADED_PM_DL   => 'LEADED PM DL',
        self::NLD_PM_IDL     => 'NLD PM IDL',
        self::LEADED_PM_IDL  => 'LEADED PM IDL'
    ];

    const SCENARIO_UPDATE_GEMBA = 'update-gemba';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_user_info';
    }


    public function scenarios()
    {
        return array_merge(parent::scenarios(),[self::SCENARIO_UPDATE_GEMBA => ['gemba_role','team']]); // TODO: Change the autogenerated stub
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'leader_uid',
                    'role_id',
                    'sex',
                    'status',
                    'position',
                    'shift',
                    'station_id',
                    'team',
                    'skill',
                    'gemba_role',
                    'shoe_type',
                    'section_ir',
                ], 'integer'
            ],
            [
                [
                    'pid',
                    'tel',
                    'chinese_name',
                    'english_name',
                    'email',
                    'area',
                    'sfc_number',
                    'fsl_number',
                    'meal_card_code',
                    'wbi_code',
                    'badge_code',
                    'department_number',
                    'section',
                    'create_time',
                    'update_time',
                ], 'string'
            ],
            [['tel', 'email','badge_code'], 'unique']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                => 'ID',
            'role_id'           => '权限关联表ID',
            'leader_uid'        => '直属经理的用户的ID',
            'pid'               => 'PID唯一标识',
            'status'            => '状态',
            'tel'               => '用户手机号',
            'chinese_name'      => '中文名称',
            'english_name'      => '英文名称',
            'sex'               => '性别',
            'email'             => '邮箱',
            'shoe_type'             => '是否校验洗鞋机',
            'position'          => '职务',
            'shift'             => '班别',
            'team'              => 'gemba小组',
            'gemba_role'        => 'gemba权限',
            'area'              => '区域',
            'station_id'        => '站别主键ID',
            'sfc_number'        => 'SFC账号',
            'fsl_number'        => 'FSL工号',
            'meal_card_code'    => '餐卡后五位',
            'wbi_code'          => 'WBI code ',
            'badge_code'        => '识别证码',
            'skill'             => '多技能工位',
            'department_number' => '部门号',
            'section'           => '所属分部',
            'section_ir'        => '所属分部IR',
            'create_time'       => '创建时间',
            'update_time'       => '更新时间',
        ];
    }

    //站别
    public function getUserstation() {
        return $this->hasOne(UserStation::className(), ['id' => 'station_id']);
    }

     /**
     * 根据用户ID获取section ir
     * @param $id
     * @return string
     */
    public function getSectionIr($id)
    {
        $query = self::findOne(['id' => $id]);
        return empty($query) ? 0 : $query->section_ir;
    }
    /**
     * 获得用户名称
     * @param  string|array $id
     * @return string
     */
    public function getNameById($id){

        if(empty($id)){
            return false;
        }
        if(!empty($id) && is_string($id)){
            $id = explode(',',$id);
        }
        if(empty($id) || count($id) <= 0){
            return false;
        }
        $userArr = $this->getAllUsersNameById($id);
        $userNameStr = [];
        foreach ($id as $k => $v){
            foreach ($userArr as $value){
                if($value['id'] == $v){
                    $userNameStr[$k] = $value['chinese_name'];
                }
            }
        }

        return $userNameStr;
    }

    /**
     * 获得用户名称
     * @param  string|array $id
     * @return string
     */
    public static function getOneNameById($id){

        if(empty($id)){
            return false;
        }
        $result = self::find()->select('chinese_name')
                ->where(['id' => $id])
                ->one();

        return $result->chinese_name;
         
    }
    public function getAllUsersNameById($id){
        return self::find()->select('id,chinese_name')
                ->where(['id' => $id])
                ->asArray()
                ->all();
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
        if (!$this->save()) {
            $error = array_values($this->getFirstErrors())[0];
            return $this->arrData(100, $error);
        }
        return $this->arrData(200, $this->attributes['id']);
    }

    /**
     * 修改
     * @param $data
     * @return array
     */
    public function edit($data)
    {
        $model = self::findOne($data['id']);
        $model->role_id           = $data['role_id'];
        $model->leader_uid        = $data['leader_uid'];
        $model->shoe_type        = $data['shoe_type'];
        $model->pid               = $data['pid'];
        $model->tel               = $data['tel'];
        $model->status            = $data['status'];
        $model->chinese_name      = $data['chinese_name'];
        $model->english_name      = $data['english_name'];
        $model->sex               = $data['sex'];
        $model->email             = $data['email'];
        $model->position          = $data['position'];
        $model->shift             = $data['shift'];
        $model->area              = $data['area'];
        $model->station_id        = $data['station_id'];
        $model->sfc_number        = $data['sfc_number'];
        $model->fsl_number        = $data['fsl_number'];
        $model->meal_card_code    = $data['meal_card_code'];
        $model->wbi_code          = $data['wbi_code'];
        $model->badge_code        = $data['badge_code'];
        $model->skill             = $data['skill'];
        $model->team              = $data['team'];
        $model->gemba_role        = $data['gemba_role'];
        $model->department_number = $data['department_number'];
        $model->section           = $data['section'];
        $model->section_ir        = $data['section_ir'];
        $model->update_time       = date('Y-m-d H:i:s', time());
        if (!$model->save()) {
            $error = array_values($model->getFirstErrors())[0];
            return $this->arrData(100, $error);
        }
        return $this->arrData(200, '更新成功');
    }

    /**
     * 获取提交的数据
     * @param $data
     */
    public function keyValue($data)
    {
        $this->role_id           = $data['role_id'];
        $this->leader_uid        = $data['leader_uid'];
        $this->shoe_type        = $data['shoe_type'];
        $this->pid               = $data['pid'];
        $this->tel               = $data['tel'];
        $this->status            = $data['status'];
        $this->chinese_name      = $data['chinese_name'];
        $this->english_name      = $data['english_name'];
        $this->sex               = $data['sex'];
        $this->email             = $data['email'];
        $this->position          = $data['position'];
        $this->shift             = $data['shift'];
        $this->area              = $data['area'];
        $this->station_id        = $data['station_id'];
        $this->sfc_number        = $data['sfc_number'];
        $this->fsl_number        = $data['fsl_number'];
        $this->meal_card_code    = $data['meal_card_code'];
        $this->wbi_code          = $data['wbi_code'];
        $this->badge_code        = $data['badge_code'];
        $this->skill             = $data['skill'];
        $this->department_number = $data['department_number'];
        $this->section           = $data['section'];
        $this->team              = $data['team'];
        $this->gemba_role        = $data['gemba_role'];
        $this->update_time       = date('Y-m-d H:i:s', time());
    }

    /**
     * 根据用户ID获取用户名称
     * @param $id
     * @return string
     */
    public function getUserName($id)
    {
        if(empty($id)){
            return '';
        }
        $query = self::findOne(['id' => $id]);
        return empty($query) ? '' : $query->chinese_name;
    }

     /**
     * 
     * @param $id
     * @return string
     */
    public function getLeaderName($id)
    {
        if(empty($id)){
            return '';
        }
        $query = self::findOne(['id' => $id]);
        if(empty($query)){
            return '';
        }
        $model = self::findOne(['id' => $query->leader_uid]);
        return empty($model) ? '' : $model->chinese_name;
    }

    /**
     * 根据id查询用户名称
     * @param $ids
     * @return array|bool
     */
    public static function getUserNameByIdArray($ids){
        if(empty($ids)){
            return false;
        }
        if(!is_array($ids)){
            $ids = explode(',',$ids);
        }
        $userNames = self::find()->select('chinese_name')->where(['id' => $ids])->column();
        if($userNames){
           return $userNames;
        }
        return false;
    }

    /**
     * 获取人员信息
     * @param array $param
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getConnectUserList($param= [])
    {
        $query = self::find();
        $query->select(['id', 'chinese_name']);
        $query->where(['status' => self::STATUS_OPEN]);
        $query->andWhere(['>', 'id', 1]);
        $query->andFilterWhere(['in', 'position', $param]);
        $query->asArray();
        return $query->all();
    }

    /**
     * 下拉搜索用户列表
     * @param $section
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function UserDropDownList($section = null){
        $query = self::find();
        $query->select(['id', 'chinese_name']);
        $query->where(['status' => self::STATUS_OPEN]);
        $query->andWhere(['>', 'id', 1]);
        $query->andFilterWhere(['section' => $section]);
        $query->asArray();
        return $query->all();
    }

    /**
     * 获取日志交接人员信息
     * @param $area
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getBlogUserList($area = null)
    {
        $query = self::find();
        $query->select(['id', 'chinese_name']);
        $query->where(['status' => self::STATUS_OPEN]);
        $query->andWhere(['>', 'id', 1]);
        $query->andFilterWhere(['area' => $area]);
        $query->asArray();
        return $query->all();
    }

    /**
     * 默认搜索的用户信息
     */
    public function getSearchDefaultUser($section){
        $query = self::find();
        $query->select(['id', 'chinese_name']);
        $query->where(['status' => self::STATUS_OPEN]);
        $query->andWhere(['>', 'id', 1]);
        $query->andWhere(['section' => $section]);
        $query->asArray();
        return $query->all();
    }
    /**
     * 根据分组筛选交接人
     * @param $params
     * @return array|\yii\db\ActiveRecord[]
     */
    public function BySearchGetBlogUserList($params){

        $query = self::find();
        $query->select(['id', 'chinese_name as text']);
        $query->where(['status' => self::STATUS_OPEN]);
        $query->andWhere(['>', 'id', 1]);
        !empty($params['section'])      && $query->andWhere(['section' => $params['section']]);
        !empty($params['area'])         && $query->andWhere(['area' => $params['area']]);
        !empty($params['position'])     && $query->andWhere(['position' => $params['position'] ]);
        !empty($params['shift'])        && $query->andWhere(['shift' => $params['shift']]);
        !empty($params['user-stations'])&& $query->andWhere(['station_id' => $params['user-stations']]);
        !empty($params['gemba_role'])   && $query->andWhere(['gemba_role' => $params['gemba_role']]);
        !empty($params['team'])   && $query->andWhere(['team' => $params['team']]);
        $query->asArray();
        return $query->all();
    }
    /**
     * 获取用户角色名称和角色名称
     * @param $userId
     * @return array|null|\yii\db\ActiveRecord
     */
    public function getUserRoleName($userId)
    {
        $query = self::find();
        $query->select(['nxp_user_info.chinese_name', 'r.name as role_name']);
        $query->leftJoin(Role::tableName() . ' r', 'r.id = nxp_user_info.role_id');
        $query->andWhere(['nxp_user_info.id' => $userId]);
        $query->asArray();
        return $query->one();
    }

    /**
     * 获取用户及角色信息
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getUserRoleData()
    {
        $query = self::find();
        $query->select(['nxp_user_info.id', 'nxp_user_info.chinese_name', 'r.name as role_name']);
        $query->leftJoin(Role::tableName() . ' r', 'r.id = nxp_user_info.role_id');
        $query->where(['nxp_user_info.status' => self::STATUS_OPEN]);
        $query->asArray();
        return $query->all();
    }

    /**
     * 获取需要发送人邮箱地址
     * @param array $userIdList
     * @return array|bool
     */
    public function getUserEmail($userIdList = [])
    {
        if(empty($userIdList)){
            return false;
        }
        $model = self::find();
        $model ->select(['chinese_name','email']);
        $model ->where(['in', 'id', $userIdList]);
        $model ->asArray();
        return $model ->all();
    }

    /**
     * 使用识别号查询用户信息，没有返回空对象
     * @param $wbi_code
     * @return $this|UserInfo|bool|null
     */
    public function getUserByWbiCode($wbi_code){

        if(empty($wbi_code)){
            return false;
        }
        $data = $this->findOne(['wbi_code' => $wbi_code]);
        if(!empty($data)){
            return $data;
        }else{
            return $this;
        }
    }
	
	
    /**
     * 使用识别号查询用户信息，没有返回空对象
     * @param $wbi_code
     * @return $this|UserInfo|bool|null
     */
    public function getUserByBadgeCode($badge_code){

        if(empty($badge_code)){
            return false;
        }
        $data = $this->findOne(['badge_code' => $badge_code]);
        if(!empty($data)){
            return $data;
        }else{
            return $this;
        }
    }
	

    /**
     * 查询用户直属领导的id
     * @param $id
     * @return array
     */
    public function getLeaderUids($id){
        return self::find()->select('leader_uid')
                ->where(['id' => $id])
                ->column();
    }

    /**
     * 根据id获取用户班别
     * @param $ids
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getUserShiftById($ids){
        return $this->find()->select('id,shift')->where(['id' => $ids])->all();
    }

    //获取所有带班的email
    public function getApplyUserEmails(){
        $role_id = (new Role())->getIdByName('带班');
        return self::find()->select('email,chinese_name')->where(['role_id' => $role_id])->andWhere(['!=','email',''])->asArray()->all();
    }

    /**
     * 获取人员所属区域
     * @param $id
     * @return string
     */
    public function getAreaById($id)
    {
        $model = self::findOne(['id' => $id]);
        return empty($model) ? '' : $model->area;
    }


    
    public function getTeamName($id)
    {
        $model = self::findOne(['id' => $id]);
        if(empty($model)) return '';
        $modelteam = Team::findOne(['id' => $model->team]);
        return empty($modelteam) ? '' : $modelteam->teamName;
    }

    /**
     * 获取班别
     * @param $id
     * @return string
     */
    public static function getShift($id)
    {
        $model = self::findOne(['id' => $id]);
        return ArrayHelper::getValue(self::$shiftArr, $model->shift);
    }


    /**
     * 根据站别区域班别获取用户id
     * @param $param
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getUserIdList($param)
    {
        $query = UserInfo::find();
        $query->select('id');
        $query->where(['area' => $param['area']]);
        $query->andFilterWhere(['in', 'station_id', $param['station']]);
        $query->andFilterWhere(['in', 'shift', $param['shift']]);
        $query->andFilterWhere(['status'=> 1]);
        $query->orderBy(['id' => SORT_ASC]);
        $query->asArray();
        return $query->column();
    }

    /**
     * 查询bbs同步的用户数据
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getSyncUserByBbs(){
        return self::find()->alias('ui')
            ->select('u.name,,ui.email,ui.id,ui.role_id,ui.chinese_name')
            ->innerJoin(['u' => User::tableName()],'u.user_id = ui.id')
            ->asArray()
            ->all();
    }

    /**
     * 根据id查询用户名称和邮箱
     * @param $id
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function getUserEmailAndName($id){
        return self::find()->select('chinese_name,email')->where(['id' => $id])->asArray()->one();
    }

    /**
     * 根据数组键获取数组值
     * @param array $selfArr 本类的定义数组
     * @param array $keyStr  键值字符串
     * @return string
     */
    public static function getSelfArrData($selfArr = [], $keyStr = '')
    {
        if (empty($selfArr) || empty($keyStr)) return '';
        $keyArr = explode(',', $keyStr);
        $str = '';
        foreach ($keyArr as $value){
            $strValue = ArrayHelper::getValue($selfArr, $value, '');
           if(empty($strValue)) continue;
            $str .= $strValue . ',';
        }
        return $str;
    }
    
    /**
     * 获取带班
     * @return array
     */
    public static function getHeaderUserList()
    {
        $query = self::find();
        $query->select(['id', 'chinese_name', 'shift', 'area']);
        $query->where(['position' => self::POSITION_TAKE]);
        $query->andWhere(['status' => self::STATUS_OPEN]);
        $query->orderBy(['area' => SORT_ASC, 'shift' => SORT_ASC]);
        $query->asArray();
        $data   = $query->all();
        $return = [];
        foreach ($data as $value) {
            $shift    = ArrayHelper::getValue(self::$shiftArr, $value['shift'], '');//班别
            $area     = ArrayHelper::getValue(self::$areaArr, $value['area'], '');  //区域
            $return[$value['id']] = $value['chinese_name'] . '---' . $area . '---' . $shift;
        }
        return $return;
    }
}
