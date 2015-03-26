# builds a debian wheezy image with php5.5
FROM vectorface/php-base
ADD http://www.dotdeb.org/dotdeb.gpg /dotdeb.gpg
ENV INSTALL_PACKAGES php5-cli php5-apcu php5-curl php5-gd php5-intl php5-json php5-mcrypt
RUN cp /etc/apt/sources.list /etc/apt/sources.list.backup
RUN cp /etc/apt/trusted.gpg /etc/apt/trusted.gpg.backup
RUN echo "deb http://packages.dotdeb.org wheezy all" >> /etc/apt/sources.list
RUN echo "deb http://packages.dotdeb.org wheezy-php55 all" >> /etc/apt/sources.list
RUN \
    apt-key add /dotdeb.gpg && \
    apt-get -y update && \
    apt-get -y install $INSTALL_PACKAGES && \
    apt-get -y autoremove && \
    apt-get -y clean && \
    rm -rf /var/lib/apt/lists/* /dotdeb.gpg
RUN mv /etc/apt/sources.list.backup /etc/apt/sources.list
RUN rm -rf /etc/apt/trusted.gpg.d/*
RUN mv /etc/apt/trusted.gpg.backup /etc/apt/trusted.gpg
