<?php
namespace backend\modules\Appeal\models;

interface AppealInterface{
    /**
     * 新增申诉
     * @return mixed
     */
    public function addAppeal();

    /**
     * 同意审核
     * @return mixed
     */
    public function auditAppeal();

}