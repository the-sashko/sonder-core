#!/bin/bash
export PHAN_ALLOW_XDEBUG=1
export PHAN_DISABLE_XDEBUG_WARN=1

currentDir=$(pwd)
scriptDir="$(cd "$(dirname "${BASH_SOURCE[0]}")" > /dev/null && pwd)"

cd "$scriptDir/../tests" || exit 1

php tools/phan.phar
phpunit

cd ../plugins/captcha/tests || exit 1

phpunit
php ../../../tests/tools/phan.phar

cd "$currentDir" || exit 1

exit 1

