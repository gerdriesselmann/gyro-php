#!/bin/sh
# Call: makedirs-sh application-dir gyro-dir  
mkdir -p $1
cd $1
mkdir -p app
mkdir -p app/behaviour
mkdir -p app/behaviour/base
mkdir -p app/behaviour/commands
mkdir -p app/controller
mkdir -p app/controller/base
mkdir -p app/controller/tools
mkdir -p app/lib
mkdir -p app/lib/components
mkdir -p app/lib/helpers
mkdir -p app/lib/interfaces
mkdir -p app/model
mkdir -p app/model/base
mkdir -p app/model/classes
mkdir -p app/view
mkdir -p app/view/base
mkdir -p app/view/widgets
mkdir -p app/view/translations
mkdir -p app/view/templates
mkdir -p app/view/templates/default
mkdir -p app/www
mkdir -p app/www/js
mkdir -p app/www/css
mkdir -p app/www/images
mkdir -p data
mkdir -p tmp
mkdir -p tmp/log
mkdir -p tmp/view
mkdir -p tmp/view/templates_c
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

