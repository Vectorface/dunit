# builds a debian jessie image with hhvm-nightly
FROM debian:jessie
ADD http://dl.hhvm.com/conf/hhvm.gpg.key /hhvm.gpg.key
ADD /hhvm.list /etc/apt/sources.list.d/hhvm.list
ENV INSTALL_PACKAGES hhvm-nightly
RUN \
	apt-get -y update && \
	apt-get -y upgrade && \
    apt-key add /hhvm.gpg.key && rm /hhvm.gpg.key && \
    apt-get -y update && \
    apt-get -y install $INSTALL_PACKAGES && \
    apt-get -y autoremove --purge && \
    apt-get -y clean && \
    rm -rf /var/lib/apt/lists/*
