-- MSSQL Database Initialization

-- Create database if not exists
IF NOT EXISTS (SELECT * FROM sys.databases WHERE name = 'sqli_db')
BEGIN
    CREATE DATABASE sqli_db;
END
GO

USE sqli_db;
GO

-- Create users table
IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='users' AND xtype='U')
BEGIN
    CREATE TABLE users (
        id INT IDENTITY(1,1) PRIMARY KEY,
        username NVARCHAR(50) UNIQUE NOT NULL,
        password NVARCHAR(255) NOT NULL,
        email NVARCHAR(100) NOT NULL,
        created_at DATETIME DEFAULT GETDATE()
    );
END
GO

-- Create items table
IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='items' AND xtype='U')
BEGIN
    CREATE TABLE items (
        id INT IDENTITY(1,1) PRIMARY KEY,
        user_id INT NOT NULL,
        title NVARCHAR(200) NOT NULL,
        description NVARCHAR(MAX),
        created_at DATETIME DEFAULT GETDATE(),
        updated_at DATETIME DEFAULT GETDATE(),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );
END
GO

-- Create trigger for updated_at
IF EXISTS (SELECT * FROM sys.triggers WHERE name = 'trg_items_updated_at')
    DROP TRIGGER trg_items_updated_at;
GO

CREATE TRIGGER trg_items_updated_at
ON items
AFTER UPDATE
AS
BEGIN
    SET NOCOUNT ON;
    UPDATE items
    SET updated_at = GETDATE()
    FROM items i
    INNER JOIN inserted ins ON i.id = ins.id;
END
GO

-- Insert sample data
IF NOT EXISTS (SELECT * FROM users WHERE username = 'admin')
BEGIN
    INSERT INTO users (username, password, email) VALUES 
    ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@sqli.lab'),
    ('testuser', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'test@sqli.lab');
END
GO

IF NOT EXISTS (SELECT * FROM items)
BEGIN
    INSERT INTO items (user_id, title, description) VALUES
    (1, 'MSSQL Item 1', 'This is a test item in MSSQL database'),
    (1, 'MSSQL Item 2', 'Another test item for SQL injection testing'),
    (2, 'User Item', 'Item created by test user');
END
GO
