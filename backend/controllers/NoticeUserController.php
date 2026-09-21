<?php
namespace backend\controllers;

use backend\models\Blog;
use backend\models\BlogDetails;
use backend\models\Notice;
use backend\models\NoticeUser;
use backend\models\search\BlogSearch;
use backend\models\search\NoticeUserSearch;
use backend\models\UserInfo;
use common\models\UploadForm;
use Yii;
use yii\data\Pagination;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class NoticeUserController extends BaseController
{

    /**
     * 列表
     * @return string
     */
    public function actionIndex()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new NoticeUserSearch();
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
     * 通知签到
     * @return string
     */
    public function actionDetail()
    {
        $Id     = Yii::$app->request->get('id');
		$model  = NoticeUser::findOne(['id' => $Id]);
        $notice = Notice::findOne(['id' => $model->notice_id]);
        return $this->render('detail', ['notice' => $notice,'noticeUserId' => $Id]);
    }

    /**
     * 修改查阅状态
     * @return string
     */
    public function actionChangeStatus()
    {
        $post  = Yii::$app->request->post();
        $uId   = $this->getUid();
        $model = NoticeUser::findOne(['id' => $post['notice_user_id']]);
        $model->status = NoticeUser::STATUS_READ;
        $model->notice_time = date('Y-m-d H:i:s', time());
        if (!$model->save(false)) {
            return $this->json(100, '查阅失败!');
        }
        $checkNotice = (new NoticeUser()) -> checkNoticeStatus($post['id']);
        if($checkNotice){
            $model = Notice::findOne(['id' => $post['id']]);
            $model->status = Notice::STATUS_COMPLETED;
            $model->save(false);
        }
        return $this->json(200, '查阅成功!');
    }

    /**
     * 通知详情
     * @return string
     */
    public function actionView()
    {
        $Id     = Yii::$app->request->get('id');
        $notice = Notice::findOne(['id' => $Id]);
        return $this->render('view', ['notice' => $notice]);
    }
}