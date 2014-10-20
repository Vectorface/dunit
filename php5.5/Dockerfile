# builds a debian wheezy image with php5.3
FROM debian:wheezy
ADD http://www.dotdeb.org/dotdeb.gpg /dotdeb.gpg
RUN echo "deb http://packages.dotdeb.org wheezy all" >> /etc/apt/sources.list
RUN echo "deb http://packages.dotdeb.org wheezy-php55 all" >> /etc/apt/sources.list
RUN apt-key add /dotdeb.gpg && apt-get -y update && apt-get -y install php5-cli && apt-get -y autoremove && apt-get -y clean
RUN rm -rf /var/lib/apt/lists/* /dotdeb.gpg