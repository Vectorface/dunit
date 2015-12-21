# builds a debian jessie image with php 7.0
FROM debian:jessie
ADD http://www.dotdeb.org/dotdeb.gpg /dotdeb.gpg
ADD /dotdeb.list /etc/apt/sources.list.d/dotdeb.list
ENV INSTALL_PACKAGES php7.0-cli php7.0-curl php7.0-gd php7.0-intl php7.0-json php7.0-mcrypt
RUN \
    apt-key add /dotdeb.gpg && rm /dotdeb.gpg && \
    apt-get -y update && \
    apt-get -y install $INSTALL_PACKAGES && \
    apt-get -y autoremove && \
    apt-get -y clean && \
    rm -rf /var/lib/apt/lists/*
