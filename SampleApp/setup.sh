#!/bin/sh

sudo chown -R rd:www-data ./Filedb/*

sudo find ./Filedb \( -type d -exec chmod 777 {} \; \) -o \( -type f -exec chmod 666 {} \; \)
