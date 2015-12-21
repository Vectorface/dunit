# builds a debian wheezy image with php5.3 from squeeze
FROM debian:wheezy
ADD /php5.3.apt-preferences /etc/apt/preferences.d/preferences
ADD /squeeze.list /etc/apt/sources.list.d/squeeze.list
ENV INSTALL_PACKAGES php5-cli php-apc php5-curl php5-gd php5-intl php5-json php5-mcrypt
RUN \
    apt-get -y update && \
    apt-get -y --force-yes install $INSTALL_PACKAGES && \
    apt-get -y autoremove && \
    apt-get -y clean && \
    rm -rf /var/lib/apt/lists/* /etc/php5/mods-available
