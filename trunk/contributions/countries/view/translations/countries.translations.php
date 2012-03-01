<?php
/**
 * Load translation for countries
 * 
 * This is an example of how to use DB based translations
 * 
 * @author Gerd Riesselmann
 * @ingroup Countries
 */
function countries_load_translations($languages) {
	$ret = array(
		'Africa' => array(
			'de' => 'Afrika'
		),
		'Antartica' => array(
			'de' => 'Antarktis'
		),
		'Asia' => array(
			'de' => 'Asien'
		),
		'Europe' => array(
			'de' => 'Europa'
		),
		'North America' => array(
			'de' => 'Nordamerika'
		),
		'Oceania' => array(
			'de' => 'Ozeanien'
		),
		'South America' => array(
			'de' => 'Südamerika'
		),
		'European Union' => array(
			'de' => 'Europäische Union'
		),
		'NONE' => array(
			'en' => 'None',
			'de' => 'Kein'
		),
		'GEOGRAPHICAL' => array(
			'en' => 'Geographical',
			'de' => 'Geografisch'
		),
		'POLITICAL' => array(
			'en' => 'Political',
			'de' => 'Politisch'
		),
		'CULTURAL' => array(
			'en' => 'Cutural',
			'de' => 'Kulturell'
		)
	);
	// Load translations for countries
	Load::models('countries', 'countriestranslations');
	$dao_c = new DAOCountries();
	$dao_t = new DAOCountriestranslations();
	$dao_t->add_where('lang', DBWhere::OP_IN, $languages);
	$dao_c->join($dao_t);
	
	$query = $dao_c->create_select_query();
	$query->set_fields(array('countries.name' => 'source', 'countries.capital' => 'source_capital', 'countriestranslations.lang' => 'lang', 'countriestranslations.name' => 'translation', 'countriestranslations.capital' => 'translation_capital'));
	
	$countries = array();
	$result = DB::query($query->get_sql(), $dao_c->get_table_driver());
	while($data = $result->fetch()) {
		$countries[$data['source']][$data['lang']] = $data['translation'];
		$tr_cap = $data['translation_capital'];
		if ($tr_cap) {
			$countries[$data['source_capital']][$data['lang']] = $tr_cap;
		}
	}
	
	$ret = array_merge($ret, $countries);
	return $ret;
}
