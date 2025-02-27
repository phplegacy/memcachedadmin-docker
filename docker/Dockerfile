# Dev environment image
# docker build --pull --tag php-70 --build-arg BUILDKIT_INLINE_CACHE=1 --target php-prod --file ./docker/images/php/Dockerfile ./ && docker run -it --rm php-70
FROM php:7.4-fpm-alpine as php-dev

ENV TERM="xterm-256color" \
LANGUAGE="en_US.UTF-8" \
LANG="en_US.UTF-8" \
LC_TIME="en_DK.UTF-8" \
TIME_STYLE="long-iso"

WORKDIR /app/

# Core utilites install
RUN set -eux \
  && apk add --quiet --update --no-cache \
    bash \
    tzdata \
    iputils \
    curl \
    # `fcgi` - Healthcheck |  https://github.com/renatomefi/php-fpm-healthcheck
    fcgi \
    # `tini` - https://github.com/krallin/tini
    tini

# Easily install PHP extension in Docker containers | https://github.com/mlocati/docker-php-extension-installer
RUN set -eux \
    && curl -fsSL https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions --output /usr/local/bin/install-php-extensions \
    && chmod +x /usr/local/bin/install-php-extensions \
    && IPE_GD_WITHOUTAVIF=1 install-php-extensions \
        @composer-2 \
        opcache \
        memcache \
        memcached

# Caddy | https://caddyserver.com/
COPY --from=caddy:2-alpine /usr/bin/caddy /usr/local/bin/caddy
COPY ./docker/config/caddy/Caddyfile /etc/caddy/Caddyfile
RUN set -eux \
    && caddy fmt --overwrite /etc/caddy/Caddyfile \
    && caddy version

# Healthcheck |  https://github.com/renatomefi/php-fpm-healthcheck
RUN set -eux \
    && curl -fsSL https://raw.githubusercontent.com/renatomefi/php-fpm-healthcheck/master/php-fpm-healthcheck --output /usr/local/bin/php-fpm-healthcheck \
    && chmod +x /usr/local/bin/php-fpm-healthcheck \
    && echo "pm.status_path = /status" >> /usr/local/etc/php-fpm.d/zz-docker.conf
HEALTHCHECK --interval=1m --start-period=10s CMD (curl --fail http://localhost/styles/style.css && php-fpm-healthcheck) || exit 1

# set bash as a default shell
RUN set -eux \
    && echo "" > /root/.ash_history \
    && sed -i -e "s/bin\/ash/bin\/bash/" /etc/passwd

# Making new local user
ARG APP_UID=1000
ARG APP_GID=1000
RUN set -eux \
    && addgroup -g $APP_GID user && adduser -D -G user -u $APP_UID user && chown user:user -R ./
# environment
ENV HOME /home/user
ENV PATH ${PATH}:${HOME}/.composer/vendor/bin:${HOME}/bin:/app/vendor/bin/

RUN set -eux && cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini
COPY ./docker/config/php/zzz-php_dev_custom.ini /usr/local/etc/php/conf.d/zzz-php_dev_custom.ini
COPY ./docker/config/php/php-fpm.d/zzz-php-fpm_dev_custom.conf /usr/local/etc/php-fpm.d/zzz-php-fpm_custom.conf
COPY ./docker/config/composer/composer-allow-plugins.json /root/.composer/composer.json
COPY --chown=user:user ./docker/config/composer/composer-allow-plugins.json ${HOME}/.composer/composer.json

RUN set -eux \
    && caddy fmt --overwrite /etc/caddy/Caddyfile

LABEL org.opencontainers.image.title="MemcachedAdmin Docker image" \
    org.opencontainers.image.description="PHPLegacy MemcachedAdmin Docker image" \
    org.opencontainers.image.authors="https://github.com/phplegacy/memcachedadmin-docker" \
    org.opencontainers.image.documentation="https://github.com/phplegacy/memcachedadmin-docker" \
    org.opencontainers.image.url="https://github.com/phplegacy/memcachedadmin-docker" \
    org.opencontainers.image.source="https://github.com/phplegacy/memcachedadmin-docker"

ARG TZ='UTC'
ENV TZ=$TZ

#These params meant to be set by CI
ARG BUILD_DATE
ENV BUILD_DATE=$BUILD_DATE
RUN echo $BUILD_DATE
ARG BUILD_FINGERPRINT
ENV BUILD_FINGERPRINT=$BUILD_FINGERPRINT
RUN echo $BUILD_FINGERPRINT

# https://cs.symfony.com/doc/usage.html
ENV PHP_CS_FIXER_FUTURE_MODE=1

USER user

# entrypoint init
COPY --chmod=0755 ./docker/etc/docker-entrypoint.sh /docker-entrypoint.sh
ENTRYPOINT ["tini", "--", "/docker-entrypoint.sh"]
CMD ["php-fpm"]
EXPOSE 80

FROM php-dev as php-prod

COPY ./app/ /app/
RUN set -eux \
    && composer install --prefer-dist --no-interaction --no-dev --no-progress --optimize-autoloader
