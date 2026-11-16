FROM php:8.2-apache

# Intentionally old-ish, permissive config for a training target.
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Basic tooling on the foothold so students can pivot into the internal network (Stage 2).
RUN apt-get update && apt-get install -y --no-install-recommends curl iproute2 && rm -rf /var/lib/apt/lists/*

# Weak-by-design PHP settings used by several Stage 1 challenges.
RUN { \
      echo "display_errors = On"; \
      echo "error_reporting = E_ALL"; \
      echo "file_uploads = On"; \
      echo "upload_max_filesize = 20M"; \
      echo "allow_url_fopen = On"; \
      echo "allow_url_include = Off"; \
      echo "expose_php = On"; \
    } > /usr/local/etc/php/conf.d/zz-range.ini

RUN a2enmod rewrite headers
COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY src/ /var/www/html/
RUN mkdir -p /var/www/html/uploads \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 0777 /var/www/html/uploads

RUN mkdir -p /var/flags && chown www-data:www-data /var/flags && chmod 0777 /var/flags
EXPOSE 80
