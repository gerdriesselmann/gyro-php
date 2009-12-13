<?php
/**
 * Extends UnitTest with assertions regarding Status
 * 
 * @author Gerd Riesselmann
 * @ingroup Simpletest
 */
class GyroUnitTestCase extends UnitTestCase {
    function assertStatus($status, $message = '%s') {
    	$this->assertIsA($status, 'Status', sprintf($message, 'Status type check'));
    }
	
	function assertStatusSuccess($status, $message = '%s') {
        $this->assertStatus($status, $message);
        if ($status instanceof Status) {
        	$this->assertTrue($status->is_ok(), sprintf($message, $status->message));
        }        
    }	

	function assertStatusError($status, $message = '%s') {
        $this->assertStatus($status, $message);
        if ($status instanceof Status) {
        	$this->assertTrue($status->is_error(), sprintf($message, 'Status error check'));
        	if ($status->is_error()) {
        		$this->assertTrue(String::length($status->message) > 0, sprintf($message, 'Status has error message'));
        	}
        }        
    }	
}
