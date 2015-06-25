<?php
/**
 * @defgroup PostCSS
 * @ingroup CSS
 * 
 * Support for PostCSS command line
 *
 * @see https://github.com/postcss/postcss
 */

EventSource::Instance()->register(new CSSPostCSSEventSink());

/**
 * Config for CSS PostCSS module
 *
 * @author Gerd Riesselmann
 * @ingroup PostCSS
 */
class ConfigPostCSS {
	/**
	 * If set to true, PostCSS will process CSS files concated by JCSSManager
	 */
	const JCSSMANAGER_INTEGRATION = 'POSTCSS_JCSSMANAGER_INTEGRATION';
}

Config::set_feature_from_constant(ConfigPostCSS::JCSSMANAGER_INTEGRATION, 'POSTCSS_JCSSMANAGER_INTEGRATION', false);


