#!/usr/bin/env bash

if [ $# -eq 0 ]
    then
        echo "[Error] Wrong parameters. Please, set environment [prod, dev]"
        exit
fi

DIR=${APP_PROJECT_DIR}/bin
PUBLIC_DIR=web
CMD_CONSOLE=$DIR/console

if [ $1 == "dev" ]
    then
        echo "---- Health check of build ----" \
        && $CMD_CONSOLE app:health-check \
        && echo "---- Executing of Development commands ----" \
        && echo "===> Clear old project cache" \
        && $CMD_CONSOLE cache:clear --no-warmup \
        && echo "===> Install project assets" \
        && $CMD_CONSOLE assets:install --symlink \
        && echo "===> Warm up project cache" \
        && $CMD_CONSOLE cache:warmup
elif [ $1 == "prod" ]
    then
        echo "---- Health check of build ----" \
        && $CMD_CONSOLE app:health-check -e prod \
        && echo "---- Executing of Production commands ----" \
        && echo "===> Clear old project cache" \
        && $CMD_CONSOLE cache:clear --no-warmup -e prod --no-debug \
        && echo "===> Install project assets" \
        && $CMD_CONSOLE assets:install -e prod --no-debug \
        && echo "===> Warm up project cache" \
        && $CMD_CONSOLE cache:warmup -e prod --no-debug
fi

if [ $? -ne 0 ]
    then
        echo "[Error] Some commands was executed with a problem."
        exit 1
else
    echo "[Success] All commands were successfully executed."
fi
