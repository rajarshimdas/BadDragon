#!/bin/sh

sudo chown -R rd:rd .
sudo chown -R rd:www-data ./SampleApp/Filedb/*

sudo find ./SampleApp/Filedb \( -type d -exec chmod 775 {} \; \) -o \( -type f -exec chmod 664 {} \; \)

sudo chmod +x ./*.sh
