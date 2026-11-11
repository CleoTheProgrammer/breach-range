FROM php:8.2-apache
RUN docker-php-ext-install pdo pdo_mysql
# The console is the SECURE side. Lock it down.
RUN { \
      echo "display_errors = Off"; \
      echo "session.cookie_httponly = On"; \
      echo "session.cookie_samesite = Lax"; \
      echo "expose_php = Off"; \
    } > /usr/local/etc/php/conf.d/zz-console.ini
COPY src/ /var/www/html/
EXPOSE 80
