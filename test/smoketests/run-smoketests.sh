#!/bin/bash
set -o nounset
set -o errexit

container_name=api_emulator
test_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo "Running smoketests for docker image $DOCKER_IMAGE"

function shutdown {
  echo ""
  echo "-------------------"
  echo "Container logs"
  docker logs $container_name || true

  echo ""
  echo "-------------------"
  echo "Cleaning up container"
  docker kill $container_name || true
}

trap shutdown EXIT

echo "Starting $container_name with default handlers and options"
docker run \
  --rm \
  -d \
  --name $container_name \
  -p 9832:80 \
  "$DOCKER_IMAGE"

sleep 5
docker logs -f $container_name &

echo "Running smoketests"
health_output="$(curl --verbose http://127.0.0.1:9832/_emulator-meta/health)"

echo "Got response: '$health_output'"

if [ "$health_output" != "Emulator healthy" ] ; then
  echo "Unexpected HTTP response"
  exit 1
else
  echo "Test passed"
fi
