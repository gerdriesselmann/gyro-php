#!/bin/sh
# Call: makedirs-sh application-dir gyro-dir  
mkdir -p $1
cd $1
mkdir app
mkdir app/behaviour
mkdir app/behaviour/base
mkdir app/behaviour/commands
mkdir app/controller
mkdir app/controller/base
mkdir app/controller/tools
mkdir app/lib
mkdir app/lib/components
mkdir app/lib/helpers
mkdir app/lib/interfaces
mkdir app/model
mkdir app/model/base
mkdir app/model/classes
mkdir app/view
mkdir app/view/base
mkdir app/view/widgets
mkdir app/view/translations
mkdir app/view/templates
mkdir app/view/templates/default
mkdir app/www
mkdir app/www/js
mkdir app/www/css
mkdir app/www/images
mkdir data
mkdir tmp
mkdir tmp/log
mkdir tmp/view
mkdir tmp/view/templates_c
chmod -R 777 tmp
# Copy files
cp $2/install/config.php.example app/config.php.example
cp $2/install/constants.php.example app/constants.php
cp $2/install/modules.php.example app/modules.php
cp $2/install/htaccess.example app/www/.htaccess
cp $2/install/index.php.example app/www/index.php
cp $2/install/robots.txt.example app/www/robots.txt
cp $2/core/view/templates/default/page.tpl.php app/view/templates/default/
# Modifiy copied files
echo "\ndefine ('APP_GYRO_PATH', '$2/');" >> app/config.php.example

