<?php
class LocaleTest extends GyroUnitTestCase {
    private $old_lang;
    private $old_charset;
    

	/**
     *    Sets up unit test wide variables at the start
     *    of each test method. To be overridden in
     *    actual user test cases.
     *    @access public
     */
    function setUp() {
    	$this->old_lang = GyroLocale::get_language();
    	$this->old_charset = GyroLocale::get_charset();
    }

    /**
     *    Clears the data set in the setUp() method call.
     *    To be overridden by the user in actual user test cases.
     *    @access public
     */
    function tearDown() {
    	GyroLocale::set_locale($this->old_lang, $this->old_charset);
    }
    
	
	public function test_set_language() {
		$lang = 'fr';
		GyroLocale::set_language($lang);
		$this->assertEqual($lang, GyroLocale::get_language());
		$lang = 'de'; // In case locale's lang already was fr before 
		GyroLocale::set_language($lang);
		$this->assertEqual($lang, GyroLocale::get_language());
	}

	public function test_set_charset() {
		$charset = 'Latin1';
		GyroLocale::set_charset($charset);
		$this->assertEqual($charset, GyroLocale::get_charset());
		$charset = 'UTF-8';
		GyroLocale::set_charset($charset);
		$this->assertEqual($charset, GyroLocale::get_charset());
	}
	
	public function test_get_locales() {
		$arr = GyroLocale::get_locales('de');
		$this->assertTrue(in_array('de_DE', $arr));
		$this->assertTrue(in_array('de', $arr));
		
		$arr = GyroLocale::get_locales('fr');
		$this->assertTrue(in_array('fr_FR', $arr));
		$this->assertTrue(in_array('fr', $arr));

		$arr = GyroLocale::get_locales('en');
		$this->assertTrue(in_array('en_US', $arr));
		$this->assertTrue(in_array('en', $arr));

		$arr = GyroLocale::get_locales('pt_BR');
		$this->assertTrue(in_array('pt_BR', $arr));
		$this->assertTrue(!in_array('pt_BR_PT_BR', $arr));
	}	
}
