#!/bin/bash

currDir=$(pwd)
scriptDir="$(cd "$(dirname "${BASH_SOURCE[0]}")" >/dev/null && pwd)"

cd "$scriptDir/.."

cp -r examples/public ../../public
cp -r examples/config config
cp -r examples/controllers controllers
cp -r examples/models models
cp -r examples/res res
cp -r examples/routers routers
cp -r examples/init.php init.php
cp -r examples/api.init.php api.init.php

cd "$currDir"

exit