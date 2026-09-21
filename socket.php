<?php
header("Content-Type:text/html;charset=utf-8");

class Socket{

    private static $run_time = 0;

    private $ip = "127.0.0.1";
    private $port = 30000;
    private $socket;
    private $sleep_time = 1;//多长时间执行一次


    public function __construct(){
        echo "初始化socket通信客户端\n";
        error_reporting(E_ALL);
        set_time_limit(0);
    }

    public function connect(){
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $result = socket_connect($this->socket, $this->ip, $this->port);
        if ($result < 0) {

            echo "socket链接失败.返回信息: ($result) " . socket_strerror($result) . "\n";

        }else {
            echo "socket连接成功\n";
        }
    }

    public function getMsg(){
        echo "接受信息\n";
        while($out = socket_read($this->socket, 8192)) {
            echo "接收服务器回传信息成功！\n";

            echo "接受的内容为:",$out."\n";

            Log::error( "接受的内容为:".json_encode($out));
        }
    }

    public function send(){

        self::$run_time = time();
        while(true){
            if (self::$run_time == time()){

                $sql = "select * from nxp_socket_infos where status = 1 order by id asc limit 1";
                $data = Mysql::query()->select($sql);
                if (!empty($data)){
                    $msg = $data['value'];

                    if(!socket_write($this->socket, $msg, strlen($msg))) {

                        Log::error('信息发送失败！错误信息'.socket_strerror($this->socket).' id'.$data['id']);

                    } else {
                        Mysql::query()->update($data['id'],2);
                        echo "发送到服务器信息成功！\n";

                        Log::error('信息发送成功！发送的消息'."$msg");

                    }
//                    echo '发送的消息：'. $msg. "\n";
                }

                $sql = "SELECT `shelf_number`, `status`, car_number FROM `nxp_shelf` ";

                $shelf = Mysql::query()->select($sql,1);
                if (!empty($shelf)){
                    $msg = json_encode($shelf);

                    if(!socket_write($this->socket, $msg, strlen($msg))) {

                        Log::error('同步货架信息失败！错误信息'.socket_strerror($this->socket).$msg);

                    } else {
                        Mysql::query()->update($data['id'],2);
                        echo "货架信息成功发送到服务器！\n";

                        Log::error('信息发送成功！发送的消息'."$msg");

                    }
//                    echo '发送的消息：'. $msg. "\n";
                }

                self::$run_time += $this->sleep_time;
            }
        }
    }

    public function close(){
        socket_close($this->socket);
        echo '关闭'. "\n";
    }
}


class Mysql{
    private $pdo;
    private $tableName = 'nxp_socket_infos';

    public static function __callStatic($method,$args)
    {
        $msyql = new self();
        try{
            $msyql->pdo = new PDO('mysql:host=49.4.11.39;dbname=nxp_server_data','root', 'Zhangxin@123');
        }catch (\Exception $e){
            echo("数据库连接失败: " . $e->getMessage() );
            Log::error('数据库连接失败！错误消息');
        }
        return $msyql;
    }

    public function select($sql,$all = false){

        try{
            $stmt =  $this->pdo->prepare($sql);
            $stmt->execute();
            if (!$all){
                $rows = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            $this->pdo = null;
            $stmt =null;
            return $rows;
        }catch (\Exception $e){
            echo("查询失败: " . $e->getMessage() );
            Log::error('查询失败:'.  $e->getMessage());
        }
    }

    public function update($id,$status){
        $sql = "update $this->tableName set status = ? , update_time = ? where id = ?";
        $stmt =  $this->pdo->prepare($sql);

        $stmt->bindValue(1,$status);
        $stmt->bindValue(2,date('Y-m-d H:i:s'));
        $stmt->bindValue(3,$id);

        $stmt->execute();
        $affect_row = $stmt->rowCount();
        if($affect_row)
        {
          Log::error('信息发送成功！数据更新成功 ID'.$id." status". $status );
        }
        else
        {
            Log::error('信息发送成功！数据更新失败 ID'.$id." status". $status );
        }
        $this->pdo = null;
        $stmt =null;
    }
}

class Log{

    public static function error($msg){
        file_put_contents('./socket.log', date('Y-m-d H:i:s').'====='.$msg."\n",FILE_APPEND);
    }

}


ini_set('date.timezone', 'Asia/Shanghai');
$obj = new Socket();
$obj->connect();
switch ($argv[1]){
    case 'get':
        $obj->getMsg();
        break;
    case 'send':
        $obj->send();
    default:
        $obj->close();
        break;
}



