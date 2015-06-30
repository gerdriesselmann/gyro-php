<?php
/**
 * Compile Sass files
 *
 * @return Status
 */
function css_sass_check_preconditions() {
	Load::components('sass');
	return SASS::compile_all();
}
