# builds a debian squeeze image with php5.2 from lenny
# this is probably full of security issues by now, and shouldn't be used.
FROM debian:squeeze
ADD /php5.2.apt-preferences /etc/apt/preferences.d/preferences
ADD /lenny.list /etc/apt/sources.list.d/lenny.list
ENV INSTALL_PACKAGES php5-cli php-apc php5-curl php5-gd php5-json php5-mcrypt
RUN \
    apt-get -y update && \
    apt-get -y install $INSTALL_PACKAGES && \
    apt-get -y autoremove && \
    apt-get -y clean && \
    rm -rf /var/lib/apt/lists/*

