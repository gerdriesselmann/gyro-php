<?php
/**
 * An Interface for richt text editor implementations
 */
interface IRichtTextEditor {
	/**
	 * Apply it
	 * 
	 * @param PageData $page_data
	 * @param string $name Name of editor, can be found as class "rte_$name" on HTML textareas  
	 */
	public function apply(PageData $page_data, $name);
}