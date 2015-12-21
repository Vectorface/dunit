# builds a debian wheezy image with php5.5 from dotdeb
FROM debian:wheezy
ADD http://www.dotdeb.org/dotdeb.gpg /dotdeb.gpg
ADD /dotdeb-php55.list /etc/apt/sources.list.d/dotdeb-php55.list
ENV INSTALL_PACKAGES php5-cli php5-apcu php5-curl php5-gd php5-intl php5-json php5-mcrypt
RUN \
    apt-get -y update && \
	apt-get -y upgrade && \
    apt-key add /dotdeb.gpg && rm /dotdeb.gpg && \
    apt-get -y update && \
    apt-get -y install $INSTALL_PACKAGES && \
    apt-get -y autoremove && \
    apt-get -y clean && \
    rm -rf /var/lib/apt/lists/*
