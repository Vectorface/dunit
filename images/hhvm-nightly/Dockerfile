# builds a debian wheezy image with hhvm
FROM debian:wheezy

ADD http://dl.hhvm.com/conf/hhvm.gpg.key /hhvm.gpg.key
ENV INSTALL_PACKAGES hhvm-nightly
RUN echo "deb http://dl.hhvm.com/debian wheezy main" > /etc/apt/sources.list.d/hhvm.list
RUN apt-key add /hhvm.gpg.key && apt-get -y update && apt-get -y install $INSTALL_PACKAGES && apt-get -y autoremove && apt-get -y clean && rm -rf /var/lib/apt/lists/* /hhvm.gpg.key
