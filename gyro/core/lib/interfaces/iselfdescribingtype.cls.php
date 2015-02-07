<?php
/**
 * Interface for classes that can give information about their type
 * to the users
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface ISelfDescribingType {
	/**
	 * Get singular name of type
     *
     * For example for a DAOUsers instance that will be 'user'
	 * 
	 * @return string
	 */
	public function get_type_name_singular();

    /**
     * Get plural name of type
     *
     * For example for a DAOUsers instance that will be 'users'
     *
     * @return string
     */
    public function get_type_name_plural();

    /**
	 * Get description for the type
	 *  
	 * @return string 
	 */
	public function get_type_description();
}
