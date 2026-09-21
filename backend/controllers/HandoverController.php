<?php
namespace backend\controllers;

use backend\models\BlogType;
use backend\models\HandoverToday;
use backend\models\search\HandoverTodaySearch;
use backend\models\UserInfo;
use Yii;
use backend\models\Blog;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

class HandoverController extends BaseController{

    /**
     * 新增交接
     * @return string
     */
    public function actionAddHandover(){
        $request = Yii::$app->request->queryParams;
        $idList  = ArrayHelper::getValue($request, 'idlist', []);
        if (!empty($idList)) {
            $shift = Yii::$app->user->identity->userInfo->shift;
            $reload = false;
            foreach ($idList as $value){
                $model = new HandoverToday(); 
                $model->setScenario('create');
                $model->setAttributes([
                    'from_id'     => $this->getUid(),
                    'log_id'      => $value,
                    'shift'       =>  $shift,
                    'create_time' => time()
                ]);

                if(!$model->save()){

                    $reload = true;
                }
            }

            if(empty($reload)){

                return  $this->json(200, '保存成功');
            }else{

                return  $this->json(200, '保存成功,自动过滤重复添加日志 ');
            }
        } else {

            return  $this->json(100, '请选择日志');
        }
    }

    /**
     * 交接首页
     * @return string
     */
    public function actionIndex(){

        $queryParams = Yii::$app->request->queryParams;
        $model = new HandoverTodaySearch();
        $dataProvider = $model->serach($queryParams);
        $data = $dataProvider->query->all();
        $userInfo = [];
        if(!empty($data)){
            $userInfo = UserInfo::findOne($data[0]['user_id']);
        }
//        list($dateArr['start_time'],$dateArr['end_time']) = $model->byDateGetShiftDate(time());
        $queryParams['start_time']  = !empty($queryParams['start_time']) ?$queryParams['start_time'] :   date('Y-m-d H:i:s' ,$model->start_time);
        $queryParams['end_time']    = !empty($queryParams['end_time']  ) ?$queryParams['end_time']   :    date('Y-m-d H:i:s' ,$model->end_time);

        $blogType = ArrayHelper::map(BlogType::findAll(['status' => BlogType::TYPE_OPEN]),'id','name') ;

        return $this->render('index',[
            'model' => $model,
            'queryParams' => $queryParams,
            'blogType' => $blogType,
            'data' => $data,
            'userInfo' => $userInfo,
            'pagination' => $dataProvider->pagination
        ]);
    }

    /**
     * 添加备注
     * @return string
     * @throws Exception
     */
    public function actionAddComment(){
        $model = HandoverToday::findOne(Yii::$app->request->get('id'));

        if(Yii::$app->request->isAjax){

            if($model->load(Yii::$app->request->post()) && $model->save()){

                return $this->json(200,'保存成功！');
            }else{
                
                return $this->json(100,$model->errorToString());
            }
        }

        if(!$model){
            throw  new Exception('Undefined page');
        }

        return $this->render('add-comment',['model' => $model]);
    }

    /**
     * 同步交班班别
     * @return string
     */
    public function actionSynchronization(){
        $data = Yii::$app->request->post();
        if(empty($data['to_shift']) || empty($data['idlist']) || count($data['idlist']) < 1){
            return $this->json(100,'参数错误');
        }

        $models = HandoverToday::findAll($data['idlist']);
        if($models){
            $isErr = false;
            foreach ($models as $model){
                $model->shift = $data['to_shift'];
                //更新班别
                if(!$model->save()){
                    $isErr = $model->errorToString();
                }
            }
            if($isErr === false){
                return $this->json(200,'保存成功');
            }else{
                return $this->json(100,$isErr);
            }
        }else{
            return $this->json(100,'服务器错误！');
        }
    }

    /**
     *  删除今日交接
     * @param $id
     * @return string
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id){
        $model = HandoverToday::findOne($id);

        if($model && $model->delete()){
             return $this->json(200,'删除成功！');
        }else{
            return $this->json(100,'删除失败！');
        }

    }

    /**
     * 导出数据
     * @return string
     */
    public function actionExport()
    {
        $request = Yii::$app->request->queryParams;
        $idList  = ArrayHelper::getValue($request, 'idlist', []);
        if (empty($idList)) {
            return $this->json(100,'请勾选要导出的交接');
        }

        $model = new HandoverTodaySearch();
        $data = $model->exploadData($idList);

        $resData = [];
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $resData[$key][] = !empty($value['type']) ? $value['type'] : '未设置   ';
                $resData[$key][] = $value['title'];
                $resData[$key][] = $value['content_data'];
                $resData[$key][] = (new UserInfo())->getUserName($value['user_id']);
                $resData[$key][] =  Blog::$statusArr[$value['status']];
                $resData[$key][] = $value['comment'];
                $resData[$key][] = date('Y-m-d H:i:s', strtotime($value['create_time']));
                $resData[$key][] = date('Y-m-d H:i:s', strtotime($value['update_time']));
            }
        }
        $fileName = '今日交接列表_' . time() . '.xlsx';
        $header   = ['类型','标题', '内容', '创建人', '状态', '备注', '创建时间', '修改时间'];
        array_unshift($resData, $header); //添加头部标题
        return $this->json(200, $fileName, $resData);
    }
}