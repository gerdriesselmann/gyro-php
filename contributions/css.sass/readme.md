SASS CSS Module
===============

Support for the SASS CSS Precompiler (http://sass-lang.com/).

Prerequisites
-------------

You need to install the SASS command line interface. See http://sass-lang.com/install. Since SASS uses Ruby and
Gem, the GenRuby package manager, these must be installed, too,  

On both Debian and Ubuntu it is normally enough to just do 

    sudo apt-get install rubygems
    sudo gem install sass


Usage
-----

On system update, the SASS module compiles all \*.sass or \*.scss files it finds in the directory 
/view/sass within your application's base directory.

Modules providing SASS files should copy them on install to /view/sass/{module name}.
 
The default output directory is /www/css/generated. So for example the file /view/sass/main.sass will become 
/www/css/generated/main.css 

You may change the output directory through the Config, though, by setting the APP_SASS_OUTPUT_DIR constant.

For direct usage just call the approbiate function on PostCSS class.
 
    Load::components('sass');
    SASS::compile('main.css');

Sass compiling is also invoked on system update. If you are using the generated CSS files with JCSSManager, ensure
the Sass module is enabled before the JCSSManager module. Since later is often a dependency of other modules,
it's best practice to include Sass as first module.

