#!/bin/bash

set -e

for version in php5.2 php5.3 php5.4 php5.5 php5.6 php7.0 php-nightly hhvm hhvm-nightly; do
	docker build -t vectorface/${version} ./${version}
	#docker push vectorface/${version}
done
