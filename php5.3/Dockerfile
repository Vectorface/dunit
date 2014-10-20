# builds a debian wheezy image with php5.3
FROM debian:wheezy
ADD /php5.3.apt-preferences /etc/apt/preferences.d/preferences
RUN echo "deb http://ftp.debian.org/debian/ squeeze main contrib non-free" >> /etc/apt/sources.list
RUN echo "deb http://security.debian.org/ squeeze/updates main contrib non-free" >> /etc/apt/sources.list
RUN apt-get -y update && apt-get -y install php5-cli && apt-get -y autoremove && apt-get -y clean
RUN rm -rf /var/lib/apt/lists/*