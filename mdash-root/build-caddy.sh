#!/bin/bash

go=$(which go)

export PATH="$PATH:$go"

modules=""

while [[ $# -gt 0 ]]; do
  module="$1"
  modules+=" --with $module"
  shift
done

cd /mdash/

xcaddy build $modules > build-caddy.log 2>&1
exit_code=$?

sudo dpkg-divert --divert /usr/bin/caddy.default --rename /usr/bin/caddy >> build-caddy.log 2>&1
sudo mv ./caddy /usr/bin/caddy.custom >> build-caddy.log 2>&1
sudo update-alternatives --install /usr/bin/caddy caddy /usr/bin/caddy.default 10 >> build-caddy.log 2>&1
sudo update-alternatives --install /usr/bin/caddy caddy /usr/bin/caddy.custom 50 >> build-caddy.log 2>&1
sudo systemctl restart caddy >> build-caddy.log 2>&1

echo "mDash: Success!" >> build-caddy.log 2>&1

exit $exit_code