#!/bin/sh

sudo chown -R rd:www-data ./SampleApp/Filedb/*
sudo chmod -R 0770 ./SampleApp/Filedb/

sudo find ./SampleApp/Filedb \( -type d -exec chmod 770 {} \; \) -o \( -type f -exec chmod 660 {} \; \)
