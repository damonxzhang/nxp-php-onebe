<?php
namespace common\models;
header('Content-type:text/html; Charset=utf-8');
date_default_timezone_set('PRC');

class Haikang
{
    public    $pre_url    = "https://192.168.1.10:443";
    protected $app_key    = "24008499";
    protected $app_secret = "5QfTKaImE2cRPLWbyvOp";
    public $time;
    public $charset;
    public $content_type = "application/json";//json类型
    public $accept       = "*/*";
    public $method       = [
        "POST" => "POST"
    ];

    public $list_url = [
        'application/oneToMany' => "/artemis/api/frs/v1/application/oneToMany",
        'face/group'            => "/artemis/api/frs/v1/face/group",
        'api/token'            => "/artemis/api/v1/oauth/token",
    ];

    public         $date  = NULL;
    private static $redis = NULL;
    const CACHE_ACCESS_TOKEN = "access_token";

    public function __construct($app_key = '', $app_secret = '')
    {
        if ($app_key != '') $this->app_key = $app_key;
        if ($app_secret != '') $this->app_secret = $app_secret;
        $this->charset = 'utf-8';
        list($msec, $sec) = explode(' ', microtime());
        $this->time = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
    }

    /**
     * 按人脸查询分组/api/frs/v1/face/group 获得分组的idfaceGroupIndexCodes
     * @return mixed
     */
    public function get_rlcx()
    {
        $postData = [
            "name" => "测试分组"
        ];
        $sign = $this->get_sign($this->list_url['face/group']);
        $options = [
            CURLOPT_HTTPHEADER => [
                "HTTP METHOD:" . $this->method['POST'],
                "Accept:" . $this->accept,
                "Content-Type:" . $this->content_type,
                "Date:" . $this->get_date(),
                "x-Ca-Key:" . $this->app_key,
                "X-Ca-Signature:" . $sign,
                "X-Ca-Signature-Headers: x-ca-key"]
        ];
        $result1 = $this->curlPost($this->pre_url . $this->list_url['face/group'], json_encode($postData), $options);
        return json_decode($result1, TRUE);
    }

    public function getData()
    {

        $sign = $this->get_sign($this->list_url['api/token']);
        $options = [
            CURLOPT_HTTPHEADER => [
                "HTTP METHOD:" . $this->method['POST'],
                "Accept:" . $this->accept,
                "Content-Type:" . $this->content_type,
                "Date:" . $this->get_date(),
                "x-Ca-Key:" . $this->app_key,
                "X-Ca-Signature:" . $sign,
                "X-Ca-Signature-Headers: x-ca-key"]
        ];
        $result1 = $this->curlPost($this->pre_url . $this->list_url['api/token'], '', $options);
       // var_dump($result1);die;
        return json_decode($result1, TRUE);
    }
    /**
     * 接收上传过来的图片
     * @return string
     */
    public function goods_uploadImg()
    {
        $file = request()->file('fileup');
        if ($file) {
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads/images');
            if ($info) {
                $img_url = $info->getSaveName();
                $img_url = str_replace('\\', '/', $img_url);
                $savename = "uploads/images/" . $img_url;
                $data = base64_encode(file_get_contents($savename, TRUE));
                $res = unlink($savename);//清除上传的图片
                return $data;
            } else {
                return json(['errcode' => '-1', 'errmsg' => $file->getError()]);
            }
        }
    }
    //接收微信小程序上传过来的图片end
    ////人脸分组1VN检索/api/frs/v1/application/oneToMany
    public function get_similarity()
    {
        //请求参数 //外委人员库
        $postData = [
            "pageNo" => "1",
            "pageSize" => "20",
            "searchNum" => "99",
            "minSimilarity" => "50",
            "facePicBinaryData" => $this->goods_uploadImg(),
            "faceGroupIndexCodes" => ["xxxxxxxx测试分组的IDxxxxxxxxxx"],
        ];
        $sign = $this->get_sign($postData, $this->list_url['application/oneToMany']);
        $options = [
            CURLOPT_HTTPHEADER => [
                "HTTP METHOD:" . $this->method['POST'],
                "Accept:" . $this->accept,
                "Content-Type:" . $this->content_type,
                "Date:" . $this->get_date(),
                "x-Ca-Key:" . $this->app_key,
                "X-Ca-Signature:" . $sign,
                "X-Ca-Signature-Headers: x-ca-key",
            ]
        ];
        $result1 = $this->curlPost($this->pre_url . $this->list_url['application/oneToMany'], json_encode($postData), $options);
        return $result1;
    }

    /**
     * @return false|string|null
     */
    public function get_date()
    {
        if (!$this->date) $this->date = date("Y-m-d H:i:s");
        return $this->date;
    }

    /**
     * 以appSecret为密钥，使用HmacSHA256算法对签名字符串生成消息摘要，对消息摘要使用BASE64算法生成签名（签名过程中的编码方式全为UTF-8）
     * @param $postData
     * @param $url
     * @return string
     */
    function get_sign($url)
    {
        $sign_str = $this->get_sign_str($url); //签名字符串
        $app_secret = $this->app_secret;
        $sign = hash_hmac('sha256', $sign_str, $app_secret, TRUE); //生成消息摘要
        $result = base64_encode($sign);
        return $result;

    }

    /**
     * @param $postData
     * @param $url
     * @return string
     */
    function get_sign_str($url)
    {
        $next = "\n";
        $str = "POST" . $next . $this->accept . $next . $this->content_type . $next . $this->get_date() . $next;//httpHeaders 
        $str .= "x-ca-key:" . $this->app_key . $next;//customHeaders 
        $str .= $url;
        return $str;
    }

    /**
     * @param $params
     * @return string
     */
    public function getSignContent($params)
    {
        ksort($params);
        $stringToBeSigned = "";
        $i   = 0;
        $len = count($params);
        foreach ($params as $k => $v) {
            if (FALSE === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {
                // 转换成目标字符集
                $v = $this->characet($v, $this->charset);
                if ($i == 0) {
                    $stringToBeSigned .= "?$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }
        unset ($k, $v);
        return $stringToBeSigned;
    }

    /**
     * @param $postData
     * @return string
     */
    function get_message($postData)
    {
        $str = str_replace(['{', '}', '"'], '', json_encode($postData));
        return base64_encode(md5($str));
    }

    /**
     * 校验$value是否非空
     * @param $value
     * @return bool
     */
    protected function checkEmpty($value)
    {
        if (!isset($value)) return TRUE;
        if ($value === NULL) return TRUE;
        if (trim($value) === "") return TRUE;
        return FALSE;
    }

    /**
     * 转换字符集编码
     * @param $data
     * @param $targetCharset
     * @return bool|false|string|string[]|null
     */
    function characet($data, $targetCharset)
    {
        if (!empty($data)) {
            $fileType = $this->charset;
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
            }
        }
        return $data;
    }

    /**
     * @param string $url
     * @param string $postData
     * @param array $options
     * @return bool|string
     */
    public function curlPost($url = '', $postData = '', $options = [])
    {
        if (is_array($postData)) {
            $postData = http_build_query($postData);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); //设置cURL允许执行的最长秒数
        if (!empty($options)) {

            curl_setopt_array($ch, $options);

        }
        //https请求 不验证证书和host
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $data = curl_exec($ch);
        //var_dump(curl_error($ch));
        //var_dump(curl_getinfo($ch));
        curl_close($ch);
        return $data;
    }

}

