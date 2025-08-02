#!/bin/bash
set -ex

# Source docker-entrypoint.sh:
# https://github.com/docker-library/postgres/blob/master/9.4/docker-entrypoint.sh
# https://github.com/kovalyshyn/docker-freeswitch/blob/vanilla/docker-entrypoint.sh

if [ "$1" = 'freeswitch' ]; then

    echo "ENTRYPOINT ARGS: $@"
    ls -la /etc/freeswitch
    mkdir -p /etc/freeswitch

    # Копируем базовые конфиги из контейнера в /etc/freeswitch, если их там нет
    if [ ! -f "/etc/freeswitch/freeswitch.xml" ]; then
        cp -r /usr/local/freeswitch/conf/* /etc/freeswitch/
    fi

    # Копируем поверх локальные (монтируемые) конфиги из /docker-conf, если есть
    if [ -d /docker-conf ]; then
        cp -r /docker-conf/* /etc/freeswitch/
    fi

    # Ensure runtime dirs exist
    mkdir -p /var/run/freeswitch /var/lib/freeswitch
    chown -R freeswitch:freeswitch /etc/freeswitch /var/run/freeswitch /var/lib/freeswitch

   
    if [ -d /docker-entrypoint.d ]; then
        for f in /docker-entrypoint.d/*.sh; do
            [ -f "$f" ] && . "$f"
        done
    fi
    
    exec gosu freeswitch /usr/bin/freeswitch -u freeswitch -g freeswitch -nonat -c
fi

exec "$@"