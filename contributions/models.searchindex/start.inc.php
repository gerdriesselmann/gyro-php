<?php

/**
 * Config options for search index
 * 
 * @author Gerd Riesselmann
 * @ingroup SearchIndex
 */
class ConfigSearchIndex {
	/**
	 * The name of the query paramter that contains search terms. Default is "q"
	 */
	const QUERY_PARAMETER = 'SEARCHINDEX_QUERY_PARAMETER';
	/**
	 * The name of the search index table (and model). Defaults to "searchindex"
	 */
	const TABLE_NAME = 'SEARCHINDEX_TABLE_NAME'; 
}

Config::set_value_from_constant(
	ConfigSearchIndex::QUERY_PARAMETER,
	'APP_SEARCHINDEX_QUERY_PARAMETER',
	'q'
);
Config::set_value_from_constant(
	ConfigSearchIndex::TABLE_NAME,
	'APP_SEARCHINDEX_TABLE_NAME',
	'searchindex'
);
