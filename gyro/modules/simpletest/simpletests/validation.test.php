<?php
/**
 * Created on 03.12.2006
 *
 * @author Catarina Schmidt
 */

class ValidationTest extends GyroUnitTestCase {
	
	function test_is_email() {
		$this->assertTrue(Validation::is_email('info@fade-in.de'));
		$this->assertTrue(Validation::is_email('a.b@c.de'));
		$this->assertTrue(Validation::is_email('a..b@c.de'));
		$this->assertTrue(Validation::is_email('a@abc.fgh.de'));
		$this->assertTrue(Validation::is_email('a@abc.fgh.de.com'));
		$this->assertFalse(Validation::is_email('abc.de'));
		$this->assertFalse(Validation::is_email('@abc.de'));
		$this->assertFalse(Validation::is_email('a@abc'));		
		$this->assertFalse(Validation::is_email('a.a@abc'));
		$this->assertTrue(Validation::is_email('a+b@abc.de')); // + is allowed but often forgotten!
		$this->assertTrue(Validation::is_email('a++b@abc.de')); // + is allowed but often forgotten!
		$this->assertTrue(Validation::is_email('a+b@ab+c.de')); // We are flexible :-)
		$this->assertTrue(Validation::is_email('a+b@ab-c.de')); 
		$this->assertTrue(Validation::is_email('a-_b1234567890@abc.de'));
		$this->assertFalse(Validation::is_email('a b@abc.de'));
		$this->assertTrue(Validation::is_email('ts@ambiweb.info'));
	}
	
	function test_is_string_of_length() {
		$this->assertTrue(Validation::is_string_of_length(''));
		$this->assertTrue(Validation::is_string_of_length('', 0));
		$this->assertTrue(Validation::is_string_of_length('', 0, 1));
		$this->assertFalse(Validation::is_string_of_length('', 1));
		$this->assertTrue(Validation::is_string_of_length('abc', 3)); 
		$this->assertTrue(Validation::is_string_of_length('abc', 3, 3)); 
		$this->assertFalse(Validation::is_string_of_length('abc', 4)); 
		$this->assertFalse(Validation::is_string_of_length('abc', 1, 2)); 
		$this->assertTrue(Validation::is_string_of_length(' '));
		
		$this->assertFalse(Validation::is_string_of_length(false));
		$this->assertFalse(Validation::is_string_of_length(0));
	}
	
	function test_is_int() {
		$this->assertTrue(Validation::is_int(1));
		$this->assertTrue(Validation::is_int('1'));		
	}
}
?>