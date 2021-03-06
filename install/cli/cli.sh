#!/bin/bash

currDir=$(pwd)
scriptDir="$(cd "$(dirname "${BASH_SOURCE[0]}")" >/dev/null && pwd)"

cd "$scriptDir" || exit

help='Usage:
    cli.sh [options]
Options:
    -c    Controller Name
    -a    Action Name
    -p    Optional Params
    -h    Help Message'

while getopts ":c:a:p:h" optName
do
    case "$optName" in
        c) controller="$OPTARG";;

        a) action="$OPTARG";;

        p) params="$OPTARG";;

        h) echo "$help";
           exit;
           break;;

        :) echo "Missing argument for -$OPTARG";
           exit;
           break;;

        *) echo 'Invalid option. Try `cli.sh -h` for more information';
           exit;
           break;;
    esac
done

if [ -z ${controller+x} ]; then
    echo 'Controller Name Is Not Set. Try `cli.sh -h` for more information';
    exit;
fi

if [ -z ${action+x} ]; then
    echo 'Action Name Is Not Set. Try `cli.sh -h` for more information';
    exit;
fi


if [ -z ${params+x} ]; then
    php cli.php --controller="$controller" --action="$action";
else
    php cli.php --controller="$controller" --action="$action" --params="$params";
fi

cd "$currDir" || exit

exit
