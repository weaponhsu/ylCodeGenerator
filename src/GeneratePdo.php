<?php


namespace ylCodeGenerator;


use Exception;
use PDOException;

class GeneratePdo
{
    static public $instance = null;
    public $dsn = null;
    public $db_name = '';
    public $username = '';
    public $password = '';

    static public function getInstance() {
        if (is_null(self::$instance))
            self::$instance = new self();

        return self::$instance;
    }

    private function __construct()
    {
        $this->dsn = 'mysql:dbname=' . Config::DB_NAME . ';host=127.0.0.1;charset=utf8';
        $this->username = Config::DB_USER;
        $this->password = Config::DB_PASSWORD;
        $this->db_name = Config::DB_NAME;
    }

    /**
     * 实例化数据库链接 传入表名 读取完整表结构
     * @param string $table_name
     * @return array
     * @throws Exception
     */
    public function _getTableConstruct($table_name = ''){
        $user = Config::DB_NAME;
        $password = Config::DB_PASSWORD;

        if(strpos($table_name, 'model') !== false){
            $table_name = substr($table_name, 0, strpos($table_name, 'model'));
        }else if(strpos($table_name, 'service') !== false){
            $table_name = substr($table_name, 0, strpos($table_name, 'service'));
        }

        try{
            $dbh = new \PDO($this->dsn, $this->username, $this->password);

            $q = $dbh->query("SHOW FULL COLUMNS FROM `" . $table_name . "`");

            if(!$q){
                throw new Exception('表不存在' . json_encode($dbh->errorInfo()));
            }

            $q->execute();
            $table_fields = $q->fetchAll();
        }catch (PDOException $e){
            exit('Connection failed: ' . $e->getMessage());
        }

        return $table_fields;
    }

    /**
     * @param string $sql
     * @return array|bool
     * @throws Exception
     */
    public function getResult($sql = '') {
        if(empty($sql)){
            return false;
        }

        try{
            $dbh = new \PDO($this->dsn, $this->username, $this->password);

            $q = $dbh->query($sql);
            if(!$q){
                throw new Exception('SQL有误' . json_encode(['info' => $dbh->errorInfo(), 'sql' => $sql]));
            }
            $q->execute();
            $result = $q->fetchAll();

        }catch (PDOException $e){
            exit('Connection failed1: ' . $e->getMessage());
        }

        return $result;
    }

    public function getTableComment($table_name = ''){

        try{
            $dbh = new \PDO($this->dsn, $this->username, $this->password);

            $q = $dbh->query("SHOW CREATE TABLE `" . $table_name . "`");
            if(!$q){
                echo $table_name . '表不存在' . json_encode($dbh->errorInfo());
                exit;
            }
            $q->execute();
            $table_fields = $q->fetchAll();
        }catch (PDOException $e){
            exit('Connection failed2: ' . $e->getMessage());
        }

        return $table_fields;
    }

    public function getTablesInfo(){
        try{
            $dbh = new \PDO($this->dsn, $this->username, $this->password);

            $q = $dbh->query("select `column` from `foreign_relationship` where `table` = '" . $table_name . "'");
            $sql = 'SELECT * FROM tables WHERE TABLE_SCHEMA = "' . $this->db_name . '"';
            $q = $dbh->query($sql);
            if(!$q){
                throw new Exception('888: ' . $dbh->errorInfo());
            }
            $q->execute();
            $table_fields = $q->fetchAll();
        }catch (PDOException $e){
            exit('Connection failed3: ' . $e->getMessage());
        }

        return $table_fields;
    }

    public function getForeignRelationship($table_name = ''){
        try{
            $dbh = new \PDO($this->dsn, $this->user, $this->password);

//            $q = $dbh->query("select `column` from `foreign_relationship` where `table` = '" . $table_name . "'");
            $q = $dbh->query("select * from `foreign_relationship` where `table` = '" . $table_name . "'");
            if(!$q){
                throw new Exception('foreign_relationship表不存在' . $dbh->errorInfo());
            }
            $q->execute();
            $table_fields = $q->fetchAll();
        }catch (PDOException $e){
            exit('Connection failed3: ' . $e->getMessage());
        }

        return $table_fields;
    }
}
