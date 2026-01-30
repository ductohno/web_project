-- Oracle Database Initialization

-- Create sequences
BEGIN
    EXECUTE IMMEDIATE 'CREATE SEQUENCE users_seq START WITH 1 INCREMENT BY 1';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -955 THEN
            RAISE;
        END IF;
END;
/

BEGIN
    EXECUTE IMMEDIATE 'CREATE SEQUENCE items_seq START WITH 1 INCREMENT BY 1';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -955 THEN
            RAISE;
        END IF;
END;
/

-- Create users table
BEGIN
    EXECUTE IMMEDIATE 'CREATE TABLE users (
        id NUMBER PRIMARY KEY,
        username VARCHAR2(50) UNIQUE NOT NULL,
        password VARCHAR2(255) NOT NULL,
        email VARCHAR2(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -955 THEN
            RAISE;
        END IF;
END;
/

-- Create items table
BEGIN
    EXECUTE IMMEDIATE 'CREATE TABLE items (
        id NUMBER PRIMARY KEY,
        user_id NUMBER NOT NULL,
        title VARCHAR2(200) NOT NULL,
        description CLOB,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_items_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -955 THEN
            RAISE;
        END IF;
END;
/

-- Create trigger for users auto-increment
CREATE OR REPLACE TRIGGER users_bir
BEFORE INSERT ON users
FOR EACH ROW
BEGIN
    IF :NEW.id IS NULL THEN
        SELECT users_seq.NEXTVAL INTO :NEW.id FROM dual;
    END IF;
END;
/

-- Create trigger for items auto-increment
CREATE OR REPLACE TRIGGER items_bir
BEFORE INSERT ON items
FOR EACH ROW
BEGIN
    IF :NEW.id IS NULL THEN
        SELECT items_seq.NEXTVAL INTO :NEW.id FROM dual;
    END IF;
END;
/

-- Create trigger for updated_at
CREATE OR REPLACE TRIGGER items_bur
BEFORE UPDATE ON items
FOR EACH ROW
BEGIN
    :NEW.updated_at := CURRENT_TIMESTAMP;
END;
/

-- Insert sample data
BEGIN
    INSERT INTO users (username, password, email) VALUES 
    ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@sqli.lab');
    
    INSERT INTO users (username, password, email) VALUES 
    ('testuser', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'test@sqli.lab');
    
    COMMIT;
EXCEPTION
    WHEN DUP_VAL_ON_INDEX THEN
        NULL;
END;
/

BEGIN
    INSERT INTO items (user_id, title, description) VALUES
    (1, 'Oracle Item 1', 'This is a test item in Oracle database');
    
    INSERT INTO items (user_id, title, description) VALUES
    (1, 'Oracle Item 2', 'Another test item for SQL injection testing');
    
    INSERT INTO items (user_id, title, description) VALUES
    (2, 'User Item', 'Item created by test user');
    
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        NULL;
END;
/
