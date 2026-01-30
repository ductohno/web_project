<?php
require_once __DIR__ . '/config.php';

class Database
{
    private static $instance = null;
    private $connection = null;
    private $dbType = null;

    private function __construct()
    {
        $this->dbType = $this->getDbType();
        $this->connect();
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function getDbType()
    {
        // Check cookie for database type
        if (isset($_COOKIE[COOKIE_DB_TYPE])) {
            $type = $_COOKIE[COOKIE_DB_TYPE];
            if (in_array($type, ['mysql', 'postgresql', 'mssql', 'oracle', 'sqlite'])) {
                return $type;
            }
        }
        return DEFAULT_DB_TYPE;
    }

    public function getCurrentDbType()
    {
        return $this->dbType;
    }

    public function switchDatabase($type)
    {
        if (in_array($type, ['mysql', 'postgresql', 'mssql', 'oracle', 'sqlite'])) {
            setcookie(COOKIE_DB_TYPE, $type, time() + COOKIE_LIFETIME, '/');
            $_COOKIE[COOKIE_DB_TYPE] = $type;
            $this->dbType = $type;
            $this->connect();
            return true;
        }
        return false;
    }

    private function connect()
    {
        try {
            switch ($this->dbType) {
                case 'mysql':
                    $dsn = "mysql:host=" . DB_MYSQL_HOST . ";dbname=" . DB_MYSQL_NAME . ";charset=utf8mb4";
                    $this->connection = new PDO($dsn, DB_MYSQL_USER, DB_MYSQL_PASS);
                    break;

                case 'postgresql':
                    $dsn = "pgsql:host=" . DB_PGSQL_HOST . ";dbname=" . DB_PGSQL_NAME;
                    $this->connection = new PDO($dsn, DB_PGSQL_USER, DB_PGSQL_PASS);
                    break;

                case 'mssql':
                    $dsn = "sqlsrv:Server=" . DB_MSSQL_HOST . ";Database=" . DB_MSSQL_NAME . ";TrustServerCertificate=yes";
                    $this->connection = new PDO($dsn, DB_MSSQL_USER, DB_MSSQL_PASS);
                    break;

                case 'oracle':
                    $conn = oci_connect(
                        DB_ORACLE_USER,
                        DB_ORACLE_PASS,
                        DB_ORACLE_HOST . ':1521/' . DB_ORACLE_SID,
                        'AL32UTF8'
                    );

                    if (!$conn) {
                        $e = oci_error();
                        throw new Exception('Oracle connection failed: ' . $e['message']);
                    }

                    $this->connection = $conn;
                    return;


                case 'sqlite':
                    // Initialize SQLite database if it doesn't exist
                    if (!file_exists(DB_SQLITE_PATH)) {
                        $this->initializeSqlite();
                    }
                    $dsn = "sqlite:" . DB_SQLITE_PATH;
                    $this->connection = new PDO($dsn);
                    break;

                default:
                    throw new Exception("Unsupported database type: " . $this->dbType);
            }

            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    private function initializeSqlite()
    {
        $sqlFile = __DIR__ . '/../init/sqlite.sql';
        if (file_exists($sqlFile)) {
            $sql = file_get_contents($sqlFile);
            $tempConn = new PDO("sqlite:" . DB_SQLITE_PATH);
            $tempConn->exec($sql);
        }
    }

    public function getConnection()
    {
        return $this->connection;
    }

    // Safe query with prepared statements
    public function query($sql, $params = [])
    {
        // Handle Oracle separately using OCI functions
        if ($this->dbType === 'oracle') {
            return $this->oracleQuery($sql, $params);
        }

        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Query error: " . $e->getMessage());
            throw new Exception("Query failed: " . $e->getMessage());
        }
    }

    // Oracle-specific query using OCI functions
    private function oracleQuery($sql, $params = [])
    {
        try {
            // Convert ? placeholders to :bind1, :bind2, etc. for Oracle
            $bindCount = 0;
            $oracleSql = preg_replace_callback('/\?/', function () use (&$bindCount) {
                $bindCount++;
                return ':bind' . $bindCount;
            }, $sql);

            $stmt = oci_parse($this->connection, $oracleSql);

            if (!$stmt) {
                $e = oci_error($this->connection);
                throw new Exception("Oracle parse error: " . $e['message']);
            }

            // Bind parameters
            foreach ($params as $index => $value) {
                $bindName = ':bind' . ($index + 1);
                oci_bind_by_name($stmt, $bindName, $params[$index]);
            }

            // Execute
            $result = oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);

            if (!$result) {
                $e = oci_error($stmt);
                throw new Exception("Oracle execute error: " . $e['message']);
            }

            // Return a wrapper object that mimics PDOStatement
            return new OracleStatementWrapper($stmt);

        } catch (Exception $e) {
            error_log("Oracle query error: " . $e->getMessage());
            throw new Exception("Query failed: " . $e->getMessage());
        }
    }

    // VULNERABLE: Direct query execution for SQL injection demonstration
    public function vulnerableQuery($sql)
    {
        // Handle Oracle separately
        if ($this->dbType === 'oracle') {
            $stmt = oci_parse($this->connection, $sql);
            if (!$stmt) {
                error_log("Oracle parse error in vulnerableQuery");
                return false;
            }
            $result = oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
            if (!$result) {
                error_log("Oracle execute error in vulnerableQuery");
                return false;
            }
            return new OracleStatementWrapper($stmt);
        }

        try {
            return $this->connection->query($sql);
        } catch (PDOException $e) {
            error_log("Vulnerable query error: " . $e->getMessage());
            return false;
        }
    }

    public function lastInsertId()
    {
        if ($this->dbType === 'oracle') {
            // Oracle uses sequences, handle differently
            return null;
        }
        return $this->connection->lastInsertId();
    }

    public function beginTransaction()
    {
        if ($this->dbType === 'oracle') {
            // Oracle doesn't need explicit transaction start
            return true;
        }
        return $this->connection->beginTransaction();
    }

    public function commit()
    {
        if ($this->dbType === 'oracle') {
            return oci_commit($this->connection);
        }
        return $this->connection->commit();
    }

    public function rollback()
    {
        if ($this->dbType === 'oracle') {
            return oci_rollback($this->connection);
        }
        return $this->connection->rollback();
    }
}

/**
 * Wrapper class to make OCI statements compatible with PDO-style code
 */
class OracleStatementWrapper
{
    private $stmt;

    public function __construct($stmt)
    {
        $this->stmt = $stmt;
    }

    public function fetch($fetchStyle = null)
    {
        if ($fetchStyle === PDO::FETCH_ASSOC || $fetchStyle === null) {
            $row = oci_fetch_assoc($this->stmt);
        } else {
            $row = oci_fetch_array($this->stmt, OCI_BOTH);
        }

        if (!$row)
            return false;

        // Convert OCILob objects to strings
        foreach ($row as $key => $value) {
            if (is_object($value) && $value instanceof OCILob) {
                $row[$key] = $value->load();
            }
        }

        return array_change_key_case($row, CASE_LOWER);
    }

    public function fetchAll($fetchStyle = null)
    {
        $results = [];
        while ($row = $this->fetch($fetchStyle)) {
            $results[] = $row;
        }
        return $results;
    }

    public function execute($params = [])
    {
        // This is called from oracleQuery, parameters are already bound
        return oci_execute($this->stmt, OCI_COMMIT_ON_SUCCESS);
    }

    public function rowCount()
    {
        return oci_num_rows($this->stmt);
    }
}
