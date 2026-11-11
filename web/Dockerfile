FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql mysqli

# Hardened runtime: no debug output, no version disclosure, tight uploads.
RUN { \
      echo "display_errors = Off"; \
      echo "log_errors = On"; \
      echo "error_reporting = E_ALL"; \
      echo "expose_php = Off"; \
      echo "file_uploads = On"; \
      echo "upload_max_filesize = 5M"; \
      echo "allow_url_include = Off"; \
      echo "session.cookie_httponly = On"; \
      echo "session.cookie_samesite = Lax"; \
    } > /usr/local/etc/php/conf.d/zz-range.ini

RUN a2enmod rewrite headers
RUN sed -i 's/^ServerTokens .*/ServerTokens Prod/; s/^ServerSignature .*/ServerSignature Off/' /etc/apache2/conf-available/security.conf
COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY src/ /var/www/html/
RUN mkdir -p /var/www/html/uploads \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 0775 /var/www/html/uploads
EXPOSE 80
