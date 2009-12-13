<?php
/**
 * Template for displaying a breadcrumb
 * 
 * @var string $breadcrumb_prefix A string to display before breadcrumb, if any
 * @var string $breadcrumb_glue The string to connect breadcrumb items
 * @var array $breadcrumb_items Array of html snippets forming the breadcrumb
 */
$out = $breadcrumb_prefix . implode($breadcrumb_glue, $breadcrumb_items);
?>
<div class="breadcrumb"><?php print $out; ?></div>

