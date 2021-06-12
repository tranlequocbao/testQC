<?php
require_once __DIR__ . "../../../autoload.php";

use Symfony\Component\Yaml\Yaml;

class pdoRequest{
    private $adapter = 'mysql';
    private $host = 'localhost';
    private $port = '3306';
    private $dbname = 'test';
    private $username = 'markandr';
    private $password = '';
    private $table = null;
    private $conn = null;
    private $arrayTableTypeQuery = [];

    private function readYmlConfig(){
        $yaml = Yaml::parse(file_get_contents(__DIR__ . '../../../../phinx.yml'));
        $yamlString = Yaml::dump($yaml);
        $yamlArr = Yaml::parse($yamlString);
        return $yamlArr;
    }

    private function getYmlConfig(){

        $yamlArr = $this->readYmlConfig();

        $environments = $yamlArr['environments'];
        $default_adapter = $this->adapter;
        $default_database = $environments['default_database_pdo'];

        return $environments[$default_database][$default_adapter];
    }

    private function getAdapter(){
        $yamlArr = $this->readYmlConfig();
        return $yamlArr['environments']['default_adapter'];
    }

    public function getHost(){
        return $this->host;
    }

    public function setHost($host){
        $this->host = $host;
    }

    public function getPort(){
        return $this->port;
    }

    public function setPort($port){
        $this->port = $port;
    }

    public function getDbname(){
        return $this->dbname;
    }

    public function setDbname($dbname){
        $this->dbname = $dbname;
    }

    public function getUsername(){
        return $this->username;
    }

    public function setUsername($username){
        $this->username = $username;
    }

    public function getPassword(){
        return $this->password;
    }

    public function setPassword($password){
        $this->password = $password;
    }

    public function getTable(){
        return $this->table;
    }

    public function setTable($table){
        $this->table = $table;
        $this->setArrayTableQuery();
    }

    public function setConnect($conn){
        $this->conn = $conn;
    }
    public function getConnect(){
        return $this->conn;
    }

    public function __construct(){
        $config = $this->getYmlConfig();
        $adapter = $this->getAdapter();
        $this->setDefaultAdapter($adapter);
        $this->setConfig($config);
        if($adapter == 'mysql'){
            $this->connect();
        }else{
            $this->connectSrv();
        }
    }

    public function changeAdapter($adapter){
        $this->setDefaultAdapter($adapter);
        $config = $this->getYmlConfig();
        $this->setConfig($config);
        if($adapter == 'mysql'){
            $this->connect();
        }else{
            $this->connectSrv();
        }
    }

    private function setArrayTableQuery(){
        if($this->conn == null){
            return false;
        }
        try{
            $result = $this->conn->prepare('SELECT * FROM ' . $this->getTable() . ' limit 1');
            $result->execute();
            foreach(range(0, $result->columnCount() - 1) as $column_index){
                $meta = $result->getColumnMeta($column_index);
                $this->arrayTableTypeQuery[$meta['name']] = $meta['native_type'];
            }
            $this->_buildArrayTableQuery();
            return true;
        }catch (PDOException $e){
            return false;
        }
    }

    private function _buildArrayTableQuery(){
        if(empty($this->getArrayTableQuery())){
            return false;
        }
        $aryArrayTableQuery = $this->getArrayTableQuery();
        foreach ($aryArrayTableQuery as $item => $value){
            switch ($value){
                case "LONG" :
                case "LONGLONG" :
                case "TINY" :
                    $this->arrayTableTypeQuery[$item] = 'number';
                    break;
                case "VAR_STRING" :
                case "BLOB" :
                    $this->arrayTableTypeQuery[$item] = 'string';
                    break;
                case "TIMESTAMP" :
                    $this->arrayTableTypeQuery[$item] = 'time';
                    break;
            }
        }
        return true;
    }

    public function getArrayTableQuery(){
        return $this->arrayTableTypeQuery;
    }

    private function setDefaultAdapter($adapter){
        $this->adapter = $adapter;
    }

