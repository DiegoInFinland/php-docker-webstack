#!/bin/sh
##
# This script is a slightly modified version of composer install script found in the oficial composer website:
# https://getcomposer.org/doc/faqs/how-to-install-composer-programmatically.md
#
# It installs composer, then it updates the container image and installs dependencies needed.
#
# composer require "twig/twig:^3.0"
# composer require php-units-of-measure/php-units-of-measure
#
##

EXPECTED_CHECKSUM="$(php -r 'copy("https://composer.github.io/installer.sig", "php://stdout");')"
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"

if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]
then
    >&2 echo 'ERROR: Invalid installer checksum'
    rm composer-setup.php
    exit 1
fi

php composer-setup.php --quiet
RESULT=$?
rm composer-setup.php

mv composer.phar /usr/local/bin/composer
apt update && apt install zip vim -y -qq

mkdir -p ./public/gallery ./public/docs 

sleep 1 
docker-php-ext-install pdo pdo_mysql

composer install 

# Sometimes apache needs a2enmod. Default disabled.  
#a2enmod rewrite

exit $RESULT
