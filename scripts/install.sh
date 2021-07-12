#!/bin/bash

currentDir=$(pwd)
scriptDir="$(cd "$(dirname "${BASH_SOURCE[0]}")" > /dev/null && pwd)"

cd "$scriptDir/../.." || exit 1

cp core/install/.gitignore .gitignore
cp -r core/install/public ../public
cp -r core/install/cli ../cli
cp -r core/install/config config
cp -r core/install/protected/controllers controllers
cp -r core/install/protected/models models
cp -r core/install/protected/hooks hooks
cp -r core/install/protected/res res
cp -r core/install/protected/routers routers
cp -r core/install/protected/tests tests
cp -r core/install/protected/app.init.php app.init.php
cp -r core/install/protected/api.init.php api.init.php
cp -r core/install/protected/cli.init.php cli.init.php

mkdir res/logs
chmod -R 775 res/logs

mkdir res/captcha
chmod -R 775 res/captcha

mkdir res/captcha/img
chmod -R 775 res/captcha/img

cd core || exit 1

git submodule update --init --recursive

cd ../../public

mkdir media
chmod -R 775 media

mkdir media/img
chmod -R 775 media/img

cd media

ln -s ../../protected/res/captcha/img captcha

cd "$scriptDir" || exit 1

./test.sh

cd "$currentDir" || exit 1

exit 1

