<?php
/**
 * A block that renders itself only from a template and can be passed parameters.
 * 
 * The blocks name is by default taken from the template: Its the templates file name, 
 * converted to plain ascii. E.g. for a template "news/blocks/teaser", the name would be
 * "news-blocks-teaser".
 * 
 * Block title by default is empty and must be set through template.
 * 
 * The block instance is available as $block within the template. So you may change title or 
 * name like this:
 * 
 * @code
 * $block->title = 'New title';
 * $block->name = 'newname';
 * @endcode
 * 
 * @ingroup Blocks
 * @author Gerd Riesselmann
 */
class TemplatedSimpleBlock extends TemplatedFullBlock {
	/**
	 * Constructor
	 * 
	 * @param string $template The template to render
	 * @param integer $index The block's index. A block with lowest index will be displayed first
	 * @param enum $position Where the block is to be displayed. 
	 */
	public function __construct($template, $index = 1000, $position = self::LEFT) {
		parent::__construct($template, array(), $index, $position);
	}
	
}
