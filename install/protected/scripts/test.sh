#!/bin/bash

export PHAN_ALLOW_XDEBUG=1
export PHAN_DISABLE_XDEBUG_WARN=1

currDir=$(pwd)
scriptDir="$(cd "$(dirname "${BASH_SOURCE[0]}")" > /dev/null && pwd)"

cd "$scriptDir/../tests" || exit 1

php core/tools/phan.phar
phpunit

cd "../core/scripts" || exit 1

./test.sh

cd "$currDir" || exit 1

exit 1

