<?php
/**
 * @defgroup YAML
 * @ingroup CSS
 * 
 * Basic installatin of YAML CSS framework.
 * 
 * @see http://www.yaml.de/en/
 * 
 * By default a 213-template is installed. Use the YAML builder to create your own template
 * and CSS stubs: http://builder.yaml.de/ 
 */

EventSource::Instance()->register(new CSSYamlEventSink());

/**
 * Config for CSS YAML module
 * 
 * @author Gerd Riesselmann
 * @ingroup YAML
 */
class ConfigYAML {
	const YAML_VERSION = 'YAML_VERSION';
}

Config::set_value_from_constant(ConfigYAML::YAML_VERSION, 'APP_YAML_VERSION', '3.2');


