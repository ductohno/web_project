package com.example.sqli.util;

import org.springframework.beans.factory.annotation.Value;
import org.springframework.stereotype.Component;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.util.Arrays;
import java.util.List;

@Component
public class DBFactory {

    private String dbType;

    // SQLite
    @Value("${app.datasource.sqlite.url}")
    private String sqliteUrl;

    // MySQL
    @Value("${app.datasource.mysql.url}")
    private String mysqlUrl;
    @Value("${app.datasource.mysql.username}")
    private String mysqlUser;
    @Value("${app.datasource.mysql.password}")
    private String mysqlPass;

    // PostgreSQL
    @Value("${app.datasource.postgresql.url}")
    private String pgUrl;
    @Value("${app.datasource.postgresql.username}")
    private String pgUser;
    @Value("${app.datasource.postgresql.password}")
    private String pgPass;

    // MSSQL
    @Value("${app.datasource.mssql.url}")
    private String mssqlUrl;
    @Value("${app.datasource.mssql.username}")
    private String mssqlUser;
    @Value("${app.datasource.mssql.password}")
    private String mssqlPass;

    // Oracle
    @Value("${app.datasource.oracle.url}")
    private String oracleUrl;
    @Value("${app.datasource.oracle.username}")
    private String oracleUser;
    @Value("${app.datasource.oracle.password}")
    private String oraclePass;

    public DBFactory(@Value("${app.db.type}") String initialDbType) {
        this.dbType = initialDbType;
    }

    public synchronized Connection getConnection() throws SQLException {
        switch (dbType.toLowerCase()) {
            case "sqlite":
                return DriverManager.getConnection(sqliteUrl);
            case "mysql":
                return DriverManager.getConnection(mysqlUrl, mysqlUser, mysqlPass);
            case "postgresql":
                return DriverManager.getConnection(pgUrl, pgUser, pgPass);
            case "mssql":
                return DriverManager.getConnection(mssqlUrl, mssqlUser, mssqlPass);
            case "oracle":
                return DriverManager.getConnection(oracleUrl, oracleUser, oraclePass);
            default:
                throw new SQLException("Unknown database type: " + dbType);
        }
    }

    public synchronized String getDbType() {
        return dbType;
    }

    /**
     * Switch to a different database type at runtime
     * 
     * @param newDbType The new database type (sqlite, mysql, postgresql, mssql,
     *                  oracle)
     * @throws SQLException if the connection to the new database fails
     */
    public synchronized void switchDatabase(String newDbType) throws SQLException {
        // Validate the new database type
        if (!getAvailableDatabases().contains(newDbType.toLowerCase())) {
            throw new SQLException("Invalid database type: " + newDbType);
        }

        // Test connection before switching
        String oldDbType = this.dbType;
        this.dbType = newDbType;

        try {
            // Test the connection
            try (Connection testConn = getConnection()) {
                // Connection successful
                System.out.println("Successfully switched database from " + oldDbType + " to " + newDbType);
            }
        } catch (SQLException e) {
            // Rollback to old database type if connection fails
            this.dbType = oldDbType;
            throw new SQLException("Failed to connect to " + newDbType + " database: " + e.getMessage(), e);
        }
    }

    /**
     * Get list of all supported database types
     * 
     * @return List of database type names
     */
    public List<String> getAvailableDatabases() {
        return Arrays.asList("sqlite", "mysql", "postgresql", "mssql", "oracle");
    }

    /**
     * Test if a connection can be established to a specific database type
     * 
     * @param testDbType The database type to test
     * @return true if connection successful, false otherwise
     */
    public synchronized boolean testConnection(String testDbType) {
        String originalDbType = this.dbType;
        this.dbType = testDbType;

        try (Connection testConn = getConnection()) {
            return true;
        } catch (SQLException e) {
            return false;
        } finally {
            this.dbType = originalDbType;
        }
    }

    /**
     * Get connection details for current database (for display purposes)
     * 
     * @return Connection string (without credentials)
     */
    public synchronized String getConnectionInfo() {
        switch (dbType.toLowerCase()) {
            case "sqlite":
                return sqliteUrl;
            case "mysql":
                return mysqlUrl + " (user: " + mysqlUser + ")";
            case "postgresql":
                return pgUrl + " (user: " + pgUser + ")";
            case "mssql":
                return mssqlUrl + " (user: " + mssqlUser + ")";
            case "oracle":
                return oracleUrl + " (user: " + oracleUser + ")";
            default:
                return "Unknown";
        }
    }
}
