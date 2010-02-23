# Convert Python transliteration tables to PHP
# (c) 2010 Gerd Riesselmann 

rm -rf unidecode_php
mkdir unidecode_php
cp $1/unidecode/x*.py unidecode_php/
cd unidecode_php


# rename .py to .php
rename "s/py/php/" *.py

# repalce "data = (" with "$data = array("
find . -name "*.php" -print | xargs sed -i 's/data\ = (/<?php $data\ =\ array(/g'

# To use \x and such, PHP requires double quote, while Pyhthon code has single quote
# " => \x22
find . -name "*.php" -print | xargs sed -i 's/"/\\x22/g'
# ^' => "
find . -name "*.php" -print | xargs sed -i "s/^'/\"/g"
# ', => ",
find . -name "*.php" -print | xargs sed -i "s/',/\",/g"
# \' => '
find . -name "*.php" -print | xargs sed -i "s/\\\\'/'/g"

#find . -name "*.php" -print | xargs sed -i "s/'\\x[A-Za-z0-9][A-Za-z0-9]'/''/g"
 

for i in ./*.php; do 
  echo ';' >> $i 
done
