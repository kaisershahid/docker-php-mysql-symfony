#!/bin/bash

IMAGES=docker

print_usage() {
  echo '******'
  echo "usage: ./build_image.sh type"
  echo
  echo "- type: http, db"
  echo
}

build_http() {
  echo "** building apache-php7 image"
  rm -rf $IMAGES/.build
  mkdir $IMAGES/.build
  cp -Rv $IMAGES/apache-php7/* $IMAGES/.build
  cp ../composer.json $IMAGES/.build
  docker build --tag=YOUR_APP_apache -f $IMAGES/apache-php7/Dockerfile $IMAGES/.build
}

build_db() {
  echo "** building mysql image"
  docker build --tag=YOUR_APP_mysql -f $IMAGES/mysql/Dockerfile $IMAGES/mysql
}

case $1 in
  'http')
    build_http
    ;;
  'db')
    build_db
    ;;
  '-h')
    print_usage
    ;;
  '')
    print_usage
    ;;
esac
