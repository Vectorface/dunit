# builds a debian wheezy image with the common dependencies for most versions
# of php included
FROM debian:wheezy
ENV INSTALL_PACKAGES php5-cli php-apc php5-curl php5-gd php5-intl php5-json php5-mcrypt
RUN \
    apt-get -y update && \
    apt-get -y install $INSTALL_PACKAGES && \
    apt-get -y remove $INSTALL_PACKAGES && \
    apt-get -y clean && \
    rm -rf /var/lib/apt/lists/*
