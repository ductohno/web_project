-- Wait for SQL Server to be ready
WAITFOR DELAY '00:00:05';
GO

-- Create database if it doesn't exist
IF NOT EXISTS (SELECT name FROM sys.databases WHERE name = 'sqli')
BEGIN
    CREATE DATABASE sqli;
    PRINT 'Database sqli created successfully';
END
ELSE
BEGIN
    PRINT 'Database sqli already exists';
END
GO

-- Switch to sqli database
USE sqli;
GO

PRINT 'MSSQL initialization complete';
GO
