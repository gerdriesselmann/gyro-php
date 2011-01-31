<?php
/**
 * This route is just to allow parameterized routes with stati cpages, which is deprecated 
 */
class StaticPageParamterizedRoute extends ParameterizedRoute {
	public function weight_against_path($path) {
		return self::WEIGHT_NO_MATCH;
	}
}