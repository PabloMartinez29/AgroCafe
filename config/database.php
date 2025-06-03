<?php

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'cafetrade_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

class Database {
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $charset = DB_CHARSET;
    private $pdo;


    public function getConnection() {
        $this->pdo = null;

        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
            die();
        }

        return $this->pdo;
    }

    public function closeConnection() {
        $this->pdo = null;
    }


    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch(PDOException $exception) {
            echo "Error en consulta: " . $exception->getMessage();
            return false;
        }
    }


    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }


    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }


    public function commit() {
        return $this->pdo->commit();
    }

    public function rollback() {
        return $this->pdo->rollback();
    }
}


function getDB() {
    $database = new Database();
    return $database->getConnection();
}


function executeQuery($sql, $params = []) {
    $database = new Database();
    $pdo = $database->getConnection();
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch(PDOException $exception) {
        echo "Error en consulta: " . $exception->getMessage();
        return false;
    }
}


function fetchOne($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt ? $stmt->fetch() : false;
}


function fetchAll($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt ? $stmt->fetchAll() : false;
}


function insertRecord($table, $data) {
    $columns = implode(',', array_keys($data));
    $placeholders = ':' . implode(', :', array_keys($data));
    
    $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
    
    $stmt = executeQuery($sql, $data);
    return $stmt ? true : false;
}


function updateRecord($table, $data, $where, $whereParams = []) {
    $setClause = [];
    foreach (array_keys($data) as $column) {
        $setClause[] = "{$column} = :{$column}";
    }
    $setClause = implode(', ', $setClause);
    
    $sql = "UPDATE {$table} SET {$setClause} WHERE {$where}";
    
 
    $allParams = $data;
    if (is_array($whereParams)) {
   
        if (isset($whereParams[0])) {
            $whereParamNames = [];
            preg_match_all('/\?/', $where, $matches);
            for ($i = 0; $i < count($whereParams); $i++) {
                $paramName = 'where_param_' . $i;
                $whereParamNames[$paramName] = $whereParams[$i];
                $where = preg_replace('/\?/', ':' . $paramName, $where, 1);
            }
            $allParams = array_merge($data, $whereParamNames);
            $sql = "UPDATE {$table} SET {$setClause} WHERE {$where}";
        } else {
            $allParams = array_merge($data, $whereParams);
        }
    }
    
    $stmt = executeQuery($sql, $allParams);
    return $stmt ? true : false;
}

function deleteRecord($table, $where, $params = []) {
    
    if (is_array($params) && isset($params[0])) {
        $namedParams = [];
        preg_match_all('/\?/', $where, $matches);
        for ($i = 0; $i < count($params); $i++) {
            $paramName = 'param_' . $i;
            $namedParams[$paramName] = $params[$i];
            $where = preg_replace('/\?/', ':' . $paramName, $where, 1);
        }
        $params = $namedParams;
    }
    
    $sql = "DELETE FROM {$table} WHERE {$where}";
    $stmt = executeQuery($sql, $params);
    return $stmt ? true : false;
}


try {
    $testConnection = getDB();
    if ($testConnection) {
        
    }
} catch (Exception $e) {
    echo "Error al conectar con la base de datos: " . $e->getMessage();
    die();
}
?>
