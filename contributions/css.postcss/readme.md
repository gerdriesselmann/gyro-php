PostCSS Module
==============

Invokes the PostCSS command line.

Prerequisites
-------------

Install PostCSS (https://github.com/postcss/postcss), the PostCSS CLI (https://github.com/code42day/postcss-cli) and 
all the PostCSS extension you like. PostCSS runs on node.js, so it should also be available on your system, 
and so should be the npm packet manager. 

On both Debian and Ubuntu it is normally enough to just do 

    sudo apt-get install node npm
    sudo npm install postcss-cli
    sudo npm install {postcss extension}


Usage
-----

For direct usage just add and configure the wanted extensions, than process either content, file, or directory by
calling the approbiate function on PostCSS class.
 
    PostCSS::extension('autoprefixer', array('browsers' => '> 5%'));
    $err = PostCSS::process_file('in.css', 'out.css');

However: usually PostCSS is part of a build chain on system update. So only configuration needs to be done in your 
application's start.inc.php:

    PostCSS::extension('autoprefixer', array('browsers' => '> 5%'));


