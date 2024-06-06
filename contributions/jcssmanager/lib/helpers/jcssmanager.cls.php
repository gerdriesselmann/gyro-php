<?php
/**
 * Helepr class for JCSSMAnager
 */
class JCSSManager {
	const TYPE_JS = 'JS';
	const TYPE_CSS = 'CSS';
	const TYPE_CSS_IE50 = 'CSS_IE50';
	const TYPE_CSS_IE55 = 'CSS_IE55';
	const TYPE_CSS_IE6 = 'CSS_IE6';
	const TYPE_CSS_IE7 = 'CSS_IE7';	

	/**
	 * Returns all possible types
	 * 
	 * @return array
	 */
	public static function get_types() {
		return array_merge(
			array(self::TYPE_JS => tr(self::TYPE_JS, 'jcssmanager')),
			self::get_css_types()
		);
	}
	
	/**
	 * Returns all possible CSS types
	 * 
	 * @return array
	 */
	public static function get_css_types() {
		return array(
			self::TYPE_CSS => tr(self::TYPE_CSS, 'jcssmanager'),
			self::TYPE_CSS_IE50 => tr(self::TYPE_CSS_IE50, 'jcssmanager'),
			self::TYPE_CSS_IE55 => tr(self::TYPE_CSS_IE55, 'jcssmanager'),
			self::TYPE_CSS_IE6 => tr(self::TYPE_CSS_IE6, 'jcssmanager'),
			self::TYPE_CSS_IE7 => tr(self::TYPE_CSS_IE7, 'jcssmanager'),
		);		
	}

	public static function root_dir() {
		return Config::get_value(Config::URL_ABSPATH);
	}
	
	/**
	 * Make path relativ to web root 
	 * 
	 * @param string $path
	 * @return string
	 */
	public static function make_relativ($path) {
		return str_replace(self::root_dir(), '', $path);
	}
	
	/**
	 * Make path relativ to web root an absolute one 
	 * 
	 * @param string $path
	 * @return string
	 */
	public static function make_absolute($path) {
		return self::root_dir() . $path;
	}

	public static function make_absolute_with_base($path, $base) {
		if (substr($path, 0, 1) === '/') {
			return $path; // Is absolute
		} else if (strpos($path, '://') !== false) {
			return $path; // Some kind of URL
		} else {
			return rtrim($base, '/') . '/' . $path;
		}
	}
	
	/**
	 * Collect and compres all JS and CSS
	 * 
	 * @return Status
	 */
	public static function collect_and_compress() {
		$err = new Status();
		Load::commands('jcssmanager/compress.js', 'jcssmanager/compress.css');
		Load::models('jcsscompressedfiles');
		
		$out_file = Config::get_value(Config::URL_ABSPATH) . Config::get_value(ConfigJCSSManager::JS_DIR) . 'compressed.js';
		$in_files = self::collect_for_compressing(JCSSManager::TYPE_JS);
		$js = new JCSSManagerCompressJSCommand($in_files, $out_file);
		$err->merge($js->execute());
		
		$css_base = Config::get_value(ConfigJCSSManager::CSS_DIR);
		foreach(JCSSManager::get_css_types() as $type => $tr) {
			$out_file = ($type !== JCSSManager::TYPE_CSS) ? 'compressed.' . strtolower($type) . '.css' : 'compressed.css';
			$out_file = JCSSManager::make_absolute($css_base . $out_file);
			$in_files = self::collect_for_compressing($type);
			$css = new JCSSManagerCompressCSSCommand($in_files, $out_file, $type);
			$err->merge($css->execute());
		}		

		// Update htaccess
		if ($err->is_ok()) {
			$err->merge(self::update_htaccess());
		}
		return $err;
	}
	
	private static function collect_for_compressing($type) {
		$in_files = array();
		EventSource::Instance()->invoke_event('jcssmanager_compress', $type, $in_files);
		
		array_walk_recursive(
			$in_files,
			function(&$file, $key) {
				if ($file instanceof HeadDataFile) {
					$file = $file->file;
				}
			},
			$in_files
		);
		return $in_files;
	}
	
