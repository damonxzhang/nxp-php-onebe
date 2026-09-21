<?php
namespace backend\controllers;

use backend\models\Base;
use backend\models\Gemba;
use backend\models\search\UserInfoSearch;
use backend\models\Team;
use backend\models\UserInfo;
use Yii;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class GembaUserController extends BaseController{

    public function actionIndex(){

        $request      = Yii::$app->request->queryParams;
        $searchModel  = new UserInfoSearch();
        $data         = $searchModel->gembaSearch($request);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        $uid = $this->getUid();
        return $this->render('index', [
            'uid'          => $uid,
            'request'      => $request,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination
        ]);
    }

    public function actionUpdate($id){

        $userInfo = Yii::$app->user->identity->userInfo;
        $model = UserInfo::findOne($id);

        if($userInfo->gemba_role !== Gemba::ADMIN_ROLE){
            return $this->json(100,'没有权限！');
        }
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            if($model->gemba_role === Gemba::ADMIN_ROLE){
                return $this->json('false','管理员权限无法在此处进行更改！');
            }
            $model->setScenario($model::SCENARIO_UPDATE_GEMBA);

            $model->gemba_role = $post['gemba_role'];
            //权限选空 同步小组为空
            if(empty($model->gemba_role)){

                $model->team = '';
            }else{

                $model->team        = $post['team'];
            }

            if($model->save()){
                return $this->json(200,'保存成功！');
            }else{
                return $this->json(100,'保存失败！');
            }
        }
        $gembaTeam = ArrayHelper::map(Team::find()->where(['status' => Team::STATUS_YES])->all(),'id','teamName');

        return $this->render('update',['model' => $model,'gembaTeam' => $gembaTeam]);

    }

    /**
     * 新增gemba人员
     * @return string
     */
    public function actionCreate(){

        $userInfo = Yii::$app->user->identity->userInfo;
        if($userInfo->gemba_role !== Gemba::ADMIN_ROLE){
            return $this->json(100,'没有权限！');
        }
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            if(empty($post['gemba_role']) || empty($post['user_id']) || !in_array($post['gemba_role'],array_keys(Gemba::$gembaRole))){
                return $this->json(100,'参数错误！');
            }
            $userIds = explode(',',$post['user_id']);
            $models = UserInfo::findAll($userIds);
            if(empty($models)){
                return $this->json(100,'用户不存在！');
            }
            foreach ($models as $model){
                //如果用户已经拥有权限  则跳过
                if(!empty($model->gemba_role) ){
                    continue;
                }
                $model->setScenario($model::SCENARIO_UPDATE_GEMBA);
                $model->gemba_role = $post['gemba_role'];
                $model->team        = $post['team'];

                if(!$model->save()){
                    return $this->json(100,$model->errorToString());
                }
            }
            return $this->json(200,'保存成功！');
        }
        $connectUser = (new UserInfo())->getBlogUserList();
        $gembaTeam = ArrayHelper::map(Team::find()->where(['status' => Team::STATUS_YES])->all(),'id','teamName');
        return $this->render('create',['connectUser' => $connectUser,'gembaTeam' => $gembaTeam]);

    }

}