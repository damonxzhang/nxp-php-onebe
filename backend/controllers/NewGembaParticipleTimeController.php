<?php
  namespace backend\controllers;
  use backend\models\GembaBlockedwords;
  use backend\models\GembaKeywords;
  use backend\models\NewGemba;
  use yii\web\Controller;

  class NewGembaParticipleTimeController extends Controller
  {

     //  http://onebe.test/new-gemba-participle-time/index

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

