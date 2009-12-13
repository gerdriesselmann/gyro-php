<?php
/**
 * Interface for all elements that render content
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface IRenderer {
	const NONE = false;
	
	/**
	 * Renders what should be rendered
	 *
	 * @param int $policy Defines how to render, meaning depends on implementation
	 * @return string The rendered content
	 */
	public function render($policy = self::NONE);
}