<?php

namespace backend\models;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "nxp_machine_roster".
 *
 * @property int $id
 * @property string $work_date
 * @property int $class
 * @property int $shift
 * @property int $week
 * @property string|null $create_time
 */
class MachineRoster extends Base
{
    const WEEK_MON  = 1;
    const WEEK_TUES = 2;
    const WEEK_WES  = 3;
    const WEEK_THUR = 4;
    const WEEK_FRI  = 5;
    const WEEK_SAT  = 6;
    const WEEK_SUN  = 0;

    public static $weekList = [
        self::WEEK_MON  => '星期一',
        self::WEEK_TUES => '星期二',
        self::WEEK_WES  => '星期三',
        self::WEEK_THUR => '星期四',
        self::WEEK_FRI  => '星期五',
        self::WEEK_SAT  => '星期六',
        self::WEEK_SUN  => '星期日'
    ];

    const CLASS_D = 1;
    const CLASS_N = 2;

    public static $classList = [
        self::CLASS_D => '白班',
        self::CLASS_N => '夜班'
    ];

    public static $shiftListArr = [
        'A' => 'A班',
        'B' => 'B班',
        'C' => 'C班',
        'D' => 'D班',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_machine_roster';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['class', 'week'], 'integer'],
            [[ 'shift','create_time', 'work_date'], 'string']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'work_date'   => '工作日期',
            'class'       => '白夜班',
            'shift'       => '班别',
            'week'        => '星期几',
            'create_time' => '创建时间',
        ];
    }

    public function getWeekList($param = []) {
        $str = '';
        foreach($param as $val){
            $str .= '   '. ArrayHelper::getValue(self::$weekList, $val,'');
        }
        return $str;
    }

}



