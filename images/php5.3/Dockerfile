# builds a debian wheezy image with php5.3
FROM debian:wheezy
ADD /php5.3.apt-preferences /etc/apt/preferences.d/preferences
ENV INSTALL_PACKAGES php5-cli php-apc php5-curl php5-gd php5-intl php5-json php5-mcrypt
RUN echo "deb http://ftp.debian.org/debian/ squeeze main contrib non-free" >> /etc/apt/sources.list
RUN echo "deb http://security.debian.org/ squeeze/updates main contrib non-free" >> /etc/apt/sources.list
RUN \
    apt-get -y update && \
    apt-get -y --force-yes install $INSTALL_PACKAGES && \
    apt-get -y autoremove && \
    apt-get -y clean && \
    rm -rf /var/lib/apt/lists/*
