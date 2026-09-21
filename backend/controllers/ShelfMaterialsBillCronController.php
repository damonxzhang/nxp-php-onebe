<?php
namespace backend\controllers;

use yii\web\Controller;
use backend\models\ShelfMaterialsBill;
use backend\models\ShelfMaterialsBillRelation;

class ShelfMaterialsBillCronController extends Controller
{
    public function actionIndex()
    {
        $this->smallPending();
        $this->smallSuccess();
        $this->bigPending();
        $this->bigSuccess();
        return true;
    }

    /**
     * 小材料
     */
    public function smallPending(){
       
        //先更新一下物料单的状态
        $materialsBillIdArr = ShelfMaterialsBill::find()->select(['id'])->where(['type' => 1, 'status' => ShelfMaterialsBill::STATUS_PENDING])->column();
        if (empty($materialsBillIdArr)) {
            return true;
        }
        //查询所有型号已完成的数量
        foreach ($materialsBillIdArr as $id) {
            $shelfMaterialsBillRelation = ShelfMaterialsBillRelation::find()->where(['bill_id' => $id])->asArray()->all();
            if (empty($shelfMaterialsBillRelation)) {
                continue;
            }
            $checkNUmber   = count($shelfMaterialsBillRelation); //标志位所有型号数量
            $successNumber = 0; //标志位用于统计已完成的型号
            foreach ($shelfMaterialsBillRelation as $value) {
                $total = $value['print_number'] + $value['abnormal_number'];
                //判断申请的数量是否等于打印数量加异常数量
                if ($value['apply_number'] != $total) {
                    continue;
                }
                $successNumber++;
            }
            //判断所有打印完成的型号是否和申请的型号数量相等
            if ($successNumber == $checkNUmber) {
                $model         = ShelfMaterialsBill::findOne($id);
                $model->status = ShelfMaterialsBill::STATUS_SUCCESS;
                $model->save(false);
            }
        }
        return true;
    }

     /**
     * 小材料
     */
    public function smallSuccess(){
       
        //先更新一下物料单的状态
        $materialsBillIdArr = ShelfMaterialsBill::find()->select(['id'])->where(['type' => 1, 'status' => ShelfMaterialsBill::STATUS_SUCCESS])->column();
        if (empty($materialsBillIdArr)) {
            return true;
        }
        //查询所有型号已完成的数量
        foreach ($materialsBillIdArr as $id) {
            $shelfMaterialsBillRelation = ShelfMaterialsBillRelation::find()->where(['bill_id' => $id])->asArray()->all();
            if (empty($shelfMaterialsBillRelation)) {
                continue;
            }
            $checkNUmber   = count($shelfMaterialsBillRelation); //标志位所有型号数量
            $successNumber = 0; //标志位用于统计已完成的型号
            foreach ($shelfMaterialsBillRelation as $value) {
                $total = $value['print_number'] + $value['abnormal_number'];
                //判断申请的数量是否等于打印数量加异常数量
                if ($value['apply_number'] != $total) {
                    continue;
                }
                $successNumber++;
            }
            //判断所有打印完成的型号是否和申请的型号数量相等
            if ($successNumber == $checkNUmber) {
                $model         = ShelfMaterialsBill::findOne($id);
                $model->status = ShelfMaterialsBill::STATUS_SUCCESS;
                $model->save(false);
            }
        }
        return true;
    }

     /**
     * 大材料
     */
    public function bigPending(){
       
        //先更新一下物料单的状态
        $materialsBillIdArr = ShelfMaterialsBill::find()->select(['id'])->where(['type' => 2, 'status' => ShelfMaterialsBill::STATUS_PENDING])->column();
        if (empty($materialsBillIdArr)) {
            return true;
        }
        //查询所有型号已完成的数量
        foreach ($materialsBillIdArr as $id) {
            $shelfMaterialsBillRelation = ShelfMaterialsBillRelation::find()->where(['bill_id' => $id])->asArray()->all();
            if (empty($shelfMaterialsBillRelation)) {
                continue;
            }
            $checkNUmber   = count($shelfMaterialsBillRelation); //标志位所有型号数量
            $successNumber = 0; //标志位用于统计已完成的型号
            foreach ($shelfMaterialsBillRelation as $value) {
                $total = $value['print_number'] + $value['abnormal_number'];
                //判断申请的数量是否等于打印数量加异常数量
                if ($value['apply_number'] != $total) {
                    continue;
                }
                $successNumber++;
            }
            //判断所有打印完成的型号是否和申请的型号数量相等
            if ($successNumber == $checkNUmber) {
                $model         = ShelfMaterialsBill::findOne($id);
                $model->status = ShelfMaterialsBill::STATUS_SUCCESS;
                $model->save(false);
            }
        }
        return true;
    }

     /**
     * 大材料
     */
    public function bigSuccess(){
       
        //先更新一下物料单的状态
        $materialsBillIdArr = ShelfMaterialsBill::find()->select(['id'])->where(['type' => 2, 'status' => ShelfMaterialsBill::STATUS_SUCCESS])->column();
        if (empty($materialsBillIdArr)) {
            return true;
        }
        //查询所有型号已完成的数量
        foreach ($materialsBillIdArr as $id) {
            $shelfMaterialsBillRelation = ShelfMaterialsBillRelation::find()->where(['bill_id' => $id])->asArray()->all();
            if (empty($shelfMaterialsBillRelation)) {
                continue;
            }
            $checkNUmber   = count($shelfMaterialsBillRelation); //标志位所有型号数量
            $successNumber = 0; //标志位用于统计已完成的型号
            foreach ($shelfMaterialsBillRelation as $value) {
                $total = $value['print_number'] + $value['abnormal_number'];
                //判断申请的数量是否等于打印数量加异常数量
                if ($value['apply_number'] != $total) {
                    continue;
                }
                $successNumber++;
            }
            //判断所有打印完成的型号是否和申请的型号数量相等
            if ($successNumber == $checkNUmber) {
                $model         = ShelfMaterialsBill::findOne($id);
                $model->status = ShelfMaterialsBill::STATUS_SUCCESS;
                $model->save(false);
            }
        }
        return true;
    }

}
