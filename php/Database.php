<?php
class Database {

    private $connection;
    private $statement;

    public function __construct(){
        $this->connection = new mysqli("u461885.mysql.masterhost.ru", "u461885", "cosT7Dgice.se", "u461885");
        $this->connection->set_charset("utf8");
        if ($this->connection->connect_errno) {
            echo "Failed to connect to MySQL!: (" . $this->connection->connect_errno . ") "
                . $this->connection->connect_error;
        }
    }

    public function real_escape_string($string){
        return $this->connection->real_escape_string($string);
    }

    public function query($query){
        $this->connection->set_charset("utf8");
        return $this->connection->query($query);
    }

    public function prepare($query){
        $this->connection->set_charset("utf8");
        $this->statement = $this->connection->prepare($query);
    }

    public function bindAndExecute(){
        $this->connection->set_charset("utf8");
        call_user_func_array(array($this->statement, "bind_param"), func_get_args());
        $this->statement->execute();
    }
}

$database = new Database();
?>