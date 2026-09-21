<?php

namespace backend\controllers;

use backend\models\search\UserGroupSearch;
use backend\models\UserGroup;
use backend\models\UserInfo;
use Yii;
use yii\data\Pagination;

class UserGroupController extends BaseController
{

    /**
     * 用户分组列表
     * @return string
     */
    public function actionIndex()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new UserGroupSearch();
        $data         = $searchModel->search($request);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        return $this->render('index', [
            'request'      => $request,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination
        ]);
    }


    /**
     * 添加分组
     * @return string
     */
    public function actionCreate()
    {
        if (Yii::$app->request->isPost) {
            $post  = Yii::$app->request->post();
            $model = new UserGroup();
            $res   = $model->create($post);
            if ($res['status'] != 200) {
                return $this->json(100, $res['msg']);
            }
            return $this->json(200, $res['msg']);
        }
        $connectUser = (new UserInfo())->getConnectUserList();
        return $this->render('create', ['connectUser' => $connectUser]);
    }

    /**
     * 修改分组
     * @return string
     * @throws \yii\base\Exception
     */
    public function actionUpdate()
    {
        if (Yii::$app->request->isPost) {
            $post  = Yii::$app->request->post();
            $model = new UserGroup();
            $check = $model->check($post);
            if (!$check) {
                return $this->json(100, '分组名称已存在!');
            }
            $res = $model->edit($post);
            if ($res['status'] != 200) {
                return $this->json(100, $res['msg']);
            }
            return $this->json(200, $res['msg']);
        }
        $id            = Yii::$app->request->get('id');
        $model         = UserGroup::findOne($id);
        $acceptUserArr = explode(',', $model->group_id);
        $connectUser   = (new UserInfo())->getConnectUserList();
        return $this->render('update',
            [
                'id'            => $id,
                'model'         => $model,
                'acceptUserArr' => $acceptUserArr,
                'connectUser'   => $connectUser
            ]);
    }

}

