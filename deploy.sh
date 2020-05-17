#!/usr/bin/env bash

set -e

cd ..
if [[ -f shopping.tar.gz ]]; then
  rm shopping.tar.gz
fi
tar -czf shopping.tar.gz shopping
echo "Copying"
scp shopping.tar.gz auri.local:/srv
echo "Securing Data"
ssh auri.local mv /srv/shopping/data/database.sqlite /srv
echo "Deleting old repo"
ssh auri.local rm -rf /srv/shopping
echo "Releasing new data"
ssh auri.local tar -xzf /srv/shopping.tar.gz -C /srv
echo "Copying old data back"
ssh auri.local mv /srv/database.sqlite /srv/shopping/data
echo "Setting permissions on folder"
ssh auri.local chmod -R 777 /srv/shopping/data