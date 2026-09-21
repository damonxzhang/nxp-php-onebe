<?php

namespace backend\controllers;

use backend\models\Base;
use backend\models\BlogFile;
use backend\models\BlogMaterial;
use backend\models\BlogShow;
use backend\models\UserGroup;
use backend\models\UserStation;
use Yii;
use backend\models\Blog;
use backend\models\BlogDetails;
use backend\models\BlogType;
use backend\models\BlogUser;
use backend\models\Notice;
use backend\models\NoticeUser;
use backend\models\search\BlogSearch;
use backend\models\UserInfo;
use common\models\SendEmail;
use common\models\Utility;
use yii\data\Pagination;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class BlogController extends BaseController
{
    public $enableCsrfValidation = false;

    /**
     * 日志列表
     * @return string
     */
    public function actionIndex()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new BlogSearch();
        $data         = $searchModel->search($request);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        // $uid          = (new Base())->getUid();
        $userInfo     = Yii::$app->user->identity->userInfo ; 
        return $this->render('index', [
            'userInfo'     => $userInfo,
            'request'      => $request,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination
        ]);
    }

    /**
     * 上传文件
     * @return string
     */
    public function actionUpload()
    {
        $result = $this->upload('/blog/');
        return Json::encode($result);
    }

    /**
     * 添加日志文件
     * @param $param
     * @param $key
     * @return bool
     * @throws \yii\db\Exception
     */
    public function blogFile($param, $key = 'blog_id')
    {
        //检查是否上传文件
        if (!isset($param['upload_file'])) {
            return true;
        }
        $data = [];
        $time = date('Y-m-d H:i:s', time());
        foreach ($param['upload_file'] as $file) {
            $path      = pathinfo($file);
            $extension = $path['extension'];
            $data[]    = [$param[$key], $file, $path['filename'], $extension, $time];
        }
        $insertFlowNode = Yii::$app->db->createCommand()->batchInsert(BlogFile::tableName(), [$key, 'file_path', 'file_name', 'file_extension', 'create_time'], $data)->execute();
        if ($insertFlowNode > 0) {
            return true;
        }
        return false;
    }


    /**
     * 添加日志草稿文件
     * @param $param
     * @param $key
     * @return bool
     * @throws \yii\db\Exception
     */
    public function blogDraftFiles($param)
    {
        //删除原有数据
        BlogFile::deleteAll(['blog_id' => $param['blog_id']]);
        //检查是否上传文件
        if (!isset($param['upload_file'])) {
            return true;
        }
        $data = [];
        $time = date('Y-m-d H:i:s', time());
        foreach ($param['upload_file'] as $file) {
            $path      = pathinfo($file);
            $extension = $path['extension'];
            $data[]    = [$param['blog_id'], $file, $path['filename'], $extension, $time];
        }
        $insertFlowNode = Yii::$app->db->createCommand()->batchInsert(BlogFile::tableName(), ['blog_id', 'file_path', 'file_name', 'file_extension', 'create_time'], $data)->execute();
        if ($insertFlowNode > 0) {
            return true;
        }
        return false;
    }

    /**
     * 添加日志
     * @return string
     * @throws Exception
     */
    public function actionCreate()
    {
        //获取日志信息
        if (Yii::$app->request->isPost) {
            $post            = Yii::$app->request->post();
            $post['user_id'] = $this->getUid();
            $post['status']  = Blog::STATUS_PROCESSING;
            $transaction     = Yii::$app->db->beginTransaction();
            //添加日志数据
            $post['group_id'] = 1;
            $post['is_draft'] = '';
            $blog = (new Blog())->create($post);
            if (!$blog) {
                $transaction->rollBack();
                return $this->json(100, '添加日志失败!');
            }

            $post['blog_id'] = $blog->id;
            $post['define']  = 'create';
            //添加日志材料
            $trainUser = $this->blogFile($post);
            if (!$trainUser) {
                $transaction->rollBack();
                return $this->json(100, '添加日志文件失败!');
            }
            //添加日志类型资料
            $result =(new BlogMaterial())->saveblogMaterial($post);
            if (true !== $result) {
                $transaction->rollBack();
                return $this->json(100, $result);
            }
            //发送消息
            $sendMessage = $this->sendMessage($post);
            if (!$sendMessage) {
                return $this->json(100, '添加日志失败!!');
            }
            $userIds = explode(',',$post['handover_id']);

            //发送邮件
            $this->sendRemindEmailByUsers($userIds,$blog,'New Handover Record');


            $transaction->commit();
            //
            if(!empty($post['draft_id'])){
                $this->actionDeleteDraft($post['draft_id']);
            }
            return $this->json(200, '日志添加成功!');
        }
        $uid         = $this->getUid();
        $userInfo    = UserInfo::findOne(['id' => $uid]);
        $section = ['id'=>$userInfo['section'],'value' =>  UserInfo::$sectionArr[$userInfo['section']]];
        $type        = (new BlogType())->getType();
        $connectUser = (new UserInfo())->getSearchDefaultUser($userInfo->section);
        $userStations =  UserStation::find()->where(['status' => 1])->select('id,station')->asArray()->all();
        return $this->render('create', [
            'uid'         => $uid,
            'type'        => $type,
            'connectUser' => $connectUser,
            'userStations' => $userStations,
            'section' => $section
        ]);
    }


    /**
     * 获取分组人员信息
     * @return string
     */
    public function actionUserGroup()
    {
        $id     = Yii::$app->request->get('id');
        $blogId     = Yii::$app->request->get('blogId','');
        $result = (new UserGroup())->getGroupIdName($id);
        return Json::encode($result);
    }

    /**
     * 修改日志
     * @return string
     * @throws Exception
     */
    public function actionUpdate()
    {
        if (Yii::$app->request->isPost) {
            $post            = Yii::$app->request->post();
            $post['user_id'] = $this->getUid();
            $model           = Blog::findOne($post['id']);
            $post['title']   = $model->title;
            $userId          = $model->user_id;
            if ($post['define'] == 'history' && $post['status'] == Blog::STATUS_PROCESSING && $post['user_id'] != $userId) {
                return $this->json(100, '抱歉只有发起人才能将状态由已完成变为进行中!');
            }
            //状态变为已完成并且是必读
            if ($post['status'] == Blog::STATUS_SUCCESS && $model->read_all == Blog::READ_TRUE) {
                if ($post['user_id'] != $userId) {
                    return $this->json(100, '抱歉只有发起人才能变更状态为已完成!');
                }
                //交接人列表
                $accepyUserIdList = (new BlogUser())->getAcceptUserId($post['id']);
                //阅读人列表
                $detailIdList = (new BlogDetails())->getDetailUserId($post['id']);
                //有不相同的
                if (array_diff($accepyUserIdList, $detailIdList)) {
                    return $this->json(100, '必读日志尚有人未回复,状态不能变为已完成!');
                }
            }
            $transaction = Yii::$app->db->beginTransaction();
            $post['group_id'] = 1;
            $detailId    = (new Blog())->edit($post);
            if (!$detailId) {
                $transaction->rollBack();
                return $this->json(100, '编辑日志失败!');
            }
            $post['define']       = 'update';
            $post['detail_id']    = $detailId;
            $post['content_data'] = $post['reply'];
            $post['blog_id']      = $post['id'];

            //添加上传文件
            $trainUser = $this->blogFile($post, $key = 'detail_id');
            if (!$trainUser) {
                $transaction->rollBack();
                return $this->json(100, '编辑日志失败!!');
            }

            //添加日志类型资料
            $result = (new BlogMaterial())->updateblogMaterial($post);
            if (true !== $result) {
                $transaction->rollBack();
                return $this->json(100, $result);
            }

            //发消息
            $sendMessage = $this->sendMessage($post);
            if (!$sendMessage) {
                return $this->json(100, '编辑日志失败!!!');
            }
            $newBlogModel = Blog::findOne($model->id);
            //如果状态变为已完成就给创建人发邮件
            if ($post['status'] == Blog::STATUS_SUCCESS) {

                $this->sendRemindEmailByUsers($model['user_id'],$newBlogModel,'New Handover Record');
            } else {

                $userIds = explode(',',$post['handover_id']);
                $this->sendRemindEmailByUsers($userIds,$newBlogModel,'New Handover Record');
            }
            $transaction->commit();
            return $this->json(200, '编辑日志成功!');
        }
        $type          = (new BlogType())->getType();
        $id            = Yii::$app->request->get('id');
        $define        = Yii::$app->request->get('define', '');
        $model         = Blog::findOne(['id' => $id]);


        $userId        = $this->getUid();
        $userInfo      = UserInfo::findOne(['id' => $userId]);
        $connectUser   = (new UserInfo())->getBlogUserList($userInfo->area);
        $acceptUserArr = (new BlogUser())->getAcceptUserId($id);
        $blogFile      = (new BlogFile())->checkBlogFileData($id);
        (new NoticeUser())->updateStatus(['user_id' => $userId, 'connect_id' => $model->id]);//更新消息为已读
        $detailModel = BlogDetails::find()->where(['blog_id' => $id])->orderBy(['create_time' => SORT_DESC])->all();
        $blogMaterial  = BlogMaterial::findAll(['blog_id' => $model->id]);

        $section = ['id'=>$userInfo['section'],'value' =>  UserInfo::$sectionArr[$userInfo['section']]];

        $userStations =  UserStation::find()->where(['status' => 1])->select('id,station')->asArray()->all();
        return $this->render('update', [
            'id'            => $id,
            'model'         => $model,
            'blogMaterial'  => $blogMaterial,
            'type'          => $type,
            'define'        => $define,
            'blogFile'      => $blogFile,
            'acceptUserArr' => $acceptUserArr,
            'connectUser'   => $connectUser,
            'detailModel'   => $detailModel,
            'userStations' => $userStations,
            'section' => $section
        ]);
    }

    /**
     * 草稿箱
     * @return string
     */
    public function actionBlogDraft(){
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new BlogSearch();
        $data         = $searchModel->searchDraft($request);
        $dataProvider = $data->getModels();
        $uid          = (new Base())->getUid();
        return $this->render('draft', [
            'uid'          => $uid,
            'request'      => $request,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'   => $data->pagination
        ]);
    }
    /**
     * 添加日志到草稿箱
     * @return string
     * @throws Exception
     */
    public function actionCreateDraft()
    {
        //获取日志信息
        if (Yii::$app->request->isPost) {
            $post            = Yii::$app->request->post();
            $post['user_id'] = $this->getUid();
            $post['status']  = Blog::STATUS_PROCESSING;

            $transaction     = Yii::$app->db->beginTransaction();
            //添加日志数据
            $post['group_id'] = 1;
            $post['is_draft'] = BLog::IS_DRAFT_YES;
            $blogInsert = (new Blog())->create($post);
            if (!$blogInsert) {
                $transaction->rollBack();
                return $this->json(100, '添加日志失败!');
            }
            $post['blog_id'] = $blogInsert->id;
            $post['define']  = 'create';
            if(!$this->saveBlogUser($post)){
                $transaction->rollBack();
                return $this->json(100, '编辑日志失败!!');
            }

            //添加日志材料
            $trainUser = $this->blogFile($post);
            if (!$trainUser) {
                $transaction->rollBack();
                return $this->json(100, '添加日志文件失败!');
            }
            //添加日志类型资料
            $result =(new BlogMaterial())->saveblogMaterial($post);
            if (true !== $result) {
                $transaction->rollBack();
                return $this->json(100, $result);
            }
            $transaction->commit();
            return $this->json(200, '保存成功!');

        }
        return $this->json(100, '非法请求！');

    }

    /**
     * 更新日志草稿
     * @return string
     * @throws Exception
     */
    public function actionUpdateDraft(){

        if (Yii::$app->request->isPost) {
            $post            = Yii::$app->request->post();
            $post['user_id'] = $this->getUid();
            $model           = Blog::findOne($post['draft_id']);
            $transaction = Yii::$app->db->beginTransaction();
            $model->setAttributes($post);
            if (!$model->save()) {
                $transaction->rollBack();
                return $this->json(100, $model->errorToString());
            }
            $post['detail_id'] =  $model->id;
            $post['blog_id']   = $post['draft_id'];

            if(!$this->saveBlogUser($post)){
                $transaction->rollBack();
                return $this->json(100, '编辑日志失败!!');
            }
            //添加日志类型资料
            $result =(new BlogMaterial())->updateblogMaterial($post);
            if (true !== $result) {
                $transaction->rollBack();
                return $this->json(100, $result);
            }
            //添加上传文件
            $trainUser = $this->blogDraftFiles($post, $key = 'blog_id');
            if (!$trainUser) {
                $transaction->rollBack();
                return $this->json(100, '编辑日志失败!!');
            }

            $transaction->commit();
            return $this->json(200, '编辑日志成功!');
        }
        $type          = (new BlogType())->getType();
        $id            = Yii::$app->request->get('id');
        $model         = Blog::findOne(['id' => $id]);
        $userId        = $this->getUid();
        $userInfo      = UserInfo::findOne(['id' => $userId]);
        $connectUser   = (new UserInfo())->getBlogUserList($userInfo->area);
        $acceptUserArr = (new BlogUser())->getAcceptUserId($id);
        $blogFile      = (new BlogFile())->getBlogFileData(['blog_id' => $id]);
        $detailModel = BlogDetails::find()->where(['blog_id' => $id])->orderBy(['create_time' => SORT_DESC])->all();
        $blogMaterial  = BlogMaterial::findAll(['blog_id' => $model->id]);
        $section = ['id'=>$userInfo['section'],'value' =>  UserInfo::$sectionArr[$userInfo['section']]];

        $userStations =  UserStation::find()->where(['status' => 1])->select('id,station')->asArray()->all();

        return $this->render('update-draft', [
            'id'            => $id,
            'model'         => $model,
            'type'          => $type,
            'blogFile'      => $blogFile,
            'acceptUserArr' => $acceptUserArr,
            'connectUser'   => $connectUser,
            'detailModel'   => $detailModel,
            'userStations'  => $userStations,
            'blogMaterial'  => $blogMaterial,
            'section'       => $section
        ]);
    }

    /**
     * 删除草稿和对应的用户和图片
     * @param $id
     * @return string
     */
    public function actionDeleteDraft($id){
        $model = Blog::findOne(['id' => $id,'is_draft' => Blog::IS_DRAFT_YES]);
        if(empty($model)){
            return $this->json(100,'数据错误！');
        }

        if($model->deleteDraf()){
            return $this->json(200,'删除成功！');
        }else{
            return $this->json(100,$model->errorToString());
        }
    }

    /**
     * 发送邮件
     * @param $param
     * @return bool
     */
    public function sendEmailToUser($param)
    {
        $userIdList = explode(',', $param['handover_id']);
        if (empty($userIdList)) {
            return false;
        }
        $param['user_list'] = $userIdList;
        $param['type']      = Notice::TYPE_BLOG;
        $userName           = (new UserInfo())->getUserName($param['user_id']);
        $status             = ArrayHelper::getValue(Blog::$statusArr, $param['status'], '');
        $param['message']   = '用户:' . $userName . ' ,创建/更新了日志, ID:' . $param['blog_id'] . ',标题为: ' . $param['title'] . '的日志,日志状态为:' . $status;
        $sendEmail          = (new SendEmail())->sendEmailUserList($param);
        if ($sendEmail) {
            return true;
        }
        return false;
    }

    /**
     * 循环发送提醒邮件
     * @param $user
     * @param Blog $blog
     * @param $subject
     */
    public function sendRemindEmailByUsers($userIds,Blog $blog,$subject)
    {
        $userInfo = new UserInfo();
        $users = $userInfo->getUserEmail($userIds);
        $createUser = $userInfo->getUserName($blog->user_id);

        if(!empty($users)){
            foreach ($users as $user){
                $this->sendRemindEmail($user,$createUser,$blog,$subject);
            }
        }
    }

    /**
     * 发送邮件
     * @param $user
     * @param $createUser
     * @param $blog
     * @param $subject
     */
    public function sendRemindEmail($user,$createUser,$blog,$subject){

        $html =  $this->renderFile('@common/mail/blog/blog-email-view.php',[
            'params' => $blog,
            'userName' => $user['chinese_name'],
            'createUser' => $createUser
        ]);
        $mail = Yii::$app->mailer->compose( );
        $mail->setHtmlBody($html);
        $mail->setSubject($subject);
        $mail->setTo($user['email']);
        @$mail->send();
    }

    /**
     * 讲日志添加到展示列表
     * @return string
     * @throws Exception
     */
    public function actionAddShow(){
        $request = Yii::$app->request->queryParams;
        $idList  = ArrayHelper::getValue($request, 'idlist', []);
        $status  = ArrayHelper::getValue($request, 'status', '');
        if (!empty($idList)) {
            $uid = $this->getUid();

            $idArray = Blog::find()
                ->select('id')
                ->where(['in', 'id', $idList])
                ->andFilterWhere(['status' => $status])
                ->column();
            if(!empty($idArray)){
                $reload = false;
                foreach ($idArray as $value){
                    $model = new BlogShow();
                    $model->setAttributes([
                        'user_id' => $uid,
                        'log_id'  => $value,
                        'status'  => BlogShow::STATUS_SHOW,
                    ]);
                    if(!$model->save()){
                        $reload = true;
                    }
                }

                //$res = Yii::$app->db->createCommand()->batchInsert(HandoverShow::tableName(),['user_id','log_id','status','created_at'],$params)->execute();

                if(empty($reload)){

                    return  $this->json(200, '保存成功');
                }else{

                    return  $this->json(200, '保存成功,自动过滤重复添加日志 ');
                }
            }else{

                return  $this->json(100, '数据异常');
            }
        } else {

            return  $this->json(100, '请选择日志');
        }
    }

    /**
     * 保存交接人
     * @param $param
     * @return bool
     * @throws Exception
     */
    public function saveBlogUser($param){
        $userId = $this->getUid();
        $data     = [];
        $time     = date('Y-m-d H:i:s', time());
        $hanover  = explode(',', $param['handover_id']);
        //删除原关联数据
        BlogUser::deleteAll([
            'blog_id'        => $param['blog_id'],
            'create_user_id' => $userId
        ]);

        foreach ($hanover as $id) {
            $data[] = [
                'blog_id'        => $param['blog_id'],
                'detail_id'      => $param['blog_id'],
                'create_user_id' => $userId,
                'accept_user_id' => $id,
                'type'           => $param['type'],
                'create_time'    => $time
            ];
        }
        //添加日志交接人信息
        $result = $this->insertData(BlogUser::tableName(), ['blog_id', 'detail_id', 'create_user_id', 'accept_user_id', 'type', 'create_time'], $data);
        if (!$result) {

            return false;
        }else{
            return true;
        }
    }
    /**
     * 发送消息
     * @param $param
     * @return bool
     * @throws Exception
     */
    public function sendMessage($param)
    {
        $userId = $this->getUid();
        //添加日志信息
        $transaction = Yii::$app->db->beginTransaction();
        //获取交接人信息
        $data     = [];
        $time     = date('Y-m-d H:i:s', time());
        $hanover  = explode(',', $param['handover_id']);
        $detailId = $param['define'] == 'create' ? $param['blog_id'] : $param['detail_id'];
        foreach ($hanover as $id) {
            $data[] = [
                'blog_id'        => $param['blog_id'],
                'detail_id'      => $detailId,
                'create_user_id' => $userId,
                'accept_user_id' => $id,
                'type'           => $param['type'],
                'create_time'    => $time
            ];
        }
        //添加日志交接人信息
        $result = $this->insertData(BlogUser::tableName(), ['blog_id', 'detail_id', 'create_user_id', 'accept_user_id', 'type', 'create_time'], $data);
        if (!$result) {
            $transaction->rollBack();
            return false;
        }

        //添加通知标题
        $noticeData = [
            'title'        => $param['title'],
            'type'         => Notice::TYPE_BLOG,
            'content_data' => $param['content_data'],
        ];
        $noticeId   = (new Notice())->create($noticeData);
        if (!$noticeId) {
            $transaction->rollBack();
            return false;
        }

        //添加通知人
        $status     = NoticeUser::STATUS_UNREAD;
        $time       = date('Y-m-d H:i:s', time());
        $NoticeData = [];
        foreach ($hanover as $uId) {
            $NoticeData[] = [$noticeId, $uId, $status, $time, $param['blog_id']];
        }

        //添加日志通知人信息
        $insertFlowNode = $this->insertData(NoticeUser::tableName(), ['notice_id', 'user_id', 'status', 'create_time', 'connect_id'], $NoticeData);
        if (!$insertFlowNode) {
            $transaction->rollBack();
            return false;
        }
        $transaction->commit();
        return true;
    }

    /**
     * 日志反馈
     * @return string
     */
    public function actionFeedback()
    {
        $blogId       = Yii::$app->request->get('id');
        $model        = Blog::findOne(['id' => $blogId]);
        $dataProvider = BlogDetails::find()->where(['blog_id' => $blogId])->orderBy(['create_time' => SORT_DESC])->all();
        return $this->render('feedback', [
            'title'        => $model->title,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * 日志详情
     * @return string
     */
    public function actionDetail()
    {
        $id       = Yii::$app->request->get('id');
        $model    = Blog::findOne($id);
        $baseUrl  = Yii::$app->params['base_url'];
        $blogFile = (new BlogFile())->getBlogFileData(['blog_id' => $id]);
        return $this->render('detail', ['model' => $model, 'baseUrl' => $baseUrl, 'blogFile' => $blogFile]);
    }

    /**
     * 历史日志
     * @return string
     */
    public function actionHistory()
    {
        $request           = Yii::$app->request->queryParams;
        $request['status'] = Blog::STATUS_SUCCESS;
        $searchModel       = new BlogSearch();
        $data              = $searchModel->search($request);
        $totalCount        = $data->totalCount;
        $dataProvider      = $data->getModels();
        $pagination        = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        return $this->render('history', [
            'request'      => $request,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination
        ]);
    }

    /**
     * 查看此次修改日志
     * @return string
     */
    public function actionSelf()
    {
        $id             = Yii::$app->request->get('id');
        $type           = (new BlogType())->getType();
        $detailModel    = BlogDetails::findOne(['id' => $id]);
        $model          = Blog::findOne(['id' => $detailModel->blog_id]);
        $blogFile       = (new BlogFile())->getBlogFileData(['detail_id' => $id]);
        $detailUserName = (new BlogUser())->getUserName($model->id);
        return $this->render('self', [
            'model'          => $model,
            'detailUserName' => $detailUserName,
            'detailModel'    => $detailModel,
            'type'           => $type,
            'blogFile'       => $blogFile
        ]);
    }

    /**
     * 全部反馈状态
     * @return string
     */
    public function actionView()
    {
        $blogId      = Yii::$app->request->get('blog_id');
        $baseurl     = Yii::$app->params['base_url'];
        $detailModel = BlogDetails::find()->where(['blog_id' => $blogId])->orderBy(['create_time' => SORT_ASC])->all();
        $model       = Blog::findOne(['id' => $blogId]);
        $connectUser = (new UserInfo())->getConnectUserList();
        $type        = (new BlogType())->getType();
        return $this->render('view', [
            'type'        => $type,
            'model'       => $model,
            'baseurl'     => $baseurl,
            'detailModel' => $detailModel,
            'connectUser' => $connectUser
        ]);
    }

    /**
     * 导出数据
     * @return string
     */
    public function actionExport()
    {
        $request = Yii::$app->request->queryParams;
        $idList  = ArrayHelper::getValue($request, 'idlist', []);
        $status  = ArrayHelper::getValue($request, 'status', '');
        if (!empty($idList)) {
            $query = Blog::find();
            $query->where(['in', 'id', $idList]);
            $query->andFilterWhere(['status' => $status]);
            $query->asArray();
            $dataProvider = $query->all();
        } else {
            $query = (new BlogSearch())->getSearchQuery($request);
            $query->asArray();
            $dataProvider = $query->all();
        }
        $data = [];
        if (!empty($dataProvider)) {
            foreach ($dataProvider as $key => $value) {
                $data[$key][] = $value['title'];
                $data[$key][] = $value['content_data'];
                $data[$key][] = (new UserInfo())->getUserName($value['user_id']);
                $data[$key][] = ArrayHelper::getValue(Blog::$statusArr, $value['status'], '');
                $data[$key][] = date('Y-m-d H:i:s', strtotime($value['create_time']));
                $data[$key][] = date('Y-m-d H:i:s', strtotime($value['update_time']));
            }
        }
        $fileName = '日志列表_' . time() . '.xlsx';
        $header   = ['标题', '内容', '创建人', '状态', '创建时间', '更新时间'];
        array_unshift($data, $header); //添加头部标题
        return $this->json(200, $fileName, $data);
    }
}