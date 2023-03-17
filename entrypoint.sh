#!/bin/ash
set -o errexit
set -o nounset

DIR="/api_emulator"

echo "Starting dev server"
php \
  -S "0.0.0.0:$PORT" \
  -t "$DIR/htdocs" \
  "$DIR/htdocs/index.php"
