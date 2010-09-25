<?php
/**
 * Interface for textplaceholder
 */
interface ITextPlaceholder {
	/**
	 * Apply on given text
	 * 
	 * @params string $text
	 * @return string
	 */
	public function apply($text);
}