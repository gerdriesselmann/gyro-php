<?php
/**
 * Wrapper around menu items
 *
 * @var string $content
 * @var string $css_class
 */
print html::div(
	html::tag('ul', $content),
	$css_class
);
