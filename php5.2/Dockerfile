# builds a debian wheezy image with php5.3
FROM debian:squeeze
ADD /php5.2.apt-preferences /etc/apt/preferences.d/preferences
ENV INSTALL_PACKAGES php5-cli php-apc php5-curl php5-gd php5-json php5-mcrypt
RUN echo "deb http://archive.debian.org/debian lenny main contrib" >> /etc/apt/sources.list
RUN apt-get -y update && apt-get -y install $INSTALL_PACKAGES && apt-get -y autoremove && apt-get -y clean && rm -rf /var/lib/apt/lists/*