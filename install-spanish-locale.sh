#!/usr/bin/env bash

echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && \
echo "es_ES.UTF-8 UTF-8" >> /etc/locale.gen && \
ln -sfn /etc/locale.alias /usr/share/locale/locale.alias && \
locale-gen