<?php
require_once 'DB_info.php';

abstract class DB_object {
    protected const PRIVATE_CONNECTION  = true;
    protected const FOREIGN_CONNECTION  = false;
    protected const THIS_OBJCT          = 0;
    protected mysqli $connection;
    protected bool $connection_type;
    protected bool $connection_status;
    // create an object and start a session to mysql sever
    // this constructor may used as a parent constructor for the extends child objects
    // you can modify the reaslt when error on connection from inside this constructor
    // the default behavior to this error by throw an exception
    public function __construct(?mysqli $conn) {
        // if connection not found create a new one and set conn type private
        if($conn == null) {
            $this->connection = new mysqli($GLOBALS['hnm'],$GLOBALS['unm'],$GLOBALS['pwd'],$GLOBALS['dbs']);
            $this->connection_type = self::PRIVATE_CONNECTION;
            $this->connection_status = true;
        }
        // if connection found then use this connection to database and set conn type foreign
        else {
            $this->connection = $conn;
            $this->connection_type = self::FOREIGN_CONNECTION;
            $this->connection_status = true;
        }
        if($this->connection->connect_error) {
            // echo $this->connection->connect_error;
            $this->connection_status = false;
            throw new Exception($this->connection->connect_error);
            // header("Location: http://");
            exit;
        }
    }
    // return a connection to mysql server to path it to anther object
    public function get_connection():mysqli {
        return $this->connection;
    }
    // close the connection to mysql server
    public function close_connection() {
        if($this->connection_type == self::PRIVATE_CONNECTION && $this->connection_status === true) {
            $this->connection->close();
            $this->connection_status = false;
        }
    }
    // end this object and close any private connection to mysql server
    public function __destruct() {
        $this->close_connection();
    }
    // this used to start transaction in child classes
    // you can modify the reaslt when error on query from inside this function
    // the default behavior to this error by throw an exception
    protected function prepare_start():bool {
        if(!$this->connection->query("START TRANSACTION")) 
            throw new Exception("database is buzy");
        return true;
    }
    // this used to end transaction in child classes
    // you can modify the reaslt when error on query from inside this function
    // the default behavior to this error by throw an exception
    protected function prepare_end():bool {
        if(!$this->connection->query("COMMIT"))
            throw new Exception("database is buzy");
        return true;
    }
    // this used to test sql result in child classes
    // you can modify the reaslt when error on query from inside this function
    // the default behavior to this error by throw an exception
    protected function is_query_resault(mysqli_result|false $res) {
        if(!$res)
            throw new Exception($this->connection->error);
        return true;
    }
    // package sanitizing and prepare
    protected function update_routien(string $query, string|int $data, string|int|null $data2=null) {
        // sanitize 
        $val = sanitize($this->connection , $data);
        if(!is_null($data2))
            $val2 = sanitize($this->connection , $data2);
        // prepare
        $this->prepare_start(); 
        $stmt = $this->connection->prepare($query);
        if($val !== 'null' && !isset($val2))
            $stmt->bind_param('s', $val);
        if($val !== 'null' && isset($val2))
            $stmt->bind_param('ss', $val, $val2);
        $val = $stmt->execute();
        $stmt->close();
        $this->prepare_end();
        return $val;
    }
    abstract public function add(int $type, string|int ...$data):bool;
    abstract public function update(int $type,string|int ...$data):bool;
    abstract public function delete(int $type, int ...$data):bool;
    abstract public function show(int $type=self::THIS_OBJCT,int $order=self::THIS_OBJCT, int $start=0, int $limit=0, string $match=''):array|false|null;
}