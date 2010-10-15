<?php
/**
 * Output a block
 * @var BlockBase $block The block to display
 */
$block_html = '';
if ($block->get_title() !== '') {
	$block_html .= html::h(String::escape($block->get_title()), 2);
}
$block_html .= html::div($block->get_content());
$cls = 'block block-' . String::plain_ascii($block->get_name()); 
$block_html = html::div($block_html, $cls);

print $block_html;