	private static function update_htaccess() {
		$err = new Status();
		$htc_option = '';
		$htc_rewrite = '';
		if (Config::has_feature(ConfigJCSSManager::ALSO_GZIP)) {
			$charset = GyroLocale::get_charset();
			$htc_option = array(
				'# Workaround Apache 1.3, which always uses gzip, x-gzip as encoding, which crashes browsers',
				'RemoveEncoding .gz',
				'AddEncoding gzip .gz',		
				'# End workaround',		
				'<FilesMatch .*\.js.gz$>',
				'AddEncoding gzip .js',
				"ForceType \"text/javascript;charset=$charset\"",
				'<IfModule mod_headers.c>',			
				'Header append Vary "Accept-Encoding"',
				'Header set "Accept-Ranges" none', // Works around a bug in chrome
				'</IfModule>',
				'</FilesMatch>',
				'<FilesMatch .*\.css.gz$>',
				'AddEncoding gzip .css',
				"ForceType \"text/css;charset=$charset\"",
				'<IfModule mod_headers.c>',			
				'Header append Vary "Accept-Encoding"',
				'Header set "Accept-Ranges" none', // Works around a bug in chrome
				'</IfModule>',
				'</FilesMatch>',
				'<IfModule mod_expires.c>',
				'ExpiresActive On',			
				'<FilesMatch "^compressed">',
				"ExpiresByType text/css 'access plus 730 days'",
				"ExpiresByType text/javascript 'access plus 730 days'",
				"ExpiresByType application/x-javascript 'access plus 730 days'",
				'</FilesMatch>',
				'</IfModule>'				
			); 
			$htc_rewrite = array(
				'RewriteCond %{HTTP:Accept-encoding} gzip',
				'RewriteCond %{REQUEST_FILENAME}.gz -f',
				'RewriteRule ^(.*)$ $1.gz [QSA,L]'
			);
		}
		Load::components('systemupdateinstaller');
		$err->merge(SystemUpdateInstaller::modify_htaccess('jcssmanager', SystemUpdateInstaller::HTACCESS_OPTIONS, $htc_option));
		$err->merge(SystemUpdateInstaller::modify_htaccess('jcssmanager', SystemUpdateInstaller::HTACCESS_REWRITE, $htc_rewrite));
		return $err;		
	}
	
	public static function concat_css_files($arr_files) {
		$ret = '';
		foreach($arr_files as $file) {
			$ret .= self::transform_css_file($file);
		}
		// Fire an event so the concated CSS can be post processed by other modules
		EventSource::Instance()->invoke_event('jcssmanager_concated', JCSSManager::TYPE_CSS, $ret);
		return $ret;
	}
	
	public static function transform_css_file($file) {
		$ret = '';
		if (substr($file, 0, 1) !== '/' && strpos($file, '://') === false) {
			$file = Config::get_value(Config::URL_ABSPATH) . $file;
		}
		$real_path = realpath($file);
		$handle = fopen($file, 'r');
		while(($line = fgets($handle)) !== false) {
			$ret .= self::transform_css_line($line, $real_path, Config::get_value(Config::URL_ABSPATH));
		}

		return $ret;		
	}

	public static function transform_css_line($line, $src_file_real_path, $base_path) {
		$ret = '';
		$line = trim($line);
		$token = substr($line, 0, 7);

		switch ($token) {
		case '@charse':
			// Works around a bug in WebKit, which dislikes two charset declarations in one file
			$charset_removed = self::remove_until_semicolon($line, 0);
			$ret = self::transform_css_line($charset_removed, $src_file_real_path, $base_path);
			break;
		case '@import':
			$ret = $line;
			$start = strpos($line, '(', 7);
			if ($start !== false) {
				$end = strpos($line, ')', $start);
				if ($end !== false) {
					$start++;
					$file_to_include = trim(substr($line, $start, $end - $start), "'\" \t");
					if (strpos($file_to_include, '://') === false) {
						// NO http:// or alike
						if (substr($file_to_include, 0, 1) !== '/') {
							// no absolute path
							$file_to_include = dirname($src_file_real_path) . '/' . $file_to_include;
						} else  {
							$file_to_include = JCSSManager::make_absolute($file_to_include);
						}
					}
					$resolved = self::transform_css_file($file_to_include);
					$rest_of_line = self::remove_until_semicolon($line, 0);
					$ret = self::transform_css_line($resolved . $rest_of_line, $src_file_real_path, $base_path);
				}
			}
			break;
		default:
			// Set all url(..) stuff absolute
			$regex = '#url\s*\(([\'"]?)([^\)]*)#';
			$rel_path = dirname(str_replace($base_path, '/', $src_file_real_path)) . '/';
			$replace = function($matches) use ($rel_path) {
				$resolved = '';
				$quotes = $matches[1];
				$path = $matches[2];

				if (strpos($path, '://') !== false)  {
					// HTTP and such
					$resolved = $path;
				} else if (substr($path, 0, 1) == '/') {
					// Absolute
					$resolved = $path;
				} else {
					// Relative
					$resolved = $rel_path . $path;
				}

				return "url({$matches[1]}$resolved";
			};
			$ret = preg_replace_callback($regex, $replace, $line);
			break;
		}

		return $ret;
	}

	private static function remove_until_semicolon($in, $start_pos = 0) {
		$pos_of_semicolon = strpos($in, ';', $start_pos);
		if ($pos_of_semicolon !== false) {
			return substr($in, 0, $start_pos) . substr($in, $pos_of_semicolon + 1);
		} else {
			return $in;
		}

	}

}