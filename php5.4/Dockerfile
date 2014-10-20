# builds a debian wheezy image with php5.3
FROM debian:wheezy
RUN apt-get -y update && apt-get -y install php5-cli && apt-get -y autoremove && apt-get -y clean
RUN rm -rf /var/lib/apt/lists/*