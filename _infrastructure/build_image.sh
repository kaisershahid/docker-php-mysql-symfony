#!/bin/bash

IMAGES=docker

print_usage() {
  echo '******'
  echo "usage:"
  echo "- ./build_image.sh http [version]"
  echo "- ./build_image.sh db"
  echo
}

build_http() {
  ver='7.3'
  case $1 in
    7.[1-3])
      set ver="$1"
      ;;
  esac

  echo "** building apache-php($ver) image"

  rm -rf $IMAGES/.build
  mkdir $IMAGES/.build
  cp -Rv $IMAGES/apache-php${ver}/* $IMAGES/.build
  cp ../composer.json $IMAGES/.build

  docker build --tag=YOUR_APP_apache -f $IMAGES/apache-php${ver}/Dockerfile $IMAGES/.build

  rm -rf $IMAGES/.build
}

build_db() {
  echo "** building mysql image"
  docker build --tag=YOUR_APP_mysql -f $IMAGES/mysql/Dockerfile $IMAGES/mysql
}

case $1 in
  'http')
    shift
    build_http $1
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
