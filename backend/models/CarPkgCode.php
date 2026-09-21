<?php
namespace backend\models;

use yii\data\ActiveDataProvider;

class CarPkgCode extends Base
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nxp_car_pkg_code';
    }

    public function search($params)
    {
        $query = self::find();
      //  $query->where(['type' => $params['type']]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        return $dataProvider;
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['case_outline', 'pkg_code','pkg_type','type', 'type_user_id'], 'safe'],
        ];
    }

       /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'           => 'ID',
            'type'         => '大小材料',
            'case_outline' => 'CaseOutline',
            'pkg_code'     => 'PkgCode',
            'pkg_type'     => 'PkgType',
            'type_user_id' => '负责人'
        ];
    }

    public function check($data)
    {
        $query = self::find();
        $query->where(['!=', 'id', $data['id']]);
        $query->andFilterWhere(['case_outline'  => $data['case_outline']]);
        if ($query->one()) {
            return false;
        }
        return true;
    }

    public function getEngineerName($package)
    {
        $pkgCode              = self::findOne(['case_outline' => $package]);
        if($pkgCode){
            $query = UserInfo::findOne(['id' => $pkgCode->type_user_id]);
            return empty($query) ? '' : $query->chinese_name;
        }
       return '';
    }

      /**
     * @param $data
     * @return array
     */
    public function edit($data)
    {
        $model                = self::findOne($data['id']);
        $model->case_outline  = strtoupper($data['case_outline']);
        $model->pkg_code      = strtoupper($data['pkg_code']);
        $model->pkg_type      = strtoupper($data['pkg_type']);
        $model->type          = $data['type'];
        $model->type_user_id  = $data['type_user_id'];
        if (!$model->save(false)) {
            $error = array_values($model->getFirstErrors())[0];
            return $this->arrData(100, $error);
        }
        return $this->arrData(200, '更新成功');
    }

}
