#!/bin/sh

cp scripts/install_additions.sh site

sleep 1 
docker compose up -d
docker exec apache_frontend sh /var/www/html/install_additions.sh
rm -rf site/install_additions.sh 

docker compose restart


