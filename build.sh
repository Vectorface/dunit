#!/bin/bash
box build
if [ $? -eq 0 ]; then
    mv bin/dunit.phar bin/dunit
    chmod +x bin/dunit
    echo 'Done.'
fi
