<?php
/**
 * @defgroup Sphinx
 * 
 * Database driver for the Sphinx full text search engine (http://www.sphinxsearch.com/)
 * 
 * @section Usage
 * 
 * To use the Sphinx full text index, you may continue using the data access objects as
 * you are used to be. Your DAO instance should be derived from DataObjectSphinx, though.
 * 
 * When declaring your table object, pass the Sphinx driver name, like so:
 * 
 * @code
 * protected function create_table_object()  {
 *   return new DBTable(
 *     '{indexname}',
 *     array(
 *       ... index fields ...
 *     ),
 *     {primary key}
 *     array(),
 *     array(),
 *     DBDriverSphinx::DEFAULT_CONNECTION_NAME
 *   );
 * }
 * @endcode
 * 
 * In your config, define these constants:
 * 
 * @li APP_SPHINX_DB_HOST: both host and port of your sphinx daemon, e.g. "localhost:3312"
 * @li APP_SPHINX_DB_NAME: A string that gets prefixed to all index names
 * @li APP_SPHINX_INDEXER_INVOKE: Path and optional arguments to the sphinx indexer, e.g '/usr/local/bin/indexer -c /path/to/sphinx.conf');
 * 
 * @section Features
 * 
 * This module supports querying by full text fields and by attributes. If you want to full text search over all 
 * indexed fields, use the property "sphinx_all_fields":
 * 
 * @code
 * $dao = .. create DAO for index ..
 * $dao->sphinx_all_fields = 'search term';
 * @endcode
 * 
 * You may also use the wildcard instead:
 * 
 * @code
 * $dao = .. create DAO for index ..
 * $dao->add_where('*', '=', 'search term');
 * @endcode
 * 
 * Sphinx virtual columns are available for sorting or filtering:
 * 
 * @code
 * public function get_sortable_columns() {
 *   return array(
 *     'relevance' => new DBSortColumn('@relevance', tr('Relevance'), DBSortColumn::TYPE_MATCH, DBSortColumn::ORDER_FORWARD, true),
 *     'title' => new DBSortColumn('title', tr('Title'), DBSortColumn::TYPE_TEXT)
 *   );
 * }
 * @endcode
 */

if (!class_exists('SphinxClient')) {
	// If PECL extension is not loaded, use PHP file
	require_once dirname(__FILE__) . '/3rdparty/sphinx/sphinxapi.php';
}
DB::create_connection('sphinx', 'sphinx', APP_SPHINX_DB_NAME, false, false, APP_SPHINX_DB_HOST);