#!/usr/bin/env bash

rm -f /etc/localtime
ln -s /usr/share/zoneinfo/America/La_Paz /etc/localtime
echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && \
echo "es_ES.UTF-8 UTF-8" >> /etc/locale.gen && \
ln -sfn /etc/locale.alias /usr/share/locale/locale.alias && \
locale-gen