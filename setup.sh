#!/bin/sh

sudo chown -R rd:www-data ./SampleApp/Filedb/*

sudo find ./SampleApp/Filedb \( -type d -exec chmod 777 {} \; \) -o \( -type f -exec chmod 666 {} \; \)
