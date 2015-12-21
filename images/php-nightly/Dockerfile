# builds a debian jessie image with php-nightly compiled from github
FROM debian:jessie
ENV BUILD_DEPS git make autoconf build-essential g++ libc6-dev bison re2c libxml2-dev
ENV RUNTIME_DEPS libxml2
RUN \
    apt-get -y update && \
	apt-get -y upgrade && \
    apt-get -y install $BUILD_DEPS && \
    git clone https://github.com/php/php-src.git /opt/php-src --depth=1 && \
    cd /opt/php-src && \
    ./buildconf --force && ./configure --quiet --prefix=/opt/php-nightly && \
    make -j8 --quiet && \
    cp /opt/php-src/sapi/cli/php /usr/local/bin/php && \
    apt-get -y remove --purge $BUILD_DEPS && \
    apt-get -y install $RUNTIME_DEPS && \
    apt-get -y autoremove --purge && \
    apt-get -y clean && \
    rm -rf /var/lib/apt/lists/* /opt/php-src
