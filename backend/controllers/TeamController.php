<?php
namespace backend\controllers;

use Yii;
use backend\models\Team;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;

class TeamController extends BaseController{

    /**
     * geamba列表
     * @return string
     */
    public function actionIndex(){
        $data = Team::find();
        $pagination = new Pagination(['totalCount' =>$data->count(), 'pageSize' => '10']);    //实例化分页类,带上参数(总条数,每页显示条数)
        $model = $data->offset($pagination->offset)->limit($pagination->limit)->all();
        return $this->render('index',[
            'dataProvider' => $model,
            'pagination'   => $pagination
        ]);

    }

    /**
     * 修改名称
     * @return string
     */
    public function actionUpdate(){

        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            $model = Team::findOne($post['id']);
            $model->setAttributes(Yii::$app->request->post());
            if($model->save()){
                return $this->json(200,'修改成功');
            }
            if($error = $model->getErrors()){
                return $this->json(100,$this->errorToString($error));
            }
            return $this->json(100,'修改失败');
        }
        $id = Yii::$app->request->get('id');

        $model = Team::findOne($id);
        return $this->render('update',['model' => $model]);

    }

    /**
     * 创建geamba分组
     * @return string
     * @throws \yii\db\Exception
     */
    public function actionCreate(){

        if(Yii::$app->request->isPost){
            $teamName = Yii::$app->request->post('teamName') ;
            if(!empty($teamName)){
                $teamName = explode(',',str_replace('，',',',$teamName));
                if(!empty($teamName) && is_array($teamName)){
                    //循环插入数据
                    $result = array_map(function($team){
                        $model = new Team();
                        $model->setAttributes([
                            'teamName'  => trim($team),
                            'status' =>  Yii::$app->request->post('status'),
                            'created_at'=> time()
                        ]);
                        return  ['key' => $team, 'result' => $model->save()];
                    },$teamName);

                    $resultStr = false;
                    //剔除重复内容
                    foreach ($result as $key => $value){

                        $resultStr .= $value['result'] == 1 ?false:$value['key'].',';
                    }
                    //如果有已存在数据
                    if($resultStr && strlen($resultStr) > 1 ){

                        return $this->json(100,'存在重复小组名称：'.rtrim($resultStr,','));
                    }

                    return $this->json(200,'保存成功');

                }
            }
            return $this->json(100,'保存失败！');
        }
        return $this->render('create');

    }


}