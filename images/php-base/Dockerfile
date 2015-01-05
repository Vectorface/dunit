# builds a debian wheezy image with php5.4
FROM debian:wheezy
ENV INSTALL_PACKAGES php5-cli php-apc php5-curl php5-gd php5-intl php5-json php5-mcrypt
RUN \
    apt-get -y update && \
    apt-get -y install $INSTALL_PACKAGES && \
    apt-get -y autoremove && \
    apt-get -y clean && \
    rm -rf /var/lib/apt/lists/*
