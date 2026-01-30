# Docker Usage Guide - Java SQLi Lab

This guide explains how to run the Java SQL Injection Lab using Docker.

## Quick Start

### Option 1: Build and Run with Docker (SQLite Only)

```bash
# Build the Docker image
docker build -t sqli-lab .

# Run the container (SQLite only, no external databases)
docker run -p 8080:8080 sqli-lab
```

Access the application at: http://localhost:8080

**Note**: With this option, you can only use SQLite. To switch between databases, use Option 2.

### Option 2: Use Docker Compose (Recommended for Database Switching)

This option starts the application with **all database servers** (MySQL, PostgreSQL) running, allowing you to switch between databases through the web interface.

```bash
# Start all services (app + MySQL + PostgreSQL)
docker-compose up
```

Access the application at: http://localhost:8080

**Features**:
- ✅ Switch between SQLite, MySQL, and PostgreSQL through the web UI
- ✅ All database servers run simultaneously
- ✅ No restart required when switching databases

## Environment Variables

You can customize the database configuration using environment variables:

| Variable | Description | Default |
|----------|-------------|---------|
| `APP_DB_TYPE` | Database type (sqlite, mysql, postgresql, mssql, oracle) | `sqlite` |
| `SPRING_DATASOURCE_URL` | JDBC connection URL | `jdbc:sqlite:sqli.db` |
| `SPRING_DATASOURCE_USERNAME` | Database username | (varies by DB) |
| `SPRING_DATASOURCE_PASSWORD` | Database password | (varies by DB) |
| `SPRING_DATASOURCE_DRIVER_CLASS_NAME` | JDBC driver class name | (auto-detected) |

## Custom Database Configuration

To run with a custom database:

```bash
docker run -p 8080:8080 \
  -e APP_DB_TYPE=mysql \
  -e SPRING_DATASOURCE_URL=jdbc:mysql://your-host:3306/sqli \
  -e SPRING_DATASOURCE_USERNAME=your-user \
  -e SPRING_DATASOURCE_PASSWORD=your-password \
  -e SPRING_DATASOURCE_DRIVER_CLASS_NAME=com.mysql.cj.jdbc.Driver \
  sqli-lab
```

## Building from Source

If you want to build the application locally first:

```bash
# Build with Gradle
./gradlew clean bootJar

# Then build Docker image
docker build -t sqli-lab .
```

## Stopping Containers

```bash
# Stop specific service
docker-compose down sqli-sqlite

# Stop all services
docker-compose down

# Stop and remove volumes (WARNING: deletes all data)
docker-compose down -v
```

## Troubleshooting

### Port Already in Use
If port 8080 is already in use, map to a different port:
```bash
docker run -p 9090:8080 sqli-lab
```

### Database Connection Issues
- Ensure the database container is running before the application
- Check that the database credentials match
- Verify network connectivity between containers

### Viewing Logs
```bash
# Docker
docker logs sqli-lab-sqlite

# Docker Compose
docker-compose logs sqli-sqlite
```

## Security Warning

⚠️ **This application is intentionally vulnerable for educational purposes.**

- Do NOT deploy this to production
- Do NOT expose this to the internet
- Use only in isolated lab environments
- The search functionality contains SQL injection vulnerabilities by design

## Features

- **Vulnerable Search**: SQL injection vulnerability in the search endpoint
- **Safe CRUD Operations**: Add, update, and delete users using prepared statements
- **Multi-Database Support**: Works with SQLite, MySQL, PostgreSQL, MSSQL, and Oracle
- **Educational Interface**: Shows executed SQL queries for learning purposes

## Testing SQL Injection

Example payloads to test:

```sql
# Basic injection
' OR '1'='1

# Union-based injection (adjust columns as needed)
' UNION SELECT 1,2,3--

# Comment out rest of query
admin'--
```

Refer to the main application documentation for more SQL injection examples specific to each database type.
