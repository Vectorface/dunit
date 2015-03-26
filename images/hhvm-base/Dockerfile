# builds a debian wheezy image with dependencies for hhvm preinstalled
FROM debian:wheezy
ADD http://dl.hhvm.com/conf/hhvm.gpg.key /hhvm.gpg.key
ENV INSTALL_PACKAGES hhvm
RUN cp /etc/apt/trusted.gpg /etc/apt/trusted.gpg.backup
RUN cp /etc/apt/sources.list /etc/apt/sources.list.backup
RUN echo "deb http://dl.hhvm.com/debian wheezy main" > /etc/apt/sources.list.d/hhvm.list
RUN \
    apt-key add /hhvm.gpg.key && \
    apt-get -y update && \
    apt-get -y install $INSTALL_PACKAGES && \
    apt-get -y remove $INSTALL_PACKAGES && \
    apt-get -y clean && \
    rm -rf /var/lib/apt/lists/* /hhvm.gpg.key /etc/apt/sources.list.d/hhvm.list
RUN rm -rf /etc/apt/trusted.gpg.d/*
RUN mv /etc/apt/trusted.gpg.backup /etc/apt/trusted.gpg
RUN mv /etc/apt/sources.list.backup /etc/apt/sources.list
