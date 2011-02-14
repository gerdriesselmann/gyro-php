<?php
require_once APP_SIMPLETEST_DIR . 'autorun.php';

/**
 * Extends UnitTest with assertions regarding Status
 * 
 * @author Gerd Riesselmann
 * @ingroup Simpletest
 */
class GyroUnitTestCase extends UnitTestCase {
	protected $is_console = false;
	
	public function __construct() {
		$this->is_console = class_exists('Console') && Console::is_console_request();
	}
	
	/**
	 * Assert given argument is an instance of Status
	 */
    protected function assertStatus($status, $message = '%s') {
    	$this->assertIsA($status, 'Status', sprintf($message, 'Status type check'));
    }
	
	/**
	 * Assert given argument is an instance of Status and its state is SUCCESS
	 */
    protected function assertStatusSuccess($status, $message = '%s') {
        $this->assertStatus($status, $message);
        if ($status instanceof Status) {
        	$this->assertTrue($status->is_ok(), sprintf($message, 'Status success check'));
        }        
    }	

	/**
	 * Assert given argument is an instance of Status and its state is ERROR
	 */
    protected function assertStatusError($status, $message = '%s') {
        $this->assertStatus($status, $message);
        if ($status instanceof Status) {
        	$this->assertTrue($status->is_error(), sprintf($message, 'Status error check'));
        	if ($status->is_error()) {
        		$this->assertTrue(String::length($status->to_string()) > 0, sprintf($message, 'Status has error message'));
        	}
        }        
    }	
    
    /**
     * Assert given argument equals given URL path
     */
    protected function assertEqualsPath($arg1, $arg2, $message = '%s') {
    	if ($this->is_console) {
    		$arg1_path = Url::create($arg1);
    		$arg1 = $arg1_path->is_valid() ? '/' . $arg1_path->get_path() : $arg1;
    		$arg2_path = Url::create($arg2);
    		$arg2 = $arg2_path->is_valid() ? '/' . $arg2_path->get_path() : $arg2;
    	}	
   		$this->assertEqual($arg1, $arg2, $message);
	}
    
}
