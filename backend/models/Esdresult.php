<?php

namespace backend\models;

class Esdresult extends Base
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'esdresult';
    }

    /**
     * @return object|\yii\db\Connection|null
     * @throws \yii\base\InvalidConfigException
     */
    public static function getDb()
    {
        return \Yii::$app->get('db3');
    }
}

