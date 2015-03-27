#!/bin/bash
docker pull debian:squeeze
docker pull debian:wheezy

# php 5.2
docker build -t vectorface/php5.2 ./php5.2
docker save `docker history -q vectorface/php5.2 | head -n 1` | sudo TMPDIR=/var/run/shm docker-squash -t vectorface/php5.2 -from `docker history -q debian:squeeze | head -n 1` | docker load
docker push vectorface/php5.2

# php-base
docker build -t vectorface/php-base ./php-base
docker save `docker history -q vectorface/php-base | head -n 1` | sudo TMPDIR=/var/run/shm docker-squash -t vectorface/php-base -from `docker history -q debian:wheezy | head -n 1` | docker load
docker push vectorface/php-base

# hhvm-base
docker build -t vectorface/hhvm-base ./hhvm-base
docker save `docker history -q vectorface/hhvm-base | head -n 1` | sudo TMPDIR=/var/run/shm docker-squash -t vectorface/hhvm-base -from `docker history -q debian:wheezy | head -n 1` | docker load
docker push vectorface/hhvm-base

# php 5.3, 5.4, 5,5, 5.6, nightly
for version in php5.3 php5.4 php5.5 php5.6 php-nightly
    do
        docker build -t vectorface/$version ./$version
        # php 5.3 cannot be squashed right now
        if [ $version != "php5.3" ]; then
            docker save `docker history -q vectorface/$version | head -n 1` | sudo TMPDIR=/var/run/shm docker-squash -t vectorface/$version -from `docker history -q vectorface/php-base | head -n 1` | docker load
        fi
        docker push vectorface/$version
done

# hhvm and hhvm-nightly
for version in hhvm hhvm-nightly
    do
        docker build -t vectorface/$version ./$version
        docker save `docker history -q vectorface/$version | head -n 1` | sudo TMPDIR=/var/run/shm docker-squash -t vectorface/$version -from `docker history -q vectorface/hhvm-base | head -n 1` | docker load
        docker push vectorface/$version
done
