<?php
/**
 * Wrapper around menu items
 *
 * @var string $content
 * @var string $css_class
 */
print html::div(
	$content,
	'btn-group ' . $css_class
);
