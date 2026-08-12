<?php
interface DatabaseInterface {
    public function connect(); public function query($sql);
    public function prepare($sql); public function getConnection(); public function close();
}
abstract class AbstractDatabase {
    protected $host,$username,$password,$database,$connection;
    abstract protected function handleError($e);
    abstract public function executeQuery($sql);
}
class Database extends AbstractDatabase implements DatabaseInterface {
    public function __construct($h='localhost',$u='root',$p='',$d='medixon_db'){
        $this->host=$h;$this->username=$u;$this->password=$p;$this->database=$d;$this->connect();
    }
    public function connect(){
        try{
            $this->connection=new mysqli($this->host,$this->username,$this->password,$this->database);
            if($this->connection->connect_error) throw new Exception($this->connection->connect_error);
            $this->connection->set_charset("utf8mb4");
        }catch(Exception $e){$this->handleError($e->getMessage());die();}
    }
    public function query($sql){return $this->connection->query($sql);}
    public function prepare($sql){return $this->connection->prepare($sql);}
    public function getConnection(){return $this->connection;}
    public function close(){if($this->connection)$this->connection->close();}
    public function getLastInsertId(){return $this->connection->insert_id;}
    public function escape($v){return $this->connection->real_escape_string((string)$v);}
    protected function handleError($e){error_log("MediXon DB: $e");echo json_encode(['error'=>'DB error']);die();}
    public function executeQuery($sql){$r=$this->query($sql);if(!$r){$this->handleError($this->connection->error);return false;}return $r;}
}
$db=new Database();
function isLoggedIn(){return isset($_SESSION['user_id']);}
function isAdmin(){return isset($_SESSION['role'])&&$_SESSION['role']==='admin';}
function getCurrentUserId(){return $_SESSION['user_id']??null;}
function redirect($url){header("Location: $url");exit();}
