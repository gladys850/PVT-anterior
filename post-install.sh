#!/usr/bin/env bash

bash ./yarn-compiler.sh
bash ./composer-clear.sh

# Update notification
if type "notify-send" &> /dev/null; then
  notify-send 'PVA RRHH MODULE' 'Updated'
fi

echo -e "\n\e[92mUpdated successfully\n"