    private function setConfig($config){
        if(isset($config['host']))
            $this->setHost($config['host']);
        if(isset($config['port']))
            $this->setPort($config['port']);
        if(isset($config['user']))
            $this->setUsername($config['user']);
        if(isset($config['pass']))
            $this->setPassword($config['pass']);
        if(isset($config['name']))
            $this->setDbname($config['name']);
    }

    private function connect(){
        try {
            $query_pdo = "mysql:host=" . $this->getHost() . ";dbname=" . $this->getDbname() . ";port=" . $this->getPort() . ";charset=utf8";
            $conn = new PDO($query_pdo,$this->getUsername(),$this->getPassword(), [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"]);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->setConnect($conn);
            return true;
        }
        catch(PDOException $e){
            return false;
        }
    }

    private function connectSrv(){
        try {
            $query_pdo = "sqlsrv:Server=" . $this->getHost() . ";Database=" . $this->getDbname() . ";Port=" . $this->getPort();
            $conn = new PDO($query_pdo,$this->getUsername(),$this->getPassword());
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->setConnect($conn);
            print_r($conn);
            return true;
        }
        catch(PDOException $e){
            return false;
        }
    }

    public function count(){
        $sql = 'SELECT COUNT(*) FROM ' . $this->getTable();
        try{
            $result = $this->conn->prepare($sql);
            $result->execute();
            $result = $result->fetchAll();
            return $result[0][0];
        }catch (PDOException $e){
            return false;
        }
    }

    public function all($select = [], $limit = null, $order = ['id', 'ASC']){
        $sl = count($select) > 0 ? implode(' ,', $select) : '*';
        $sql = "SELECT " . $sl . " FROM " . $this->getTable();
        $sql .= ' order by ' . $order[0] . (isset($order[3]) && $order[3] == 'number' ? ' + 0' : '') . ' ' . $order[1];
        if($limit != null){
            if(gettype($limit) == 'string' || gettype($limit) == 'integer')
                $sql .= " limit " . $limit;
            else if(gettype($limit) == 'array')
                $sql .= " limit " . implode(', ', $limit);
        }
        try{
            $result = $this->conn->prepare($sql);
            $result->execute();
            $result = $result->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }catch (PDOException $e){
            return false;
        }
    }

    public function get($where, $limit = null, $order = []){
        $_where  = $this->_buildWhere($where);
        $sql = "SELECT * FROM " . $this->getTable() . " where " . $_where;
        if(!empty($order)){
            $sql .= " order by ";
            $temp = '';
            foreach ($order as $item => $value){
                $temp .= $item . " " . $value . ',';
            }
            $sql .= rtrim($temp, ',');
        }
        if($limit != null) $sql .= ' limit '. $limit;
        try{
            $result = $this->conn->prepare($sql);
            $result->execute();
            $result = $result->fetchAll(PDO::FETCH_ASSOC);
            return $result ?? [];
        }catch (PDOException $e){
            return [];
        }
    }

    public function one($where, $order = []){
        $get = $this->get($where,1,$order);
        return $get[0] ?? [];
    }

    public function getPage($page, $limit, $order){
        return $this->all(['*'], [(int) $page * (int) $limit - (int) $limit, $limit], $order);
    }

    private function _buildWhere($where){
        if(count($where) == 0){
            return '';
        }
        $ary_columnType = $this->getArrayTableQuery();
        $w = ' id >= 0 ';
        if(gettype($where[0]) != 'array'){
            $w .= " AND ";
            if(count($where) == 3){
                if($where[1] == 'BETWEEN'){
                    $w .= $where[0] . ' BETWEEN "' . $where[2][0] . '" AND "' . $where[2][1] . '"';
                }else{
                    $w .= $where[0] . ' ' . $where[1] . ($ary_columnType[$where[0]] == 'number' ? ' ' : ' "') . $where[2] . ($ary_columnType[$where[0]] == 'number' ? ' ' : ' "');
                }
            }
            else{
                $w .= $where[0] . " = '" . $where[1] . "' ";
            }
            return $w;
        }
        foreach ($where as $item=>$value){
            $w .= ' AND ';
            if(count($value) == 3){
                if($value[1] == 'like'){
                    $w .= $value[0] . ' like "%'. $value[2] . '%"';
                }else if($value[1] == 'IN'){
                    $w .= $value[0] . ' IN ("'. implode('","', $value[2]) . '") ';
                }
                else{
                    $w .= $value[0] . ' ' . $value[1] . ($ary_columnType[$value[0]] == 'number' ? ' ' : ' "') . $value[2] . ($ary_columnType[$value[0]] == 'number' ? ' ' : ' "');
                }
            }
            else{
                $w .= $value[0] . ($ary_columnType[$value[0]] == 'number' ? ' = ' : ' = "') . $value[1] . ($ary_columnType[$value[0]] == 'number' ? '' : '"');
            }
        }
        return $w;
    }

    public function insert($arr, $duplicate = null, $update = false){
        if($duplicate != null){
            if($this->one([$duplicate, $arr[$duplicate]]) != null){
                if($update){
                    return $this->update($arr, [$duplicate, $arr[$duplicate]], false);
                }
                else return false;
            }
        }
        $keys = array_keys($arr);
        $values = array_values($arr);
        $col_insert = implode(', ', $keys);
        $val_insert = array_map(function ($a){
            return "'" . $a . "'";
        },$values);
        $val_insert = implode(', ',$val_insert);
        try{
            $sql = "INSERT INTO " . $this->getTable() . " (" . $col_insert . ") VALUES" . " (" . $val_insert . ")";
            $this->conn->exec($sql);
            return $this->conn->lastInsertId();
        }catch (PDOException $e){
            return false;
        }
    }

    public function update($arr, $where, $flag = true){

        $w = $this->_buildWhere($where);

        $count_arr = count($arr);
        $i = 0;
        $sql = "UPDATE " . $this->getTable() . " SET ";
        foreach ($arr as $item => $value){
            $sql .= $item . ' = "' . $value . '"';
            if($i < ($count_arr - 1)){
                $sql .= ', ';
            }
            $i++;
        }
        $sql .= ' WHERE ' . $w;
        try{
            $this->conn->exec($sql);
            return true;
        }catch (PDOException $e){
            return false;
        }
    }

    public function delete($where, $exc = '='){
        $sql = 'DELETE FROM ' . $this->getTable() . ' WHERE ' . $this->_buildWhere($where);
        try{
            $this->conn->exec($sql);
            return true;
        }catch (PDOException $e){
            return false;
        }
    }

    public function likeEnd($where, $first = false){
        if(gettype($where[0]) == 'array'){
            echo 'NOT LIKE';
            return false;
        }
        $_where = " " . $where[0] . " like '" . ($first ? '' : '%') . $where[1] . ($first ? '%' : '') . "' ";
        $sql = "SELECT * FROM " . $this->getTable() . " where " . $_where;
        try{
            $result = $this->conn->prepare($sql);
            $result->execute();
            $result = $result->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }catch (PDOException $e){
            return false;
        }
    }
    public function likeFirst($where){
        return $this->likeEnd($where,true);
    }

    public function _exec($sql){
        try{
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            return $stmt->fetchAll() ?? [];
        }catch (PDOException $e){
            return [];
        }
    }

    public function exportDb($file){
        ob_start();
        $command = "C:\\xampp\\mysql\\bin\\mysqldump --add-drop-table --host=$this->host --user=$this->username ";
        if ($this->password)
                $command.= "--password=". $this->password ." ";
        $command.= $this->dbname;
        try{
            system($command);
            $dump = ob_get_contents();
            ob_end_clean();
            // send dump file to the output
            $myfile = fopen($file, "w");
            fwrite($myfile, $dump);
            fclose($myfile);
            return "Done!";
        }catch (PDOException $e){
            return $e->getMessage();
        }
    }

}