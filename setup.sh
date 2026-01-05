#!/bin/sh

sudo chown -R rd:www-data .
sudo find . \( -type d -exec chmod 755 {} \; \) -o \( -type f -exec chmod 644 {} \; \)

sudo chown -R rd:www-data ./SampleApp/Filedb/*

sudo find ./SampleApp/Filedb \( -type d -exec chmod 775 {} \; \) -o \( -type f -exec chmod 664 {} \; \)

sudo chmod +x ./*.sh
