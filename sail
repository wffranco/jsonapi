#!/usr/bin/env bash

cd "$(dirname "${BASH_SOURCE[0]}")"

if [ ! -e ./vendor ]; then
  docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
fi

if [ ! -f .env ]; then
  cp .env.example .env
  ./sail artisan key:generate
fi

./vendor/bin/sail $@
