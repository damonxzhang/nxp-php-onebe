<?php

namespace backend\models;

class TinBallCabinetQr extends Base
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_tin_ball_cabinet_qr';
    }
     /**
     * 添加数据
     * @param $data
     * @return bool
     */
    public function create($data)
    {
        $this->boxstr       = $data['boxstr'];
        $this->end_time     = $data['end_time'];
        $this->start_time   = $data['start_time'];
        $this->expire_time  = $data['expire_time'];
        $this->create_time  = $data['create_time'];
        $this->bill_id      = $data['bill_id'];
        $this->relation_id  = $data['relation_id'];
        $this->part_no      = $data['part_no'];
        $this->lot_no       = $data['lot_no'];
        $this->material_part = $data['material_part'];
        $this->cont_no      = $data['cont_no'];
        $this->no_box       = $data['no_box'];
        $this->big_select   = $data['big_select'];
        if (!$this->save()) {
            return false;
        }
        return true;
    }

}
