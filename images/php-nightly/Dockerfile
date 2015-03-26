# builds a debian wheezy image with php-nightly compiled from github
FROM vectorface/php-base
ENV BUILD_DEPS git make autoconf build-essential g++ libc6-dev bison re2c libxml2-dev
ENV RUNTIME_DEPS libxml2
RUN cp /etc/apt/sources.list /etc/apt/sources.list.backup
RUN echo "deb-src http://http.debian.net/debian wheezy main" >> /etc/apt/sources.list
RUN echo "deb-src http://http.debian.net/debian wheezy-updates main" >> /etc/apt/sources.list
RUN \
    apt-get -y update && \
    apt-get -y install $BUILD_DEPS
RUN \
    cd /opt && \
    git clone https://github.com/php/php-src.git --depth=1
RUN \
    cd /opt/php-src && \
    ./buildconf --force && ./configure --quiet --prefix=/opt/php-nightly && \
    make -j8 --quiet && \
    cp /opt/php-src/sapi/cli/php /usr/local/bin/php
RUN \
    apt-get -y update && \
    apt-get -y remove $BUILD_DEPS && \
    apt-get -y install $RUNTIME_DEPS && \
    apt-get -y autoremove && \
    apt-get -y clean && \
    rm -rf /var/lib/apt/lists/* /opt/php-src
RUN mv /etc/apt/sources.list.backup /etc/apt/sources.list
