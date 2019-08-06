#!/bin/bash

currDir=$(pwd)
scriptDir="$(cd "$(dirname "${BASH_SOURCE[0]}")" >/dev/null && pwd)"

cd "$scriptDir/../.."

cp core/examples/.gitignore .gitignore
cp -r core/examples/public ../public
cp -r core/examples/protected/config config
cp -r core/examples/protected/controllers controllers
cp -r core/examples/protected/models models
cp -r core/examples/protected/hooks hooks
cp -r core/examples/protected/res res
cp -r core/examples/protected/routers routers
cp -r core/examples/protected/init.php init.php
cp -r core/examples/protected/api.init.php api.init.php
cp -r core/examples/protected/deamon.init.php deamon.init.php

mkdir res/logs

chmod -R 775 res/logs

cd core

git submodule update --init --recursive

cd "$currDir"

exit
