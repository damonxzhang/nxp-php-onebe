<?php

namespace frontend\controllers;


use Yii;
use backend\models\Shelf;
use backend\models\ShelfApply;
use backend\models\ShelfMaterials;
use backend\models\UserInfo;
use backend\models\ShelfActionLog;

class ShelfController extends FrontendBaseController
{
    public function actionStatusInfo(){
        $params = $this->validateRequest();
        $query = Shelf::find();

        if (!empty($params['floor'])){
            $query->andWhere(['floor'  => $params['floor']]);
        }
        $query->andWhere(['in','region',['G','Y']]);
        $shelfs = $query->asArray()->all();
        if (empty($shelfs)){
            return $this->outputJson('false','参数错误，未查询到货架列表');
        }

        $status_num = [
            Shelf::STATUS_RECEIVE   => 0,
            Shelf::STATUS_EXPIRE    => 0,
            Shelf::STATUS_FREE      => 0,
            Shelf::SHELF_RECEIVED   => 0,
            20                      => 0
        ];

        foreach ($shelfs as &$shelf){

            if($shelf['region'] == 'Y'){
                $shelf['column'] = 6;
            }
            $shelf['number'] = $shelf['region'].$shelf['row'];
            $expire_time = date('Y-m-d h:i:s',strtotime("+1 hours"));
            foreach ($status_num as $key => &$status){
                if ($shelf['status'] == Shelf::STATUS_RECEIVE) {
                    $shelfMaterial = ShelfMaterials::find()->where(['shelf_id' => $shelf['id'],'fettle' => ShelfMaterials::FETTLE_UP])->andWhere(['>=','expire_time',$expire_time])->exists();
                    if (!empty($shelfMaterial)){
                        $status_num[20] += 1;
                    } else {
                        $status += 1;
                    }
                    break;
                } else if ($shelf['status'] == $key){
                    $status += 1;
                    break;
                }

            }
            $shelf['status_info'] = !empty(Shelf::$statusArr[$shelf['status']]) ? Shelf::$statusArr[$shelf['status']]: '';
        
            $shelfMater = ShelfMaterials::find()->where(['shelf_id' => $shelf['id'],'fettle' => ShelfMaterials::FETTLE_UP,'status' => ShelfMaterials::STATUS_USING])->orderBy('id')->one();
            // print_r(Shelf::SHELF_RECEIVED);die;
            if ($shelf['status'] == Shelf::SHELF_RECEIVED){
                // print_r($shelf['id']);die;
                $applyUser = UserInfo::find()
                ->alias('u')
                ->leftJoin(ShelfApply::tableName().' as sa','u.id = sa.user_id')
                ->select('u.*')
                ->where(['sa.id'=>$shelfMater->apply_id])
                ->one();
                $shelf['apply'] = [
                    'part_no' => $shelfMater->part_no,
                    'chinese_name' => $applyUser->chinese_name,
                    'badge_code' => $applyUser->badge_code,
                ];
            }
        }



        return $this->outputJson('true','成功！',['status' => $status_num,'shelf' => $shelfs]);
    }

     /**
     * 移动车
     * @return string
     * @throws \yii\db\Exception
     */
    public function actionChange()
    {
        $params = $this->validateRequest();
        if ( empty($params['floor']) || empty($params['car_number']) || empty($params['region'])){
            return $this->outputJson('false','参数错误');
        }

        $shelfs = Shelf::find()->where(['car_number' => $params['car_number']])->orderBy('row asc')->all();
        if (empty($shelfs)){
            return $this->outputJson('false','货架车号错误或者货架不存在');
        }
        // 增加一个判断，G位置同一位置不能有多个车
        if($params['region']=='G'){
            $checkChange = Shelf::find()->where(['region' => $params['region'],'column' => $params['column']])->all();
            if($checkChange){
                
                return $this->outputJson('false','此位置已有车');
            }
        }
        


        $transaction = Yii::$app->db->beginTransaction();
        foreach ($shelfs as $shelf){
            
            if ($shelf->region == $params['region']){
                return $this->outputJson('false','操作失败！位置未改变');
            }
            if(($params['region']=="N" || $params['region']=="B" ) && $shelf->region=='G'){
                $shelf->end_shelf = NULL;
            }

            $shelf->region = $params['region'];
            $shelf->column = !empty($params['column']) ? $params['column'] : 0 ;
            

            if(!$shelf->save()){
                $transaction->rollBack();
                break;
            }

        }
        $transaction->commit();
        
        $log_params[] = [
            'car_number' => $params['car_number'],
            'floor' => $params['floor'],
            'region' => $params['region'],
            'column' => $params['column']
        ];
        ShelfActionLog::saveLog(1,ShelfActionLog::CAR_CHANGE,$log_params);

        return $this->outputJson('true','成功！');

    }

}