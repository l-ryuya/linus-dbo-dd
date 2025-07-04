#!/bin/bash

while true; do
    cd /app && /opt/bitnami/php/bin/php artisan schedule:run --no-ansi >> /dev/null 2>&1
    sleep 60
done
