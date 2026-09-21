<?php
namespace  backend\models\search;

use backend\models\Shelf;
use yii\data\ActiveDataProvider;
use backend\models\ShelfMaterials;


class ShelfSearch extends Shelf {

    public $status;
    public $shelf_number;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['status', 'shelf_number'], 'safe'],
        ];
    }

    /**
     * 数据查询
     * @param $param
     * @return ActiveDataProvider
     */
    public function search($param)
    {
        $this->setAttributes($param);
        $query = Shelf::find();
        $query->andFilterWhere(['status' => $this->status]);
        $query->andFilterWhere(['like', 'shelf_number', trim($this->shelf_number)]);
        $query->orderBy(['id'=>SORT_ASC]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
    }

    /**
    * 数据查询
    * @param $param
    * @return ActiveDataProvider
    */
   public function searchall($param)
   {
        $this->setAttributes($param);
        $query = ShelfMaterials::find();
        $query->select('sm.id,sm.part_no,sm.lot_no,sm.cont_no,sm.end_time,sm.expire_time,s.shelf_number,s.floor,s.region,s.car_number,s.column,s.row,s.status');
        $query->alias('sm');
        $query->leftJoin(Shelf::tableName() . ' s', 'sm.shelf_id = s.id');
        $query->andFilterWhere(['sm.fettle'=>1]);
        // $query->andFilterWhere(['in','sm.status',[1,2,3,4]]);

        if(!empty($param['floor']))
            $query->andFilterWhere(['s.floor' => $param['floor']]);

        if(!empty($param['part_no']))
            $query->andFilterWhere(['like', 'sm.part_no' , $param['part_no']]);

        if(!empty($param['status'])){
            if($param['status'] == 1){
                $query->andFilterWhere(['in' , 'sm.status' , [1,2,3]]);
                $query->andFilterWhere(['<' , 'sm.end_time' , date("Y-m-d H:i:s", time())]);
            }
                

            if($param['status'] == 2)
                $query->andFilterWhere(['>=' , 'sm.end_time' , date("Y-m-d H:i:s", time())]);
            
            if($param['status'] == 3){
                $query->andFilterWhere(['in' , 'sm.status' , [1,3]]);
                $query->andFilterWhere(['<' , 'sm.expire_time' , date("Y-m-d H:i:s", strtotime('+6 hours'))]);
            }

            
            if($param['status'] == 4){
                $query->andFilterWhere(['sm.status' => 4]);
            }
                
                    
        }
            

        if(!empty($param['protime'])){
            if($param['protime'] < 99){
                $addtime = '+'.$param['protime'].' hours';
                $query->andFilterWhere(['<=' , 'sm.end_time' , date("Y-m-d H:i:s", strtotime($addtime))]);
                $query->andFilterWhere(['>=' , 'sm.end_time' , date("Y-m-d H:i:s", time())]);
            }else{
                $query->andFilterWhere(['>=' , 'sm.end_time' , date("Y-m-d H:i:s", strtotime('+4 hours'))]);
                
            }
            
            $query->orderby('sm.end_time asc');
        }else{
            $query->orderby('floor asc');
        }
            
        
        $query->asArray();
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        return $dataProvider;
   }


    /**
     * 获取货架当前状态
     * @param $status
     * @param string $number
     * @return string
     */
    public function getStatus($status, $number = '')
    {
        $result = '';
        switch ($status) {
           case Shelf::STATUS_RECEIVE:  //1可领取
                $result = '<button  type="button" style="width:200px;height:260px" class="layui-btn layui-btn-lg">' . $number;
                break;
            case Shelf::STATUS_PROGRESS: //2醒料中
                $result = '<button type="button" style="width:200px;height:260px" class="layui-btn layui-btn-normal layui-btn-lg">' . $number ;
                break;
            case Shelf::STATUS_FREE:     //3空闲
                $result = '<button type="button" style="width:200px;height:260px" class="layui-btn layui-btn-primary layui-btn-lg">' . $number ;
                break;
            case Shelf::STATUS_EXCEPTION: //4异常
                $result = '<button type="button" style="width:200px;height:260px" class="layui-btn layui-btn-danger layui-btn-lg">' . $number ;
                break;
            case Shelf::STATUS_WARNING:  //5预警中
                $result = '<button type="button" style="width:200px;height:260px" class="layui-btn layui-btn-warm layui-btn-lg">' . $number ;
                break;
            case Shelf::STATUS_EXPIRE:   //6过期
                $result = '<button type="button" style="width:200px;height:260px" class="layui-btn layui-btn-danger layui-btn-lg">' . $number ;
                break;
            case Shelf::SHELF_RECEIVED:  //7领取中
                $result = '<button type="button" style="width:200px;height:260px" class="layui-btn layui-btn-lg">' . $number ;
                break;
        }
        return $result;
    }
	
	
    /**
     * 获取 part_no
     */
    public function getPartNo($shelf_id){
        $query_list = ShelfMaterials::find();
        $query_list->select(['part_no']);
        $query_list->andWhere(['shelf_id'=>$shelf_id]);
        $query_list->andWhere(['fettle'=>1]);
        $query_list->andWhere(['in','status',[1,2,3,4]]);
//        $query_list->groupBy(['shelf_id']);
        $list = $query_list->asArray()->one();

//        print_r($list);
        if(empty($list))
            return '';

        return $list['part_no'];
    }
	
    /**
     * 获取 part_no
     */
    public function getContNo($shelf_id){
        $query_list = ShelfMaterials::find();
        $query_list->select(['cont_no']);
        $query_list->andWhere(['shelf_id'=>$shelf_id]);
        $query_list->andWhere(['fettle'=>1]);
        $query_list->andWhere(['in','status',[1,2,3,4]]);
//        $query_list->groupBy(['shelf_id']);
        $list = $query_list->asArray()->all();

    //    print_r($list);die;
        if(empty($list))
            return '';


        $result = '';
        foreach ($list as $key => $value) {
            # code...
            $result = $result . $value['cont_no'] . '<br>';
        }


        return $result;
    }

}