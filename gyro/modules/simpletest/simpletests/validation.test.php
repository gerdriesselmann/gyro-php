<?php
/**
 * Created on 03.12.2006
 *
 * @author Catarina Schmidt
 */

class ValidationTest extends GyroUnitTestCase {
	function test_is_domain() {
		$this->assertTrue(Validation::is_domain('a.de'));
		$this->assertTrue(Validation::is_domain('1.a.de'));
		$this->assertFalse(Validation::is_domain('de'));
		$this->assertFalse(Validation::is_domain('.de'));
		$this->assertFalse(Validation::is_domain('a.12'));
		$this->assertFalse(Validation::is_domain('@a.de'));
		$this->assertFalse(Validation::is_domain('a .de'));
	}
	
	
	function test_is_email() {
		$this->assertTrue(Validation::is_email('a.b@c.de'));
		$this->assertTrue(Validation::is_email('a..b@c.de')); // I know people with such address, really! 
		$this->assertFalse(Validation::is_email('.ab@c.de')); // I know people with such address, really!
		$this->assertFalse(Validation::is_email('ab.@c.de')); // I know people with such address, really!
		
		// Allowed are upper- and lowercase alpha, 0-0 and 
		// ! # $ % &  ' *  + - = ? ^ _ ` { | } and ~ 
		$allowed = array(
			'!', '#', '$', '%', '&', "'", '*', '+', '-', '/', '=', '?', '^', '_', '`', '{', '|', '}', '~'
		);
		for($i = ord('0'); $i <= ord('9'); $i++) {
			$allowed[] = chr($i);
		} 
		for($i = ord('a'); $i <= ord('z'); $i++) {
			$allowed[] = chr($i);
		}
		for($i = ord('A'); $i <= ord('Z'); $i++) {
			$allowed[] = chr($i);
		}
        // Check all
        for ($i = 0; $i < 255; $i++) {
        	$chr = chr($i);
        	if ($chr == '.') {
        		continue;
        	}
        	$test = array(
        		$chr . 'mail@example.org',
        		$chr . $chr . 'mail@example.org',
        		'pre' . $chr . $chr . 'mail@example.org',
        		'pre' . $chr . 'mail@example.org',
        		'pre' . $chr . $chr . '@example.org',
        		'pre' . $chr . '@example.org',
        	);
        	$true = in_array($chr, $allowed);
        	foreach($test as $t)
        		if ($true) { 
        			$this->assertTrue(Validation::is_email($t), $t . ' should be considered a VALID email address, but was not');
        		}
        		else {
        			$this->assertFalse(Validation::is_email($t), $t . ' should be considered an INVALID email address, but was not');
        		}
        }		
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
	
	function test_is_ip4() {
		$true = array(
			'1.1.1.1',
			'255.255.255.255',
			'255.0255.255.255',
			'255.025.255.255',
			'1.0.0.0',
			'0.0.0.1'
		);
		$false = array(
			'::0:0.0.0.0',
			'0.0.0.0',
			'::',
			'256.1.2.3',			
		);
		foreach($true as $test) {
			$this->assertTrue(Validation::is_ip4($test), $test . ' is valid IPv4');
		}	
		foreach($false as $test) {
			$this->assertFalse(Validation::is_ip4($test), $test . ' is invalid IPv4');
		}	
		
		$test = '255.0255.2.55';
		Validation::is_ip4($test);
		$this->assertEqual($test, '255.255.2.55', 'IPv4 converts to well formed version');		

		$test = '255.255.255.A';
		Validation::is_ip4($test);
		$this->assertEqual($test, '255.255.255.A', 'IPv4 leaves failing IPs untouched');		
	}
	
	function test_is_ip6() {
		$true = array(
			'A::A:1.1.1.1',
			'12AB:0000:0000:CD30:0000:0000:0000:0000',
      		'12AB::CD30:0:0:0:0',
      		'12AB:0:0:CD30::',
			'::13.1.68.3',
         	'::FFFF:129.144.52.38',
			'0:0:0:0:0:0:13.1.68.3',
			'0:0:0:0:0:FFFF:129.144.52.38',
		);
		$false = array(
			'::0:0.0.0.0',
			'::0.0.0.0',
			'::',
			'::0',
			'ABCD:1080:0:0:0:8:800:200C:417A',
			'12AB::0:0:CD30::',
			'::FFFF:129.144..38',
			'::FFFF:129.144.52.',
			'::FFFF:129.144.52',
			'::FFFF:129.144.A2.38',
		);
		foreach($true as $test) {
			$this->assertTrue(Validation::is_ip6($test), $test . ' is valid IPv6');
		}	
		foreach($false as $test) {
			$this->assertFalse(Validation::is_ip6($test), $test . ' is invalid IPv6');
		}	
	}
}
?>