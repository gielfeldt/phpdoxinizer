FROM alpine:3.5

# Add NGINX-Webserver with NAXSI
# Add PHP 7
RUN apk upgrade -U && \
    apk add --update --no-cache \
        curl bash && \
    apk add --update --no-cache --repository=http://dl-4.alpinelinux.org/alpine/edge/testing \
    # web server
        nginx \
        php7-fpm \
    # php
        php7 \
        php7-opcache \
    # composer
        php7-json \
        php7-mbstring \
        php7-phar \
        php7-openssl \
    # hirak/prestissimo
        php7-curl \
    # phpunit
        php7-dom \
        php7-xml \
        php7-xmlwriter \
    # phpdox
        php7-xsl \
        php7-iconv \
        php7-fileinfo \
    # phpdoxinizer
        git

# Add S6-overlay to use S6 process manager
# https://github.com/just-containers/s6-overlay/#the-docker-way
ARG S6_VERSION=v1.19.1.1
RUN curl -sSL https://github.com/just-containers/s6-overlay/releases/download/${S6_VERSION}/s6-overlay-amd64.tar.gz | tar zxf -

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www

COPY composer.* ./
RUN composer global require "hirak/prestissimo:^0.3" && \
    composer install --prefer-dist --no-dev && \
    composer clearcache

COPY /rootfs /

COPY . .

ENV PATH $PATH:/var/www:/var/www/vendor/bin
ENV S6_BEHAVIOUR_IF_STAGE2_FAILS=2

EXPOSE 80

ENTRYPOINT ["/init"]
