<?php
  namespace backend\controllers;
  use backend\models\NewGembaKeys;
  use backend\models\GembaKeywords;
  use backend\models\GembaBlockedwords;
  use backend\models\NewGemba;
  use Yii;
  use yii\data\Pagination;
  use yii\db\Expression;
  use yii\db\Exception;

  class NewGembaParticipleController extends BaseController
  {
    /**
     * 关键词
     * @return string
     */
    public function actionKeywords()
    {
      $query = GembaKeywords::find();
      $pagination = new Pagination([
        'totalCount' => $query->count(),
        'defaultPageSize' => 20,
      ]);
      $models = $query
        ->offset($pagination->offset)
        ->limit($pagination->limit)
        ->all();
      return $this->render('keywords', compact('models', 'pagination'));
    }

    /**
     * 添加关键词
     * @return string
     */
    public function actionKeywordsCreate()
    {
      if (Yii::$app->request->isPost) {
        $post = Yii::$app->request->post();
        $menu = new GembaKeywords();
        $res = $menu->create($post);
        if ($res['status'] != 200) {
          return $this->json(100, $res['msg']);
        }
        $changeuserDict = $this->changeuserDict();
        if (!$changeuserDict['status']) {
          return $this->json(100, $changeuserDict['msg']);
        }
        return $this->json(200, $res['msg']);
      }
      return $this->render('keywords-create');
    }

    /**
     * 修改关键词
     * @return string
     */
    public function actionKeywordsUpdate()
    {
      $id = Yii::$app->request->get('id');
      $data = GembaKeywords::findOne($id);
      if (Yii::$app->request->isPost) {
        $post = Yii::$app->request->post();
        $data = new GembaKeywords();
        $res = $data->edit($post);
        if ($res['status'] != 200) {
          return $this->json(100, $res['msg']);
        }
        $changeuserDict = $this->changeuserDict();
        if (!$changeuserDict['status']) {
          return $this->json(100, $changeuserDict['msg']);
        }
        return $this->json(200, $res['msg']);
      }
      return $this->render('keywords-update', compact('data'));
    }

        /**
     * 修改关键词文件
     * @return array
     */
    public function changeuserDict(){

      $status = ['status' => false, 'msg' => ''];

      // 项目路径目录，判断是否存在，不存在则创建
      $fileDir = __DIR__ . "/../../vendor/fukuball/jieba-php/src/dict/";

      if(!is_dir($fileDir)){
        mkdir(iconv("UTF-8", "GBK", $fileDir),0777,true);
      }

      // 文件，判断是否存在，不存在则创建
      $txtFileName = __DIR__ . "/../../vendor/fukuball/jieba-php/src/dict/user_dict.txt";

      // 以读写方式打写指定文件，如果文件不存则创建
      if(file_exists($txtFileName)) {
        // 存在，删掉原有文件
        if(!unlink($txtFileName)){
          $status['msg'] = '删除原有文件失败!';
          return $status;
        }
        if (($txtRes = fopen($txtFileName, "w+")) === FALSE) {
          $status['msg'] = '创建文件失败!';
          return $status;
        }
      } else {
        // 不存在，创建并写入
        if (($txtRes = fopen($txtFileName, "w+")) === FALSE) {
          $status['msg'] = '创建文件失败!!!';
          return $status;
        }
      }
      $blockedWords     = GembaKeywords::find()->select('name')->where(['status' => 1])->column();//关键词数组
      if (empty($blockedWords)) {
        fclose ($txtRes); // 关闭指针
        $status['msg'] = '关键词数组为空!';
        return $status;
      }

      foreach ($blockedWords as $name) {
        $txt = $name . ' ' . 5000 . "\n";
        if (!fwrite ($txtRes,$txt)){ // 将信息写入文件
          fclose($txtRes);
          $status['msg'] = '文件写入数据失败!';
          return $status;
        }
      }
      $status['status'] = true;
      $status['msg'] = '文件写入成功!';
      return $status;
    }

    /**
     * 屏蔽词
     * @return string
     */
    public function actionBlockedwords()
    {
      $userID = $this->getUid();
      $query = GembaBlockedwords::find()->where(['user_id'=>$userID]);
      $pagination = new Pagination([
        'totalCount' => $query->count(),
        'defaultPageSize' => 20,
      ]);
      $models = $query
        ->offset($pagination->offset)
        ->limit($pagination->limit)
        ->all();
      return $this->render('blockedwords', compact('models', 'pagination'));
    }

    public function actionBlockedwordsCreate()
    {
      if (Yii::$app->request->isPost) {
        $post = Yii::$app->request->post();
        $post['user_id'] = $this->getUid();
        $menu = new GembaBlockedwords();
        $res = $menu->create($post);
        if ($res['status'] != 200) {
          return $this->json(100, $res['msg']);
        }
        return $this->json(200, $res['msg']);
      }
      return $this->render('blockedwords-create');
    }

    public function actionBlockedwordsUpdate()
    {
      $id = Yii::$app->request->get('id');
      $data = GembaBlockedwords::findOne($id);
      if (Yii::$app->request->isPost) {
        $post = Yii::$app->request->post();
        $data = new GembaBlockedwords();
        $res = $data->edit($post);
        if ($res['status'] != 200) {
          return $this->json(100, $res['msg']);
        }
        return $this->json(200, $res['msg']);
      }
      return $this->render('blockedwords-update', compact('data'));
    }

    /**
     * 导出数据
     * @return string
     */
    public function actionExportExcelKeywords()
    {
      $params = Yii::$app->request->queryParams;
      $header   = ['关键词', '词频'];
      $list[] = implode(',', $header);

      if(isset($params['params']['NewGembaSearch']['keywords']) && !empty($params['params']['NewGembaSearch']['keywords'])){
       
        //$gembaIdArr =  NewGembaKeys::find()->select('gemba_id')->where(['keys' => trim($params['params']['NewGembaSearch']['keywords'])])->distinct()->column();
        $newquery =  NewGembaKeys::find();
        $newquery->select('gemba_id');
        $newquery->where(['keys' => trim($params['params']['NewGembaSearch']['keywords'])]);
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
            foreach($temp as $keys => $num){
              if(in_array($keys,$blockWordsArr)) continue;
              $arr    = [$keys, $num];
              $list[] = implode(',', $arr);
            }  
          }
        }
      }else{
       
        $newquery =  NewGembaKeys::find();
        $newquery->select('gemba_id');
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
        if(!empty($params['params']['NewGembaSearch']['area'])){
          $area = $params['params']['NewGembaSearch']['area'];
          $newquery->andFilterWhere([
            'area'=>$area
          ]);
        }
        if(empty($params['params']['NewGembaSearch']['start_date']) && empty($params['params']['NewGembaSearch']['end_date'])){
          $start_date = strtotime("-30 days");
          $newquery->andFilterWhere([
            '>','created_at',$start_date
          ]);
        }
        if(!empty($params['params']['NewGembaSearch']['shift'])){
          $shift = $params['params']['NewGembaSearch']['shift'];
          $newquery->andFilterWhere([
            'shift'=>$shift
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
            foreach($temp as $keys => $num){
              if(in_array($keys,$blockWordsArr)) continue;
              $arr    = [$keys, $num];
              $list[] = implode(',', $arr);
            }  
          }
           
        }
      }
      $fileName = '关键词列表_' . time() . '.csv';
      header("Content-type:text/csv;");
      header("Content-Disposition:attachment;filename=" . $fileName);
      header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
      header('Expires:0');
      header('Pragma:public');
      $csvData = mb_convert_encoding($list, "cp936", "UTF-8");
      echo implode("\r\n", $csvData);exit;
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
     * 关键词
     * @return array
     */
    public function getKeywords ()
    {
      $keyWordsData = [];
      $keyWords     = GembaKeywords::find()->select('name')->where(['status' => 1])->column();//关键词数组
      if (empty($keyWords)) {
        return $keyWordsData;
      }
      foreach ($keyWords as $name) {
        $keyWordsData[$name] = 0;
      }
      $titleArr = NewGemba::find()->select('title')->column();
      if (empty($titleArr)) {
        return $keyWordsData;
      }
      $pattern = "/" . implode("|", $keyWords) . "/i"; // 定义正则表达式
      foreach ($titleArr as $title) {
        if (preg_match_all($pattern, $title, $matches)) { // 匹配到了结果
          $patternList = $matches[0];                     // 匹配到的数组
          if (count($patternList) >= 1) {
            foreach ($patternList as $keyTitle) {
              if (isset($keyWordsData[$keyTitle])) {
                $keyWordsData[$keyTitle] += 1;
              }
            }
          }
        }
      }
      return $keyWordsData;
    }

    /**
     * 屏蔽词数量
     * @return array
     */
    function getBlockedWords()
    {
      $blockedWordsData = [];
      $blockedWords     = GembaBlockedwords::find()->select('name')->where(['status' => 1])->column();//关键词数组
      if (empty($blockedWords)) {
        return $blockedWordsData;
      }
      foreach ($blockedWords as $name) {
        $blockedWordsData[$name] = 0;
      }
      $titleArr = NewGemba::find()->select('title')->column();
      if (empty($titleArr)) {
        return $blockedWordsData;
      }
      $pattern = "/" . implode("|", $blockedWords) . "/i"; // 定义正则表达式
      foreach ($titleArr as $title) {
        if (preg_match_all($pattern, $title, $matches)) { // 匹配到了结果
          $patternList = $matches[0];                     // 匹配到的数组
          if (count($patternList) >= 1) {
            foreach ($patternList as $keyTitle) {
              if (isset($blockedWordsData[$keyTitle])) {
                $blockedWordsData[$keyTitle] += 1;
              }
            }
          }
        }
      }
      return $blockedWordsData;
    }

    /**
     * 屏蔽词过滤
     * @param $str
     * @return string
     */
    function testAction($str = '')
    {
      $list     = GembaBlockedwords::find()->select('name')->where(['status' => 1])->column();//关键词数组
      return $this->sensitive($list,$str);
    }

    /**
     * 屏蔽词替换字符串
     * @param $list
     * @param $string
     * @return string
     */
    function sensitive ($list, $string)
    {
      $stringAfter = $string;                                   // 替换后的内容
      $pattern     = "/" . implode("|", $list) . "/i"; // 定义正则表达式
      if (preg_match_all($pattern, $string, $matches)) {     // 匹配到了结果
        $patternList  = $matches [0];                           // 匹配到的数组
        $replaceArray = array_combine($patternList, array_fill(0, count($patternList), '')); // 把匹配到的数组进行合并，替换使用
        $stringAfter  = strtr($string, $replaceArray);          // 结果替换
      }
      return $stringAfter;
    }


  }
