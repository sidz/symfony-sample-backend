#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
  set -- php-fpm "$@"
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'bin/console' ]; then
  mkdir -p var/cache var/log config/jwt
  chmod 777 -R var/log

  if [ "$APP_ENV" != 'prod' ]; then
    composer install --prefer-dist --no-progress --no-suggest --no-interaction

    bin/console doctrine:database:create

    chown www-data:www-data ./var/app.db

    bin/console doctrine:schema:update --force
  fi
fi

exec docker-php-entrypoint "$@"
