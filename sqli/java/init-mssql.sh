#!/bin/bash
# Wait for SQL Server to start
sleep 30s

# Create database
/opt/mssql-tools/bin/sqlcmd -S localhost -U sa -P YourStrong@Passw0rd -Q "IF NOT EXISTS (SELECT name FROM sys.databases WHERE name = 'sqli') CREATE DATABASE sqli"

echo "MSSQL database 'sqli' created successfully"
