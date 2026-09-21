<?php

namespace backend\controllers;

use backend\models\GembaChangeArea;
use backend\models\NewGembaData;
use backend\models\NewGembaSendMsg;
use backend\models\Team;
use backend\models\UserInfo;
use backend\serves\new_gemba\newGembaFactory;

require_once __DIR__ . '/../../vendor/fukuball/jieba-php/src/vendor/multi-array/MultiArray.php';
require_once __DIR__ . '/../../vendor/fukuball/jieba-php/src/vendor/multi-array/Factory/MultiArrayFactory.php';
require_once __DIR__ . '/../../vendor/fukuball/jieba-php/src/class/Jieba.php';
require_once __DIR__ . '/../../vendor/fukuball/jieba-php/src/class/Finalseg.php';
require_once __DIR__ . '/../../vendor/fukuball/jieba-php/src/class/JiebaAnalyse.php';

use Fukuball\Jieba\Jieba;
use Fukuball\Jieba\Finalseg;
use Fukuball\Jieba\JiebaAnalyse;

use Yii;
use backend\models\NewGemba;
use backend\models\search\NewGembaSearch;
use backend\models\search\MappingGembaSearch;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\db\Expression;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use backend\models\NewGembaMapping;
use backend\models\NewGembaKeys;
use backend\models\GembaBlockedwords;
Jieba::init(array('mode'=>'default','dict'=>'small'));
Finalseg::init();
JiebaAnalyse::init();

/**
 * NewGembaController implements the CRUD actions for NewGemba model.
 */
