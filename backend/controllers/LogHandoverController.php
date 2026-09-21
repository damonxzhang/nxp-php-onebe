<?php

namespace backend\controllers;

use Yii;
use yii\data\Pagination;
use backend\models\UserInfo;
use yii\helpers\ArrayHelper;
use backend\models\LogHandover;
use backend\models\LogHandoverType;
use backend\models\LogHandoverBatch;
use backend\models\LogHandoverCommentType;
use backend\models\LogHandoverUser;
use backend\models\LogHandoverDefault;
use backend\models\LogHandoverDeviceType;
use backend\models\LogHandoverSensitiveWords;
use backend\models\search\LogHandoverSearch;
use backend\models\search\LogHandoverAuditSearch;
use backend\models\search\LogHandoverTypeSearch;
use backend\models\search\LogHandoverUserSearch;
use backend\models\search\LogHandoverBatchSearch;
use backend\models\search\LogHandoverCommentTypeSearch;
use backend\models\search\LogHandoverDefaultSearch;
use backend\models\search\LogHandoverDeviceTypeSearch;
use backend\models\search\LogHandoverSensitiveWordsSearch;

class LogHandoverController extends BaseController
{

    protected $sectionArr = [
        1  => 'Non-Lead',
        2  => 'Leaded',
        3  => 'PM',
        4  => 'Plating',
    ];
    
    
    /**
     * 列表
     * @return string
     */
    public function actionIndex()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new LogHandoverTypeSearch();
        $data         = $searchModel->search($request);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        return $this->render('index', [
            'sectionArr'   => $this->sectionArr,
            'request'      => $request,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination
        ]);
    }


    /**
     * 物料种类列表
     * @return string
     */
    public function actionType()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new LogHandoverTypeSearch();
        $data         = $searchModel->search($request);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        return $this->render('type', [
            'sectionArr'   => $this->sectionArr,
            'request'      => $request,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination
        ]);
    }


    /**
     * 添加物料种类
     * @return string
     */
    public function actionTypeCreate()
    {
        if (Yii::$app->request->isPost) {
            $post  = Yii::$app->request->post();
            $post['user_id'] = $this->getUid();
            $model = new LogHandoverType();
            $res   = $model->create($post);
            if ($res['status'] != 200) {
                return $this->json(100, $res['msg']);
            }
            return $this->json(200, $res['msg']);
        }
        return $this->render('type-create',[ 'sectionArr' => $this->sectionArr]);
    }

    /**
     * 更新物料种类
     * @return string
     * @throws \yii\base\Exception
     */
    public function actionTypeUpdate()
    {
        if (Yii::$app->request->isPost) {
            $post  = Yii::$app->request->post();
            $model = new LogHandoverType();
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
        $model         = LogHandoverType::findOne($id);
        return $this->render('type-update',['id' => $id,'model' => $model, 'sectionArr' => $this->sectionArr]);
    }



    /**
     * 批次种类列表
     * @return string
     */
    public function actionBatch()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new LogHandoverBatchSearch();
        $data         = $searchModel->search($request);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        return $this->render('batch', [
            'sectionArr'   => $this->sectionArr,
            'request'      => $request,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination
        ]);
    }


    /**
     * 创建批次种类
     * @return string
     */
    public function actionBatchCreate()
    {
        if (Yii::$app->request->isPost) {
            $post  = Yii::$app->request->post();
            $post['user_id'] = $this->getUid();
            $model = new LogHandoverBatch();
            $res   = $model->create($post);
            if ($res['status'] != 200) {
                return $this->json(100, $res['msg']);
            }
            return $this->json(200, $res['msg']);
        }
        return $this->render('batch-create',[ 'sectionArr' => $this->sectionArr]);
    }

    /**
     * 更新批次种类
     * @return string
     * @throws \yii\base\Exception
     */
    public function actionBatchUpdate()
    {
        if (Yii::$app->request->isPost) {
            $post  = Yii::$app->request->post();
            $model = new LogHandoverBatch();
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
        $model         = LogHandoverBatch::findOne($id);
        return $this->render('batch-update',['id' => $id,'model' => $model, 'sectionArr' => $this->sectionArr]);
    }

 
    /**
     * 设备类型列表
     * @return string
     */
    public function actionDeviceType()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new LogHandoverDeviceTypeSearch();
        $data         = $searchModel->search($request);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        return $this->render('device-type', [
            'sectionArr'   => $this->sectionArr,
            'request'      => $request,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination
        ]);
    }


    /**
     * 创建设备类型
     * @return string
     */
    public function actionDeviceTypeCreate()
    {
        if (Yii::$app->request->isPost) {
            $post  = Yii::$app->request->post();
            $post['user_id'] = $this->getUid();
            $model = new LogHandoverDeviceType();
            $res   = $model->create($post);
            if ($res['status'] != 200) {
                return $this->json(100, $res['msg']);
            }
            return $this->json(200, $res['msg']);
        }
        return $this->render('device-type-create',[ 'sectionArr' => $this->sectionArr]);
    }

    /**
     * 更新设备类型
     * @return string
     * @throws \yii\base\Exception
     */
    public function actionDeviceTypeUpdate()
    {
        if (Yii::$app->request->isPost) {
            $post  = Yii::$app->request->post();
            $model = new LogHandoverDeviceType();
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
        $model         = LogHandoverDeviceType::findOne($id);
        return $this->render('device-type-update',['id' => $id,'model' => $model, 'sectionArr' => $this->sectionArr]);
    }   
    
    /**
     * 备注类型列表
     * @return string
     */
    public function actionCommentType()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new LogHandoverCommentTypeSearch();
        $data         = $searchModel->search($request);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        return $this->render('comment-type', [
            'sectionArr'   => $this->sectionArr,
            'request'      => $request,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination
        ]);
    }


    /**
     * 添加备注种类
     * @return string
     */
    public function actionCommentTypeCreate()
    {
        if (Yii::$app->request->isPost) {
            $post  = Yii::$app->request->post();
            $post['user_id'] = $this->getUid();
            $model = new LogHandoverCommentType();
            $res   = $model->create($post);
            if ($res['status'] != 200) {
                return $this->json(100, $res['msg']);
            }
            return $this->json(200, $res['msg']);
        }
        return $this->render('comment-type-create',[ 'sectionArr' => $this->sectionArr]);
    }

    /**
     * 更新备注种类
     * @return string
     * @throws \yii\base\Exception
     */
    public function actionCommentTypeUpdate()
    {
        if (Yii::$app->request->isPost) {
            $post  = Yii::$app->request->post();
            $model = new LogHandoverCommentType();
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
        $model         = LogHandoverCommentType::findOne($id);
        return $this->render('comment-type-update',['id' => $id,'model' => $model, 'sectionArr' => $this->sectionArr]);
    }

    
    
    /**
     * 敏感词列表
     * @return string
     */
    public function actionWords()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new LogHandoverSensitiveWordsSearch();
        $data         = $searchModel->search($request);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        return $this->render('words', [
            'sectionArr'   => $this->sectionArr,
            'request'      => $request,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination
        ]);
    }


    /**
     * 添加敏感词
     * @return string
     */
    public function actionWordsCreate()
    {
        if (Yii::$app->request->isPost) {
            $post  = Yii::$app->request->post();
            $post['user_id'] = $this->getUid();
            $model = new LogHandoverSensitiveWords();
            $res   = $model->create($post);
            if ($res['status'] != 200) {
                return $this->json(100, $res['msg']);
            }
            return $this->json(200, $res['msg']);
        }
        return $this->render('words-create',[ 'sectionArr' => $this->sectionArr]);
    }

    /**
     * 更新敏感词
     * @return string
     * @throws \yii\base\Exception
     */
    public function actionWordsUpdate()
    {
        if (Yii::$app->request->isPost) {
            $post  = Yii::$app->request->post();
            $model = new LogHandoverSensitiveWords();
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
        $model         = LogHandoverSensitiveWords::findOne($id);
        return $this->render('words-update',['id' => $id,'model' => $model, 'sectionArr' => $this->sectionArr]);
    }

    
    /**
     * 下拉选择框数据
     */
    public function selectLogHandoverArr()
    {
        $logHandoverTypeData        = LogHandoverType::find()->where(['status'=> 1])->orderBy(['id' => SORT_DESC])->asArray()->all();//物料种类名称
        $logHandoverBatchData       = LogHandoverBatch::find()->where(['status'=> 1])->orderBy(['id' => SORT_DESC])->asArray()->all();  //批次
        $logHandoverDeviceTypeData  = LogHandoverDeviceType::find()->where(['status'=> 1])->orderBy(['id' => SORT_DESC])->asArray()->all();//设备类型
        $logHandoverCommentTypeData = LogHandoverCommentType::find()->where(['status'=> 1])->orderBy(['id' => SORT_DESC])->asArray()->all();//备注
     
        $data['logHandoverTypeArr']        = empty($logHandoverTypeData) ? [] : ArrayHelper::map($logHandoverTypeData,'id','title');
        $data['logHandoverBatchArr']       = empty($logHandoverBatchData) ? [] :  ArrayHelper::map($logHandoverBatchData,'id','title');
        $data['logHandoverDeviceTypeArr']  = empty($logHandoverDeviceTypeData) ? [] : ArrayHelper::map($logHandoverDeviceTypeData,'id','title');
        $data['logHandoverCommentTypeArr'] = empty($logHandoverCommentTypeData) ? [] : ArrayHelper::map($logHandoverCommentTypeData,'id','title');

        return $data;

    }

    
    /**
     * 默认交接类型列表
     * @return string
     */
    public function actionDefault()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new LogHandoverDefaultSearch();
        $data         = $searchModel->search($request);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        $logHandoverArr = $this->selectLogHandoverArr();
        return $this->render('default', [
            'sectionArr'   => $this->sectionArr,
            'request'      => $request,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination,
            'logHandoverTypeArr'        => $logHandoverArr['logHandoverTypeArr'],
            'logHandoverBatchArr'       => $logHandoverArr['logHandoverBatchArr'],
            'logHandoverDeviceTypeArr'  => $logHandoverArr['logHandoverDeviceTypeArr'],
            'logHandoverCommentTypeArr' => $logHandoverArr['logHandoverCommentTypeArr']
        ]);
    }

    /**
     * 添加默认交接类型种类
     * @return string
     */
    public function actionDefaultCreate()
    {
        if (Yii::$app->request->isPost) {
            $post  = Yii::$app->request->post();
            $post['user_id'] = $this->getUid();
            $model = new LogHandoverDefault();
            $check = $model->check($post);
            if ($check) {
                return $this->json(100, '每个类型只能有一个默认交接内容!');
            }
            if($post['type'] == 1){//物料
                if(empty($post['type_id']) || empty($post['batch_id'])){
                    return $this->json(100, '请选择物料类型或者批次类型!');
                }
                $post['type_id']    = intval($post['type_id']);
                $post['batch_id']   = intval($post['batch_id']);
                $post['comment_id'] = $post['device_type_id'] = 0;
            }elseif($post['type'] == 2){//设备
                if(empty($post['device_type_id'])){
                    return $this->json(100, '请选择设备类型!');
                }
                $post['device_type_id']  = intval($post['device_type_id']);
                $post['type_id'] = $post['batch_id'] = $post['comment_id'] = 0;
            }elseif($post['type'] == 3){//备注
                if(empty($post['comment_id'])){
                    return $this->json(100, '请选择备注类型!');
                }
                $post['comment_id'] = intval($post['comment_id']);
                $post['type_id'] = $post['batch_id'] = $post['device_type_id'] = 0;
            }

            $res   = $model->create($post);
            if ($res['status'] != 200) {
                return $this->json(100, $res['msg']);
            }
            return $this->json(200, $res['msg']);
        }
        $logHandoverArr = $this->selectLogHandoverArr();
        return $this->render('default-create',[
            'sectionArr' => $this->sectionArr,
            'logHandoverTypeArr'        => $logHandoverArr['logHandoverTypeArr'],
            'logHandoverBatchArr'       => $logHandoverArr['logHandoverBatchArr'],
            'logHandoverDeviceTypeArr'  => $logHandoverArr['logHandoverDeviceTypeArr'],
            'logHandoverCommentTypeArr' => $logHandoverArr['logHandoverCommentTypeArr']
        ]);
    }

    /**
     * 更新默认交接类型种类
     * @return string
     * @throws \yii\base\Exception
     */
    public function actionDefaultUpdate()
    {
        if (Yii::$app->request->isPost) {
            $post  = Yii::$app->request->post();
            $check = LogHandoverDefault::find()->where(['!=', 'id', $post['id']])->andWhere(['type' => $post['type'],'section' => $post['section'],'status'=> LogHandoverDefault::STATUS_OPEN])->one();
      
            if ($check) {
                return $this->json(100, '每个类型只能有一个默认交接内容!');
            }
            if($post['type'] == 1){//物料
                if(empty($post['type_id']) || empty($post['batch_id'])){
                    return $this->json(100, '请选择物料类型或者批次类型!');
                }
                $post['type_id']    = intval($post['type_id']);
                $post['batch_id']   = intval($post['batch_id']);
                $post['comment_id'] = $post['device_type_id'] = 0;
            }elseif($post['type'] == 2){//设备
                if(empty($post['device_type_id'])){
                    return $this->json(100, '请选择设备类型!');
                }
                $post['device_type_id']  = intval($post['device_type_id']);
                $post['type_id'] = $post['batch_id'] = $post['comment_id'] = 0;
            }elseif($post['type'] == 3){//备注
                if(empty($post['comment_id'])){
                    return $this->json(100, '请选择备注类型!');
                }
                $post['comment_id'] = intval($post['comment_id']);
                $post['type_id'] = $post['batch_id'] = $post['device_type_id'] = 0;
            }
            $model = new LogHandoverDefault();
            $res = $model->edit($post);
            if ($res['status'] != 200) {
                return $this->json(100, $res['msg']);
            }
            return $this->json(200, $res['msg']);
        }
        $logHandoverArr = $this->selectLogHandoverArr();
        $id             = Yii::$app->request->get('id');
        $model          = LogHandoverDefault::findOne($id);
        return $this->render('default-update',[
            'id' => $id,
            'model' => $model,
            'sectionArr' => $this->sectionArr,
            'logHandoverTypeArr'        => $logHandoverArr['logHandoverTypeArr'],
            'logHandoverBatchArr'       => $logHandoverArr['logHandoverBatchArr'],
            'logHandoverDeviceTypeArr'  => $logHandoverArr['logHandoverDeviceTypeArr'],
            'logHandoverCommentTypeArr' => $logHandoverArr['logHandoverCommentTypeArr']
        ]);
    }
    

    /**
     * 日志打印列表
     */
    public function actionLoglist()
    {
        $this->layout = false;
        $date    = Yii::$app->request->get('date');
        $classes = Yii::$app->request->get('classes');
        $content = Yii::$app->request->get('content');
        $section = Yii::$app->request->get('section');
        $userStr = $copulateStr = '';
        $query = LogHandover::find();
        $query->where(['is_del' => 0]);
        $query->andFilterWhere(['date' => $date]);
        $query->andFilterWhere(['classes' => $classes]);
        $query->andFilterWhere(['like', 'content', trim($content)]);
        $query->andFilterWhere(['section' => $section]);
        $query->orderBy(['id' => SORT_DESC]);
        $query->asArray();
        $dataProvider = $query->all();
        if($dataProvider){
            $data = $dataProvider[0];
            $classesStr = ArrayHelper::getValue(LogHandover::$classesArr, $data['classes'], '');
            $shiftStr = ArrayHelper::getValue(UserInfo::$shiftArr, $data['shift'], '');
            $userStr  = $classesStr .' - '. $shiftStr .' - '.$data['chinese_name'];
            if(empty($date)){
                $date = $data['date']; 
            }
            $copulateclassesStr = ArrayHelper::getValue(LogHandover::$classesArr, $data['copulate_classes'], '');
            $copulateshiftStr = ArrayHelper::getValue(UserInfo::$shiftArr, $data['copulate_shift'], '');
            $copulateStr = $copulateclassesStr . ' - '. $copulateshiftStr .' - '.$data['copulate_chinese_name'];
        }
        return $this->render('loglist', [
            'date'         => $date,
            'userStr'      => $userStr,
            'copulateStr'  => $copulateStr,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * 列表
     * @return string
     */
    public function actionHandover()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new LogHandoverSearch();
        $data         = $searchModel->search($request);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        $logHandoverArr = $this->selectLogHandoverArr();
        return $this->render('handover', [
            'sectionArr'   => $this->sectionArr,
            'request'      => $request,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination,
            'logHandoverTypeArr'        => $logHandoverArr['logHandoverTypeArr'],
            'logHandoverBatchArr'       => $logHandoverArr['logHandoverBatchArr'],
            'logHandoverDeviceTypeArr'  => $logHandoverArr['logHandoverDeviceTypeArr'],
            'logHandoverCommentTypeArr' => $logHandoverArr['logHandoverCommentTypeArr']
        ]);
    }

    /**
     * 批量删除数据
     */
    public function actionBatchDeletion()
    {
        $request = Yii::$app->request->queryParams;
        $idList  = ArrayHelper::getValue($request, 'idlist', []);
        if (!empty($idList)) {
            LogHandover::updateAll(['is_del' => 1,'del_time'=> date('Y-m-d H:i:s', time())],['id'=> $idList]);
        }else{
            return $this->json(100, '批量删除失败!');
        }
        return $this->json(200, '批量删除成功!');
    }

    /**
     * 日志详情
     */
    public function actionHandoverDetail()
    {
        $id = (int)Yii::$app->request->get('id');
        $query  = LogHandover::find()->where(['id' => $id,'is_del' => 0])->orWhere(['handover_id' => $id]);
        $models = $query->asArray()->all();
        $count  = $query->count();
        $logHandoverArr = $this->selectLogHandoverArr();
        return $this->render('handover-detail', [
            'id'         => $id,
            'count'      => $count,
            'models'     => $models,
            'sectionArr' => $this->sectionArr,
            'logHandoverTypeArr'        => $logHandoverArr['logHandoverTypeArr'],
            'logHandoverBatchArr'       => $logHandoverArr['logHandoverBatchArr'],
            'logHandoverDeviceTypeArr'  => $logHandoverArr['logHandoverDeviceTypeArr'],
            'logHandoverCommentTypeArr' => $logHandoverArr['logHandoverCommentTypeArr']
        ]);
      
    }

    /**
     * 更新日志
     */
    public function actionHandoverUpdate()
    {
        $id = (int)Yii::$app->request->get('id');
        $query  = LogHandover::find()->where(['id' => $id,'is_del' => 0])->orWhere(['handover_id' => $id]);
        $models = $query->asArray()->all();
        $count  = $query->count();
        return $this->render('handover-update', compact('models', 'count', 'id'));
    }

    /**
     * 更新日志内容
     * @return string
     */
    public function actionHandoverUpdateContent()
    {
        if (Yii::$app->request->isPost) {
            $post  = Yii::$app->request->post();
            $model                 = LogHandover::findOne($post['id']);
            $model->content        = trim($post['content']);
            $model->update_time    = date('Y-m-d H:i:s', time());
            if (!$model->save(false)) {
                return $this->json(100, '更新失败!');
            }
            return $this->json(200, '更新成功!');
        }
        $id    = Yii::$app->request->get('id');
        $model = LogHandover::find()->where(['id' => $id])->asArray()->one();
        return $this->render('handover-update-content',['model' => $model]); 
    }

    /**
     * 删除日志
     */
    public function actionHandoverDel()
    {
        $id = (int)Yii::$app->request->get('id');
        $logHandover = LogHandover::findOne(['id' => $id]);
        $logHandover->is_del    = 1;
        $logHandover->del_time  = date('Y-m-d H:i:s', time());
        if (!$logHandover->save(false)) {
            return $this->json(100, '删除失败!');
        }
        LogHandover::updateAll(['is_del' => 1,'del_time'=> date('Y-m-d H:i:s', time())],['handover_id'=> $id]);
        return $this->json(200, '删除成功!');
    }

    /**
     * 日志打印列表
     */
    public function actionLoglistAudit()
    {
    
        $this->layout = false;
        $date    = Yii::$app->request->get('date');
        $classes = Yii::$app->request->get('classes');
        $content = Yii::$app->request->get('content');
        $section = Yii::$app->request->get('section');
        $batchIdArr = LogHandoverBatch::find()->select('id')->where(['is_audit' => 1])->column();
        $userStr = $copulateStr = '';
        $query = LogHandover::find();
        $query->where(['is_del_audit' => 0]);
        $query->andWhere(['is_audit' => 1]);
        $query->andWhere(['not in', 'batch_id', $batchIdArr]);
        $query->andFilterWhere(['like', 'content_audit', trim($content)]);
        $query->andFilterWhere(['date' => $date]);
        $query->andFilterWhere(['classes' => $classes]);
        $query->andFilterWhere(['section' => $section]);
        $query->orderBy(['id' => SORT_DESC]);
        $query->asArray();
        $dataProvider = $query->all();
        if($dataProvider){
            $data = $dataProvider[0];
            $classesStr = ArrayHelper::getValue(LogHandover::$classesArr, $data['classes'], '');
            $shiftStr = ArrayHelper::getValue(UserInfo::$shiftArr, $data['shift'], '');
            $userStr  = $classesStr .' - '. $shiftStr .' - '.$data['chinese_name'];
            if(empty($date)){
                $date = $data['date']; 
            }
            $copulateclassesStr = ArrayHelper::getValue(LogHandover::$classesArr, $data['copulate_classes'], '');
            $copulateshiftStr = ArrayHelper::getValue(UserInfo::$shiftArr, $data['copulate_shift'], '');
            $copulateStr = $copulateclassesStr . ' - '. $copulateshiftStr .' - '.$data['copulate_chinese_name'];
        }
        return $this->render('loglist-audit', [
            'date'         => $date,
            'userStr'      => $userStr,
            'copulateStr'  => $copulateStr,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * 列表
     * @return string
     */
    public function actionHandoverAudit()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new LogHandoverAuditSearch();
        $data         = $searchModel->search($request);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        $uid                  = $this->getUid();
        $userInfo             = UserInfo::findOne(['id' => $uid]);
        $logHandoverArr = $this->selectLogHandoverArr();
        return $this->render('handover-audit', [
            'sectionArr'   => $this->sectionArr,
            'request'      => $request,
            'userInfo'     => $userInfo,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination,
            'logHandoverTypeArr'        => $logHandoverArr['logHandoverTypeArr'],
            'logHandoverBatchArr'       => $logHandoverArr['logHandoverBatchArr'],
            'logHandoverDeviceTypeArr'  => $logHandoverArr['logHandoverDeviceTypeArr'],
            'logHandoverCommentTypeArr' => $logHandoverArr['logHandoverCommentTypeArr']
        ]);
    }

    /**
     * 日志详情
     */
    public function actionHandoverAuditDetail()
    {
        $id = (int)Yii::$app->request->get('id');
        $query  = LogHandover::find()->where(['id' => $id,'is_del_audit' => 0,'is_audit' => 1])->orWhere(['handover_id' => $id]);
        $models = $query->asArray()->all();
        $count  = $query->count();
        $logHandoverArr = $this->selectLogHandoverArr();
        return $this->render('handover-audit-detail', [
            'id'         => $id,
            'count'      => $count,
            'models'     => $models,
            'sectionArr' => $this->sectionArr,
            'logHandoverTypeArr'        => $logHandoverArr['logHandoverTypeArr'],
            'logHandoverBatchArr'       => $logHandoverArr['logHandoverBatchArr'],
            'logHandoverDeviceTypeArr'  => $logHandoverArr['logHandoverDeviceTypeArr'],
            'logHandoverCommentTypeArr' => $logHandoverArr['logHandoverCommentTypeArr']
        ]);
    }

    /**
     * 更新日志
     */
    public function actionHandoverAuditUpdate()
    {
        $id     = (int)Yii::$app->request->get('id');
        $query  = LogHandover::find()->where(['id' => $id,'is_del_audit' => 0,'is_audit' => 1])->orWhere(['handover_id' => $id]);
        $models = $query->asArray()->all();
        $count  = $query->count();
        return $this->render('handover-audit-update', compact('models', 'count', 'id'));
    }


    /**
     * 更新日志内容
     * @return string
     */
    public function actionHandoverAuditUpdateContent()
    {
        if (Yii::$app->request->isPost) {
            $post  = Yii::$app->request->post();
            $logHandover                 = LogHandover::findOne($post['id']);
            $logHandover->content_audit  = trim($post['content_audit']);
            $logHandover->update_time    = date('Y-m-d H:i:s', time());
            if (!$logHandover->save(false)) {
                return $this->json(100, '更新失败!');
            }
            return $this->json(200, '更新成功!');
        }
        $id    = Yii::$app->request->get('id');
        $model = LogHandover::find()->where(['id' => $id])->asArray()->one();
        return $this->render('handover-audit-update-content',['model' => $model]); 
    }


    /**
     * 删除日志
     */
    public function actionHandoverAuditDel()
    {
        $id = (int)Yii::$app->request->get('id');
        $logHandover = LogHandover::findOne(['id' => $id]);
        $logHandover->is_del_audit    = 1;
        $logHandover->del_time_audit  = date('Y-m-d H:i:s', time());
        if (!$logHandover->save(false)) {
            return $this->json(100, '删除失败!');
        }
        LogHandover::updateAll(['is_del_audit' => 1,'del_time_audit'=> date('Y-m-d H:i:s', time())],['handover_id'=> $id]);
        return $this->json(200, '删除成功!');
    }

    /**
     * 可操作人员
     * @return string
     */
    public function actionUser()
    {
        $searchModel  = new LogHandoverUserSearch();
        $data         = $searchModel->search();
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        return $this->render('user', [
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination
        ]);
    }


    /**
     * 添加可操作人员
     * @return string
     */
    public function actionUserCreate()
    {
        if (Yii::$app->request->isPost) {
            $post  = Yii::$app->request->post();
            $userInfo = UserInfo::findOne(['pid'=> $post['pid']]);
            if (!$userInfo) {
                return $this->json(100, '无此PID信息!');
            }
            $model = new LogHandoverUser();
            $check = LogHandoverUser::findOne(['pid'=> $post['pid']]);
            if ($check) {
                return $this->json(100, '人员已存在!');
            }
            $post['user_id'] = $userInfo->id;
            $post['chinese_name'] = $userInfo->chinese_name;
            $post['pid']     = $userInfo->pid;
            $post['shift']   = $userInfo->shift;
            $post['section'] = $userInfo->section;
            $post['status']  = $post['status'];
            $res   = $model->create($post);
            if ($res['status'] != 200) {
                return $this->json(100, $res['msg']);
            }
            return $this->json(200, $res['msg']);
        }
        return $this->render('user-create');
    }

    /**
     * 更新可操作人员
     * @return string
     * @throws \yii\base\Exception
     */
    public function actionUserUpdate()
    {
        if (Yii::$app->request->isPost) {
            $post  = Yii::$app->request->post();
            $model = new LogHandoverUser();
            $check = $model->check($post);
            if (!$check) {
                return $this->json(100, '人员已存在!');
            }
            $res = $model->edit($post);
            if ($res['status'] != 200) {
                return $this->json(100, $res['msg']);
            }
            return $this->json(200, $res['msg']);
        }
        $id            = Yii::$app->request->get('id');
        $model         = LogHandoverUser::findOne($id);
        return $this->render('user-update',['id' => $id,'model' => $model]);
    }

    

}

