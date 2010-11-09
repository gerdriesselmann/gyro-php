<?php
/**
 * Contains rules for indexing a single model
 * 
 * @author Gerd Riesselmann
 * @ingroup SearchIndex
 */
class SearchIndexModelRule {
	public $model;
	public $model_id;
	public $weight;
	
	public function __construct($model, $id, $weight = 1) {
		$this->model = $model;
		$this->model_id = $id;
		$this->weight = $weight;		
	}
}
