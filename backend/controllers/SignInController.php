<?php
namespace backend\controllers;

use backend\models\Train;
use backend\models\TrainCourses;
use backend\models\TrainUser;
use backend\models\UserInfo;
use yii\web\NotFoundHttpException;
use Yii;

class SignInController extends BaseController{

    /**
     * 签到列表
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionSignInList($id){
        $model = new TrainUser();
        $train = Train::findOne($id);
        $data = $model->trainUserInfo($id);
        if(!empty($data) && !empty($data[0]['courses_id'])){
            $result = [ 'yes' => [], 'no'  => [],'else' => []];
            foreach ($data as $value){
                if($value['sign_in'] == $model::SIGN_IN_YES ){
                    $result['yes'][] = $value;

                }else{
                    if($value['status'] != TrainUser::STATUS_NORMAL && !empty($value['status'])){
                        $result['else'][] = $value;
                    }else{

                        $result['no'][] = $value;
                    }

                }
            }
        }else{
            throw new NotFoundHttpException('page is not found');
        }
        return $this->render('sign-in-list',['data' => $result,'model' => $train]);
    }

    /**
     * 签到
     * @return string
     */
    public function actionSignIn(){
        $post = Yii::$app->request->post();

        $userInfo = UserInfo::findOne(['badge_code' =>  trim($post['code'])]);
        if(empty($userInfo)){
            return $this->json(100,'用户不存在');
        }

        $train = Train::findOne($post['train_id']);
        if($train->start_time > time()){
            return $this->json(100,'培训未开始！');
        }
        $trainUser = TrainUser::findOne(['user_id' => $userInfo->id,'train_id' =>$post['train_id'] ,'sign_in' =>TrainUser::SIGN_IN_NO]);
        if(empty($trainUser)){
            return $this->json(100,'已签到或未参与本次培训！');
        }

        if($trainUser->signIn() && $train->updateProgress()){
            return $this->json(200,'签到成功！');

        }else{
            return $this->json(100,'签到失败！请稍后再试！');

        }
    }

    /**
     * 修改学院状态
     * @return string
     */
    public function actionUpdateUserStatus(){
        if(Yii::$app->request->isAjax){
            $post = Yii::$app->request->post();
            $model = TrainUser::findOne($post['id']);
            if($model){
                $model->status = $post['status'];
                if(!$model->save()){
                    return $this->json(100,$model->errorToString());
                }
                $train = Train::findOne($model->train_id);

                if(!empty($train) && $train->updateProgress()){
                    return $this->json(200,'保存成功！');
                }else{
                    return $this->json(100,$train->errorToString());
                }

            }
            return $this->json(100,'非法请求！');
        }
    }


}