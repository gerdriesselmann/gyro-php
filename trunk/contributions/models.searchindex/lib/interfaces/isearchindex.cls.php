<?php
/**
 * Interface for a search index
 * 
 * @ingroup SearchIndex
 * @author Gerd Riesselmann
 */
interface ISearchIndex extends ISearchAdapter {
	/**
	 * Set the search string
	 * 
	 * @param string $search The search string
	 */
	public function set_search($search);
	
	/**
	 * Exclude models from search
	 * 
	 * @param string|array Name of model or array of names of models
	 */
	public function exclude_models($models);
	
	/**
	 * Include only given models in search
	 * 
	 * @param string|array Name of model or array of names of models
	 */
	public function limit_to_models($models);
	
	/**
	 * Sort by Relevance 
	 */
	public function sort_by_relevance();
}