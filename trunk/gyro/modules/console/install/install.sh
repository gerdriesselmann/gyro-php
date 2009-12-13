#!/bin/sh
# Call: install.sh application-dir
dirname=`dirname "$0"`

# Copy files
cp $dirname/run_console.php.example $1/app/run_console.php