class NewGembaController extends BaseController
{
    /**
     * Lists all NewGemba models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new NewGembaSearch();
        $params = Yii::$app->request->queryParams;
        if(isset($params['NewGembaSearch']['keywords']) && !empty($params['NewGembaSearch']['keywords'])){
          $dataProvider = $searchModel->searchKeys($params);
        }else{
          $dataProvider = $searchModel->search($params);
        }
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all NewGemba models.
     * @return mixed
     */
    public function actionMappingIndex()
    {
        $searchModel = new MappingGembaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('mapping_index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single NewGemba model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionMappingView($id)
    {
       
        $model =  NewGembaMapping::findOne($id);
        $userInfo = UserInfo::findOne($model->create_user);
        return $this->render('mapping_view', [
            'model' => $model,
            'userInfo' =>$userInfo
        ]);
    }

    /**
     * Lists all NewGemba models.
     * @return mixed
     */
    public function actionDraft()
    {
        $searchModel = new NewGembaSearch();
        $dataProvider = $searchModel->searchDraft(Yii::$app->request->queryParams);

        return $this->render('draft', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 与我选相关
     * @return mixed
     */
    public function actionCorrelation ()
    {
        $searchModel = new NewGembaSearch();
        $dataProvider = $searchModel->searchCorrelation(Yii::$app->request->queryParams);

        return $this->render('correlation', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 历史gemba
     * @return string
     */
    public function actionHistory(){

        $searchModel = new NewGembaSearch();
        $dataProvider = $searchModel->searchHistory(Yii::$app->request->queryParams);

        return $this->render('history', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single NewGemba model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $userInfo = UserInfo::findOne($model->create_user);
        return $this->render('view', [
            'model' => $model,
            'userInfo' =>$userInfo
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
     * @param $content
     * @param $gembaId
     * @return bool
     * @throws Exception
     */
    public function insertKeys($content,$gembaId)
    {
        jieba::loadUserDict( __DIR__ . "/../../vendor/fukuball/jieba-php/src/dict/user_dict.txt");//导入自定义的词语

        //这里是定义截断性比重占比分析 如果添加屏蔽词把这个地方打开
        //JiebaAnalyse::setStopWords(__DIR__ . '/../../vendor/fukuball/jieba-php/src/dict/stop_words.txt');

        //越小精确度又高|提取的关键词越准|默认20
        $topK = 10;
        //调用方法分析词语的占比
        $tags = JiebaAnalyse::extractTags($content, $topK);
        $shift = $area = 0;
        $data = NewGemba::find()->where(['id' => $gembaId])->asArray()->one();
        if($data){
            $userData = UserInfo::findOne($data['create_user']);
            $shift = intval($userData->shift);
            $area =  intval($userData->area);
        }
   
        $insertData = [];
        foreach ($tags as $keys => $numb) {
        $insertData[] = [$gembaId, $keys, time()];
        }
       
        //添加数据
        $resultData = $this->insertData(NewGembaKeys::tableName(), ['gemba_id', 'keys', 'created_at'], $insertData);
        if (!$resultData) {
        return false;
        }
        NewGembaKeys::updateAll(['area' => $area,'shift' => $shift], ['gemba_id' => $gembaId]);
        return true;
    }

    public function actionShowdata(){
        $params = Yii::$app->request->queryParams;
        $resultData = [];
        $newquery =  NewGembaKeys::find();
        $newquery->select('gemba_id');
        if(isset($params['params']['NewGembaSearch']['keywords']) && !empty($params['params']['NewGembaSearch']['keywords'])){
            $newquery->where(['keys' => trim($params['params']['NewGembaSearch']['keywords'])]);
        }
        if(!empty($params['params']['NewGembaSearch']['area'])){
          $area = $params['params']['NewGembaSearch']['area'];
          $newquery->andFilterWhere([
            'area'=>$area
          ]);
        }
        if(!empty($params['params']['NewGembaSearch']['shift'])){
          $shift = $params['params']['NewGembaSearch']['shift'];
          $newquery->andFilterWhere([
            'shift'=>$shift
          ]);
        }
        if(!empty($params['params']['NewGembaSearch']['start_date'])){
          $start_date = strtotime($params['params']['NewGembaSearch']['start_date']);
          $newquery->andFilterWhere([
            '>','created_at',$start_date
          ]);
        }
        if(!empty($params['params']['NewGembaSearch']['end_date'])){
          $end_date = strtotime($params['params']['NewGembaSearch']['end_date']);
          $newquery->andFilterWhere([
            '<','created_at',$end_date
          ]);
        }
        if(empty($params['params']['NewGembaSearch']['start_date']) && empty($params['params']['NewGembaSearch']['end_date'])){
            $start_date = strtotime("-30 days");
            $newquery->andFilterWhere([
              '>','created_at',$start_date
            ]);
          }
        $newquery->distinct();
        $gembaIdArr =  $newquery->column();
        if (!empty($gembaIdArr)) {
          $listGembaId = $queryData = [];
          foreach ($gembaIdArr as $gembaId) 
          {
            $listGembaId[] = $gembaId;
            if(count($listGembaId)>=50)
            {
              $query = NewGembaKeys::find();
              $query->select('keys,'.new Expression("count(id) as num"));
              $query->andFilterWhere(['in', 'gemba_id', $listGembaId]);
              $query->groupBy('keys');
              $query->orderBy('num desc');
              $query->asArray();
              $keyWords = $query->all();
              if (!empty($keyWords)) {
                foreach ($keyWords as $value) {
                  $queryData[] = $value;
                }
              }
              $listGembaId = [];
            }
          }

          if (!empty($queryData)) {
            $temp = array();
            foreach($queryData as $value) {
              list($n, $p) = array_values($value);
              $temp[$n] = array_key_exists($n, $temp) ? $temp[$n]+$p : $p;
            }
            $temp = $this -> sort_with_keyName($temp);
            $userId = $this->getUid();
            $blockWordsArr = GembaBlockedwords::find()->select('name')->where(['user_id' => $userId,'status'=>1])->column();
            foreach($temp as $key => $value) {
                if(in_array($key,$blockWordsArr)) continue;
                $resultData[] =['keys'=>$key,'number' => $value];
                if(count($resultData) >= 20) break;
            }
           
          }
        }
     
        return $this->render('showdata', [ 'data' => $resultData]);
    }

    //一维数组排序desc 从大到小倒序排列， asc 递增排序
    private function sort_with_keyName($arr, $orderby = 'desc')
    {
    $new_array = array();
    $new_sort = array();
    foreach ($arr as  $value) {
        $new_array[] = $value;
    }
    if ($orderby == 'asc') {
        asort($new_array);
    } else {
        arsort($new_array);
    }
    foreach ($new_array as  $v) {
        foreach ($arr as $key => $value) {
        if ($v == $value) {
            $new_sort[$key] = $value;
            unset($arr[$key]);
            break;
        }
        }
    }
    return $new_sort;
    }


    /**
     * Creates a new NewGemba model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new NewGembaData();
        $model->steps = $model::CREATE_STEPS;
        if(Yii::$app->request->post()){
            $post = Yii::$app->request->post();
            if(isset($post['NewGembaData']['files_mapping'])){
                $post['NewGembaData']['files_mapping'] = json_encode($post['NewGembaData']['files_mapping']);
            }
       
            if ($model->setScenariosBySteps() && $model->load($post)) {
                $participant = Yii::$app->request->post('participant');
                //格式化参与人
                if(!empty($participant) && is_array($participant)){
                    $model->participant = implode(',',$participant);
                }
                $draft = Yii::$app->request->post('draft');
                //状态为草稿
                if(!empty($draft)){
                    $model->draft = NewGemba::DRAFT_YES;
                }

                if(!empty($model->files)){
                    $model->files = json_encode($model->files);
                }
                $model->year = date('Y');
                $model->createNumber();
                if($model->setAttributesBySteps() && $model->save()){

                    $this -> insertKeys($model->title,$model->id);

                    if($model->steps == NewGemba::COMPLETE_STEPS){
                        $data = NewGemba::find()->where(['id' => $id])->asArray()->one();
                        $query = (new \yii\db\Query())->from('nxp_new_gemba_mapping')->select('id')->where(['title' => $data['title']])->one();
                        if(!$query){
                        unset($data['id']);
                        $userData = UserInfo::findOne($data['create_user']);
                        $data['team'] = $userData->team;
                        $data['shift'] = $userData->shift;
                        $data['section'] = $userData->section;
                        Yii::$app->db->createCommand()->insert('nxp_new_gemba_mapping', $data)->execute();
                        }
                    }
                    return $this->json(200,'保存完成！');
                }else{
                    return $this->json(100,$model->errorToString());
                }
            }
        }
        $userInfo = Yii::$app->user->identity->userInfo;
        $model->area = $userInfo->area;
        return $this->render('create', [
            'model' => $model,
            'userInfo' => $userInfo
        ]);
    }

    /**
     * 主管确认完成
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionLeaderConfirm($id){
        $model = $this->findModel($id);
        if(Yii::$app->request->post()){
            $create_user = UserInfo::find()->where(['id' => $model->create_user])->select('leader_uid')->one();

            if(empty($create_user) || $create_user->leader_uid != Yii::$app->user->identity->user_id){
                return $this->json(100,'没有权限！');
            }
            $model->setScenario(NewGembaData::LEADER_SEC);
            $model->steps   = NewGembaData::AUTO_CONFIRM_STEPS;
            $model->leader_status = Yii::$app->request->post('status');

            if($model->save()){
                return $this->json(200,'操作成功！');
            }else{
                return $this->json(100,$model->errorToString());
            }
        }
    }

    /**
     * 修改审批区域
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionChangeArea($id){
        $model = $this->findModel($id);
        if(Yii::$app->request->post()){
            $post = Yii::$app->request->post();

            if (empty($post['area']) || !in_array($post['area'],array_keys(UserInfo::$areaArr))){
                return $this->json(100,'请选择区域！');
            }

            if(empty($post['content'])){
                return $this->json(100,'内容不能为空！');
            }

            $areaModel = new GembaChangeArea();
            if($areaModel->isExists($id)){
                return $this->json(100,'无法重复修改区域！');
            }

            $tran = Yii::$app->db->beginTransaction();
            try{
                $areaModel->setAttributes([
                    'gemba_id' => $model->id,
                    'original_area' => $model->area,
                    'new_area' => $post['area'],
                    'content' => $post['content'],
                    'create_user' => Yii::$app->user->identity->user_id
                ]);

                //保存记录
                if(!$areaModel->save()){
                    throw new Exception($model->errorToString());
                }
                //更改信息
                $model->area = $post['area'];
                if(!$model->save()){
                    throw new Exception($model->errorToString());
                }

                $tran->commit();
                return $this->json(200,'操作成功！');
            }catch (\Exception $e){

                $tran->rollBack();
                return $this->json(100,$e->getMessage());
            }
        }

        return $this->render('change_area',[
            'model' => $model
        ]);
    }

    public function actionChangeAreaInfo($id){
        $models = GembaChangeArea::find()->where(['gemba_id' => $id]);
        $data = new ActiveDataProvider(['query' => $models,'pagination' => false]);
        return $this->render('change_area_info',[
            'models' => $data
        ]);
    }

    /**
     * 完成用户确认
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionUserConfirm($id){
        $model = $this->findModel($id);
        $model->setScenario(NewGembaData::LEADER_SEC);

        if(Yii::$app->request->post()){

            if($model->create_user != Yii::$app->user->identity->user_id){
                return $this->json(100,'没有权限！');
            }

            if($model->steps != NewGemba::WORK_CONFIRM_STEPS){
                //审核拒绝
                if($model->leader_status == NewGemba::SELECT_YES){
                    $model->status = NewGemba::STATUS_COMPLETE;
                }else{
                    $model->status = NewGemba::STATUS_DIE;
                }

                $model->steps = NewGemba::COMPLETE_STEPS;
            }else{
                //实施取消
                $status = Yii::$app->request->post('status');
                //同意取消
                if($status == NewGemba::SELECT_YES){
                    $model->status = NewGemba::STATUS_DIE;
                    $model->steps = NewGemba::REFUSED_WORK_STEPS;
                //继续执行
                }else{
                   $model->steps = NewGemba::WORK_STEPS;
                }
            }

            if($model->save()){
                if($model->steps == NewGemba::COMPLETE_STEPS){
                    $data = NewGemba::find()->where(['id' => $id])->asArray()->one();
                    $query = (new \yii\db\Query())->from('nxp_new_gemba_mapping')->select('id')->where(['title' => $data['title']])->one();
                    if(!$query){
                    unset($data['id']);
                    $userData = UserInfo::findOne($data['create_user']);
                    $data['team'] = $userData->team;
                    $data['shift'] = $userData->shift;
                    $data['section'] = $userData->section;
                    Yii::$app->db->createCommand()->insert('nxp_new_gemba_mapping', $data)->execute();
                    }
                }
                return $this->json(200,'操作成功！');
            }else{
                return $this->json(100,$model->errorToString());
            }

        }
    }

    /**
     * 判断修改
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionBeforUpdate(){
        $id = Yii::$app->request->post('id');
        $model = $this->findModel($id);
        $steps = $model->getSteps();
        if($steps){
            $redirect_url = Yii::$app->request->post('redirect_url');
            if(!empty($redirect_url)){
                Yii::$app->session->set('redirect_gemba_url',$redirect_url);
            }
            $url = Url::to(['update','id' => $id,'steps' => $steps]);
            return $this->json(200,'请求成功！',['url' => $url]);
        }else{
            return $this->json(100,'没有操作权限！');
        }

    }

    /**
     * Updates an existing NewGemba model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id,$steps)
    {
        $model = $this->findModel($id);
        $model->steps = $steps;
        if (Yii::$app->request->isPost) {
            //var_dump(Yii::$app->request->post());die;
            $post = Yii::$app->request->post();
            if(isset($post['NewGembaData']['files_mapping']) && is_array($post['NewGembaData']['files_mapping'])){
                $post['NewGembaData']['files_mapping'] = json_encode($post['NewGembaData']['files_mapping']);
            }elseif(!isset($post['NewGembaData']['files_mapping'])){
                $post['NewGembaData']['files_mapping'] = '';
            }
          
            if ($model->setScenariosBySteps() && $model->load($post)) {
                $participant = Yii::$app->request->post('participant');

                //格式化参与人
                if(!empty($participant) && is_array($participant)){
                    $model->participant = implode(',',$participant);
                }
                //状态不是草稿
                if(!empty($model->draft)){
                    $model->draft = NewGemba::DRAFT_NO;
                }
                $draft = Yii::$app->request->post('draft');

                //状态为草稿
                if(!empty($draft)){
                    $model->draft = NewGemba::DRAFT_YES;
                }
                if(!empty($model->files) && is_array($model->files)){
                    $model->files = json_encode($model->files);
                }
                if($model->validateUser() && $model->setAttributesBySteps() && $model->save()){

                    $redirect_url = Yii::$app->session->get('redirect_gemba_url');

                    if(!empty($redirect_url)){
                        Yii::$app->session->set('redirect_gemba_url','');

                        return $this->redirect(urldecode($redirect_url));die;
                    }else{
                        if($model->steps == NewGemba::COMPLETE_STEPS){
                            $data = NewGemba::find()->where(['id' => $id])->asArray()->one();
                            $query = (new \yii\db\Query())->from('nxp_new_gemba_mapping')->select('id')->where(['title' => $data['title']])->one();
                            if(!$query){
                            unset($data['id']);
                            $userData = UserInfo::findOne($data['create_user']);
                            $data['team'] = $userData->team;
                            $data['shift'] = $userData->shift;
                            $data['section'] = $userData->section;
                            Yii::$app->db->createCommand()->insert('nxp_new_gemba_mapping', $data)->execute();
                            }
                        }
                        return $this->json(200,'保存成功！');
                    }
                }else{
                    return $this->json(100,$model->errorToString());
                }
            }
        }
         $userInfo = UserInfo::findOne($model->create_user);
       // $userInfo = Yii::$app->user->identity->userInfo;
        $model->setImagesPath();
        //如果表达不存在就跳转到详情页
        if(empty(NewGembaData::$formList[$model->steps])){
           return $this->redirect(['view','id' => $model->id]);
        }
        return $this->render('update', [
            'model' => $model,
            'view' => NewGembaData::$formList[$model->steps],
            'params' => [
                'userInfo' => $userInfo
            ]
        ]);
    }

    /**
     * 导出excel
     * @param null $idlist
     * @param null $type
     */
    public function actionExprotExcel( ){
        $ids= null;
        $get = Yii::$app->request->get();

        if(!empty($get['idlist'])){
            $ids = explode(',',$get['idlist']);
        }
        $query = [];
        if(!empty($get['query'])){
            $query = json_decode($get['query'],1);
        }
        if(!empty($get['type']) && $get['type'] == 2){
            $queryModel = new MappingGembaSearch();
        }else{
            $queryModel = new NewGembaSearch();
        }
        $history = false;
        if(!empty($get['history'])){
            $history = true;
        }

        $data = $queryModel->searchExcelData($query,$ids,$history);
		
        $file_name  = "gemba-".date("Y-m-d H:i:s",time());
        $file_suffix = "xls";
        header("Content-type:application/vnd.ms-excel;charset=utf8");
        header("Content-Disposition:attachment;filename=$file_name.$file_suffix");

        echo $this->render('excel_view',['data' => $data]);
    }

    /**
     * 发送邮件
     * @param NewGemba $model
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function sendEmailByStepe(NewGemba $model){
        $msgModel = Yii::createObject([
            'class' => NewGembaSendMsg::className(),
            'gemModel' => $model
        ]);
        if($msgModel->sendEmail()){
            return true;
        }
        return false;
    }

    /**
     * Finds the NewGemba model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return NewGemba the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = NewGembaData::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
	
	public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
}
