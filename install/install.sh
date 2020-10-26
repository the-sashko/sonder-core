#!/bin/bash

currDir=$(pwd)
scriptDir="$(cd "$(dirname "${BASH_SOURCE[0]}")" >/dev/null && pwd)"

cd "$scriptDir/../.." || exit 1

cp core/install/samples/.gitignore .gitignore
cp -r core/install/samples/public ../public
cp -r core/install/samples/cli ../cli
cp -r core/install/samples/config config
cp -r core/install/samples/protected/controllers controllers
cp -r core/install/samples/protected/models models
cp -r core/install/samples/protected/hooks hooks
cp -r core/install/samples/protected/res res
cp -r core/install/samples/protected/routers routers
cp -r core/install/samples/protected/tests tests
cp -r core/install/samples/protected/init.php init.php
cp -r core/install/samples/protected/api.init.php api.init.php
cp -r core/install/samples/protected/cli.init.php cli.init.php

mkdir res/logs

chmod -R 775 res/logs

cd core || exit 1

git submodule update --init --recursive

cd "$currDir" || exit 1

exit 1
