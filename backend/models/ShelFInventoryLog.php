<?php

namespace backend\models;


/**
 * This is the model class for table "nxp_shelf_apply".
 *
 * @property string $id
 * @property string $part_no
 * @property int $user_id
 * @property int $status
 * @property int $count
 * @property int $amount
 * @property string $machine_id
 * @property string $application_time
 */
class ShelFInventoryLog extends Base
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_shelf_inventory_log';
    }



}
