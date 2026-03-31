FROM hyperf/hyperf:8.4-alpine-v3.21-swoole

ARG timezone

ENV TIMEZONE=${timezone:-"America/Sao_Paulo"} \
    APP_ENV=dev \
    SCAN_CACHEABLE=false

RUN set -ex \
    && php -v \
    && php -m \
    && php --ri swoole \
    && cd /etc/php* \
    && { \
        echo "upload_max_filesize=128M"; \
        echo "post_max_size=128M"; \
        echo "memory_limit=1G"; \
        echo "date.timezone=${TIMEZONE}"; \
    } | tee conf.d/99_overrides.ini \
    && ln -sf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime \
    && echo "${TIMEZONE}" > /etc/timezone \
    && rm -rf /var/cache/apk/* /tmp/* /usr/share/man

WORKDIR /opt/www

COPY . /opt/www

# 🔥 instala dependências corretamente
RUN composer install --no-dev -o

EXPOSE 9501

# 🔥 ESSENCIAL: iniciar o Hyperf
CMD ["php", "bin/hyperf.php", "start"]