<?php
/**
 * Output a block
 * @var BlockBase $block The block to display
 */
$block_html = '';
if ($block->title !== '') {
	$block_html .= html::h(String::escape($block->title), 2);
}
$block_html .= html::div($block->content);
$cls = 'block block-' . String::plain_ascii($block->name); 
$block_html = html::div($block_html, $cls);

print $block_html;