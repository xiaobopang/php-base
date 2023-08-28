FROM peterpang/base:v1

LABEL maintainer="pangxiaobo <10846295@qq.com>" version="1.0" app.name="base-api"

##
# ---------- env settings ----------
##
# --build-arg timezone=Asia/Shanghai
ARG timezone

ENV TIMEZONE=${timezone:-"Asia/Shanghai"} \
    APP_ENV=prod \
    SCAN_CACHEABLE=(false)

# update
RUN set -ex \
    # show php version and extensions
    && php -v \
    && php -m \
    && php --ri swoole \
    #  ---------- some config ----------
    && cd /etc/php* \
    # - config PHP
    && { \
        echo "upload_max_filesize=128M"; \
        echo "post_max_size=128M"; \
        echo "memory_limit=1G"; \
        echo "date.timezone=${TIMEZONE}"; \
    } | tee conf.d/99_overrides.ini \
    # - config timezone
    && ln -sf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime \
    && echo "${TIMEZONE}" > /etc/timezone \
    # ---------- clear works ----------
    && rm -rf /var/cache/apk/* /tmp/* /usr/share/man \
    && apk add --no-cache supervisor vim \
    && echo -e "\033[42;37m Build Completed :).\033[0m\n"


COPY supervisor /etc/supervisor
COPY ../src /var/www
WORKDIR /var/www
RUN composer install --no-dev -o

CMD ["/usr/bin/supervisord", "-n","-c", "/etc/supervisor/supervisord.conf"]
