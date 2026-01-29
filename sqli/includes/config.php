<?php
// Application Configuration

// Database configurations
define('DB_MYSQL_HOST', getenv('DB_MYSQL_HOST') ?: 'mysql');
define('DB_MYSQL_USER', getenv('DB_MYSQL_USER') ?: 'sqli_user');
define('DB_MYSQL_PASS', getenv('DB_MYSQL_PASS') ?: 'sqli_pass123');
define('DB_MYSQL_NAME', getenv('DB_MYSQL_NAME') ?: 'sqli_db');

define('DB_PGSQL_HOST', getenv('DB_PGSQL_HOST') ?: 'postgresql');
define('DB_PGSQL_USER', getenv('DB_PGSQL_USER') ?: 'sqli_user');
define('DB_PGSQL_PASS', getenv('DB_PGSQL_PASS') ?: 'sqli_pass123');
define('DB_PGSQL_NAME', getenv('DB_PGSQL_NAME') ?: 'sqli_db');

define('DB_MSSQL_HOST', getenv('DB_MSSQL_HOST') ?: 'mssql');
define('DB_MSSQL_USER', getenv('DB_MSSQL_USER') ?: 'sa');
define('DB_MSSQL_PASS', getenv('DB_MSSQL_PASS') ?: 'SqliPass123!');
define('DB_MSSQL_NAME', getenv('DB_MSSQL_NAME') ?: 'sqli_db');

define('DB_ORACLE_HOST', getenv('DB_ORACLE_HOST') ?: 'oracle');
define('DB_ORACLE_USER', getenv('DB_ORACLE_USER') ?: 'sqli_user');
define('DB_ORACLE_PASS', getenv('DB_ORACLE_PASS') ?: 'sqli_pass123');
define('DB_ORACLE_SID', getenv('DB_ORACLE_SID') ?: 'XEPDB1');

define('DB_SQLITE_PATH', '/var/www/init/sqli.db');

// Default database type
define('DEFAULT_DB_TYPE', 'mysql');

// Application settings
define('APP_NAME', 'Multi-Database SQL Injection Lab');
define('SESSION_LIFETIME', 3600); // 1 hour

// Cookie settings
define('COOKIE_DB_TYPE', 'db_type');
define('COOKIE_LIFETIME', 86400); // 24 hours
