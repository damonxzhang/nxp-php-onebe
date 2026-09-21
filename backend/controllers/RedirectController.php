<?php
namespace backend\controllers;

use Yii;
use backend\models\UserInfo;


class RedirectController extends BaseController
{
    public function actionNotice()
    {


        $userInfo     = Yii::$app->user->identity->userInfo;
        $user_id = $userInfo->id;
        $user_id_md5 = md5($user_id);
        print_r($userInfo->id.'----');
        print_r($user_id_md5);
    }

    public function actionInspection()
    {
        $userInfo     = Yii::$app->user->identity->userInfo;
        $user_id = $userInfo->id;
        $user_id_md5 = md5($user_id);

        $url = 'http://onebe.ap.freescale.net:8686/login?userId='.$user_id.'&secret='.$user_id_md5;

        $temp = '<script>window.open(\''.$url.'\');</script>';

        return $temp;
    }

    
    public function actionMold()
    {
        $userInfo     = Yii::$app->user->identity->userInfo;
        $user_id = $userInfo->id;
        $user_id_md5 = md5($user_id);

        $url = 'http://onebe.ap.freescale.net:8585/login?userId='.$user_id.'&secret='.$user_id_md5;

        $temp = '<script>window.open(\''.$url.'\');</script>';

        return $temp;
    }


    public function actionFlowmanage()
    {
        // $userInfo     = Yii::$app->user->identity->userInfo;
        // $user_id = $userInfo->id;
        // $user_id_md5 = md5($user_id);

        $url = 'http://10.193.226.115:9090/flow';

        $temp = '<script>window.open(\''.$url.'\');</script>';

        return $temp;
    }

    public function actionBing()
    {
        $userInfo     = Yii::$app->user->identity->userInfo;
        $user_id = $userInfo->id;
        $user = UserInfo::findOne($user_id);


        $url = 'http://10.193.226.115:8080/FlowControl?id='.$user->id.'&user='.$user->chinese_name.'&pid='.$user->pid.'&account='.$user->english_name.'&area='.$user->area;
        // print_r($url);die;
        // $temp = '<script>frametop.location.href="https://bing.com/";</script>';
        // $temp = '<iframe src="\''.$url.'\'" name="obj" frameborder="0"></iframe>';
        $temp = '<script>window.location.href="'.$url.'";</script>';
        return $temp;
    }

    
    public function actionShowArea()
    {
        $url = 'http://onebe.ap.freescale.net:9001/show-area/index';

        $temp = '<script>window.open(\''.$url.'\');</script>';
        return $temp;
    }
    
    public function actionShowView()
    {

        $url = 'http://onebe.ap.freescale.net:9001/show-view/index';

        $temp = '<script>window.open(\''.$url.'\');</script>';
        return $temp;
    }

    public function actionShowChart()
    {

        $url = 'http://onebe.ap.freescale.net:9001/show-chart/index';

        $temp = '<script>window.open(\''.$url.'\');</script>';
        return $temp;
    }
}
