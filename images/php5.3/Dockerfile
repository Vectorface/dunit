# builds a debian wheezy image with php5.3
FROM vectorface/php-base
ADD /php5.3.apt-preferences /etc/apt/preferences.d/preferences
ENV INSTALL_PACKAGES php5-cli php-apc php5-curl php5-gd php5-intl php5-json php5-mcrypt
RUN cp /etc/apt/sources.list /etc/apt/sources.list.backup
RUN echo "deb http://ftp.debian.org/debian/ squeeze main contrib non-free" >> /etc/apt/sources.list
RUN echo "deb http://security.debian.org/ squeeze/updates main contrib non-free" >> /etc/apt/sources.list
RUN \
    apt-get -y update && \
    apt-get -y --force-yes install $INSTALL_PACKAGES && \
    apt-get -y autoremove && \
    apt-get -y clean && \
    rm -rf /var/lib/apt/lists/* /etc/php5/mods-available
RUN mv /etc/apt/sources.list.backup /etc/apt/sources.list
RUN rm /etc/apt/preferences.d/preferences
