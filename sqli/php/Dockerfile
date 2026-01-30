FROM php:8.2-apache

# ===============================
# 1. System dependencies
# ===============================
RUN apt-get update && apt-get install -y \
    git curl wget gnupg unzip \
    libpng-dev libonig-dev libxml2-dev \
    libpq-dev sqlite3 libsqlite3-dev \
    unixodbc-dev \
    && rm -rf /var/lib/apt/lists/*

# ===============================
# 2. PHP core extensions
# ===============================
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    mysqli \
    pgsql

# ===============================
# 3. Microsoft ODBC Driver (SQL Server)
# ===============================
RUN curl https://packages.microsoft.com/keys/microsoft.asc \
    | gpg --dearmor > /usr/share/keyrings/microsoft-prod.gpg \
    && curl https://packages.microsoft.com/config/debian/12/prod.list \
    > /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update \
    && ACCEPT_EULA=Y apt-get install -y \
        msodbcsql18 \
        mssql-tools18 \
    && rm -rf /var/lib/apt/lists/*

RUN export PATH="/opt/mssql-tools18/bin:$PATH"

# ===============================
# 4. SQLSRV & PDO_SQLSRV
# ===============================
RUN pecl install sqlsrv-5.12.0 pdo_sqlsrv-5.12.0 \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv

# ===============================
# 5. Oracle Instant Client (Optional - download from Oracle)
# ===============================
# Install libaio1 dependency for Oracle
RUN apt-get update && apt-get install -y \
    libaio1t64 \
    && ln -s /usr/lib/x86_64-linux-gnu/libaio.so.1t64 /usr/lib/x86_64-linux-gnu/libaio.so.1 \
    && rm -rf /var/lib/apt/lists/*


# Download and install Oracle Instant Client
RUN mkdir -p /opt/oracle && cd /opt/oracle \
    && wget -q https://download.oracle.com/otn_software/linux/instantclient/2340000/instantclient-basic-linux.x64-23.4.0.24.05.zip \
    && wget -q https://download.oracle.com/otn_software/linux/instantclient/2340000/instantclient-sdk-linux.x64-23.4.0.24.05.zip \
    && unzip -qo instantclient-basic-linux.x64-23.4.0.24.05.zip \
    && unzip -qo instantclient-sdk-linux.x64-23.4.0.24.05.zip \
    && rm -f *.zip \
    && echo /opt/oracle/instantclient_23_4 > /etc/ld.so.conf.d/oracle-instantclient.conf \
    && ldconfig

# Install OCI8 extension
ENV LD_LIBRARY_PATH=/opt/oracle/instantclient_23_4
ENV PATH=/opt/oracle/instantclient_23_4:$PATH

RUN echo "instantclient,/opt/oracle/instantclient_23_4" | pecl install oci8 \
    && docker-php-ext-enable oci8

# ===============================
# 6. Apache config
# ===============================
RUN a2enmod rewrite

# ===============================
# 7. App files
# ===============================
WORKDIR /var/www/html

COPY public/ /var/www/html/
COPY includes/ /var/www/includes/

RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www

# ===============================
# 8. Expose & start
# ===============================
EXPOSE 80
CMD ["apache2-foreground"]
