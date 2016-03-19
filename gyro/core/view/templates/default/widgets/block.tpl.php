<?php
/**
 * Output a block
 * @var BlockBase $block The block to display
 */
$block_html = '';
if ($block->get_title() !== '') {
	$block_html .= html::h(GyroString::escape($block->get_title()), 2);
}
$block_html .= html::div($block->get_content(), 'block-content');
$cls = 'block block-' . GyroString::plain_ascii($block->get_name()); 
$block_html = html::div($block_html, $cls);

print $block_html;