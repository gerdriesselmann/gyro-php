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
	
	/**
	 * Make path relativ to web root 
	 * 
	 * @param string $path
	 * @return string
	 */
	public static function make_relativ($path) {
		return str_replace(Config::get_value(Config::URL_ABSPATH), '', $path);
	}
	
	/**
	 * Make path relativ to web root an absolute one 
	 * 
	 * @param string $path
	 * @return string
	 */
	public static function make_absolute($path) {
		return Config::get_value(Config::URL_ABSPATH) . $path;
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
		$in_files = array();
		EventSource::Instance()->invoke_event('jcssmanager_compress', JCSSManager::TYPE_JS, $in_files);
		$js = new JCSSManagerCompressJSCommand($in_files, $out_file);
		$err->merge($js->execute());
		
		$css_base = Config::get_value(ConfigJCSSManager::CSS_DIR);
		foreach(JCSSManager::get_css_types() as $type => $tr) {
			$out_file = ($type !== JCSSManager::TYPE_CSS) ? 'compressed.' . strtolower($type) . '.css' : 'compressed.css';
			$out_file = JCSSManager::make_absolute($css_base . $out_file);
			$in_files = array();
			EventSource::Instance()->invoke_event('jcssmanager_compress', $type, $in_files);
			$css = new JCSSManagerCompressCSSCommand($in_files, $out_file, $type);
			$err->merge($css->execute());
		}		

		// Update htaccess
		if ($err->is_ok()) {
			$err->merge(self::update_htaccess());
		}
		return $err;
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
				'</FilesMatch>',
				'<FilesMatch .*\.css.gz$>',
				'AddEncoding gzip .css',
				"ForceType \"text/css;charset=$charset\"",
				'</FilesMatch>',
				'<IfModule mod_expires.c>',
				'ExpiresActive On',				
				"ExpiresByType text/css 'access plus 2 years'",
				"ExpiresByType text/javascript 'access plus 2 years'",
				"ExpiresByType application/x-javascript 'access plus 2 years'",
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
	
}