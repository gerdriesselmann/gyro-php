<?php
/**
 * Facade class for countries
 */
class Countries {
	const GROUP_TYPE_NONE = 'NONE';
	const GROUP_TYPE_POLITICAL = 'POLITICAL';
	const GROUP_TYPE_GEOGRAPHICAL = 'GEOGRAPHICAL';
	const GROUP_TYPE_CULTURAL = 'CULTURAL';
	
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
	 * Create an adapter that loads translated names
     */
    public static function create_localized_sort_adapter($lang) {
    	$dao = new DAOCountries();
    	$trans = new DAOCountriestranslations();
    	$dao->join($trans);
    	$dao->sort('countriestranslations.name');
    	return $dao;
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
    
}
