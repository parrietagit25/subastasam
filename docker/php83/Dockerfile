# Imagen PHP 8.3.36 de Subastas PCR.
# El código se monta en tiempo de ejecución (./php20 -> /var/www/html); la imagen no contiene
# código, .env, respaldos ni documentos. Construir con este directorio como contexto:
#   docker build -t subastaspcr-php:8.3.36-AAAAMMDD docker/php83
ARG PHP_IMAGE=php:8.3.36-apache
FROM ${PHP_IMAGE}

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        libfreetype-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libzip-dev \
        zlib1g-dev; \
    docker-php-ext-configure gd --with-freetype --with-jpeg; \
    docker-php-ext-install -j"$(nproc)" gd zip pdo_mysql; \
    docker-php-ext-enable opcache; \
    rm -rf /var/lib/apt/lists/*

RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY php-subastas.ini "$PHP_INI_DIR/conf.d/zz-subastas.ini"
COPY apache-subastas.conf /etc/apache2/conf-available/subastas.conf
RUN a2enmod remoteip headers && a2enconf subastas

HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
    CMD curl -fsS -o /dev/null http://127.0.0.1/subasta/index.php || exit 1
