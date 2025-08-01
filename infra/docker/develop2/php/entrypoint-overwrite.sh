#!/bin/bash
set -o errexit
set -o nounset
set -o pipefail

/usr/bin/supervisord -c /etc/supervisor/supervisord.conf

sleep 3
echo "==== Process Check ===="
ps -ef | grep supervisord | grep -v grep
echo "======================="

echo "==== Supervisorctl Process Check ===="
supervisorctl status
echo "======================="

exec php-fpm
