<?php
Load::models('continents', 'countriesgroups', 'countries2countriesgroups');
/**
 * Facade class for countries
 * 
 * @author Gerd Riesselmann
 * @ingroup Countries
 */
class Countries {
	const GROUP_TYPE_NONE = 'NONE';
	const GROUP_TYPE_POLITICAL = 'POLITICAL';
	const GROUP_TYPE_GEOGRAPHICAL = 'GEOGRAPHICAL';
	const GROUP_TYPE_CULTURAL = 'CULTURAL';
	
	/**
	 * Return possible types for country groups
	 * 
	 * @return array
	 */
	public static function get_group_types() {
		return array(
			self::GROUP_TYPE_NONE => tr(self::GROUP_TYPE_NONE, 'countries'),
			self::GROUP_TYPE_POLITICAL => tr(self::GROUP_TYPE_POLITICAL, 'countries'),
			self::GROUP_TYPE_GEOGRAPHICAL => tr(self::GROUP_TYPE_GEOGRAPHICAL, 'countries'),
			self::GROUP_TYPE_CULTURAL => tr(self::GROUP_TYPE_CULTURAL, 'countries')
		);
	}
	
    /**
     * Returns country for given code (de, fr etc.
     *
     * @param DAOCountries $country_code
     */
    public static function get($country_code) {
        return DB::get_item('countries', 'id', $country_code);
    }

    /**
	 * Create adapter 
	 * 
	 * @return DAOCountries
     */
    public static function create_adapter() {
		return new DAOCountries();
    } 
    
    
    /**
	 * Create adapter for countries in continent
	 * 
	 * @return DAOCountries
     */
    public static function create_continent_adapter($id_continent) {
		$dao = self::create_adapter();
		$dao->id_continent = $id_continent;
		return $dao;	    	
    } 

    /**
	 * Create an adapter that loads translated names
	 * 
	 * @return DAOCountries
     */
    public static function create_localized_sort_adapter($lang = false) {
    	$dao = new DAOCountries();
    	self::localize_adapter($dao, $lang);
    	return $dao;
    }
    
    /**
	 * Join given adapter with translations
	 * 
	 * @param $adapter DAOCountries
	 * @return void
     */
    public static function localize_adapter(DAOCountries $adapter, $lang = false) {
    	if (empty($lang)) {
    		$lang = GyroLocale::get_language();
    	}
    	$trans = new DAOCountriestranslations();
    	$adapter->join(
    		$trans, 
    		array(
    			new DBJoinCondition($trans, 'id_country', $adapter, 'id'),
    			new DBWhere($trans, 'lang', '=', 'en')
    		), 
    		DBQueryJoined::LEFT
    	);
    	$adapter->sort('countriestranslations.name');
    }
    
    /**
     * Returns array of all countries with code as key and name as value
     *
     * @return array
     */
    public static function get_all() {
        $ret = array();
        $dao = self::create_localized_sort_adapter(GyroLocale::get_language());
        $dao->sort('name');
        $dao->find();
        while($dao->fetch()) {
            $ret[$dao->id] = $dao->get_title();
        }
        return $ret;
    }
    
    /**
     * Returns continents as associative array with id as key and translated name as value  
     * 
     * @return array
     */
    public static function get_continents() {
    	$ret = array();
    	$dao = new DAOContinents();
    	$dao->find();
    	while($dao->fetch()) {
    		$ret[$dao->id] = $dao->get_title();
    	}
    	return $ret;
    }
    
    /**
	 * Returns continent with given ID
	 * 
	 * @return DAOContinents
     */
    public static function get_continent($id) {
    	return DB::get_item('continents', 'id', $id); 
    }
    
}
