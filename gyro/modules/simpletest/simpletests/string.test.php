<?php
/*
 * Created on 21.09.2006
 */

class StringTest extends GyroUnitTestCase {
	private $locale;
  
	function setUp() {
		$this->locale = setlocale(LC_ALL, '0');
	}
	    
	function tearDown() {
		setlocale(LC_ALL, $this->locale);
	}
	
	function test_clear_html() {
		$val = '&';
		$this->assertEqual('&amp;', GyroString::clear_html($val));
		$val = '>';
		$this->assertEqual('&gt;', GyroString::clear_html($val));
		$val = "'";
		$this->assertEqual('&#039;', GyroString::clear_html($val));
		$val = '"';
		$this->assertEqual('&quot;', GyroString::clear_html($val));
		$val = '<a href="test">Test</a>';
		$this->assertEqual('Test', GyroString::clear_html($val));
		$val = '<input type="text" name="password" value="topsecret">';
		$this->assertEqual('', GyroString::clear_html($val));
		$val = '<textarea name="username">topsecret</textarea>';
		$this->assertEqual('topsecret', GyroString::clear_html($val));
		
		// try attack vectors from http://ha.ckers.org/xss.html
		// attack vector as key, text that should not be contained in clean as value
		$values = array(
			'\'\';!--"<XSS>=&{()}' => '<XSS',
			'<<p />script>alert("hallo");<</p>/script>' => 'script',
			"';alert(String.fromCharCode(88,83,83))//\';alert(String.fromCharCode(88,83,83))//\";alert(String.fromCharCode(88,83,83))//\";alert(String.fromCharCode(88,83,83))//--></SCRIPT>\">'><SCRIPT>alert(String.fromCharCode(88,83,83))</SCRIPT>" => '<script',
			'<SCRIPT SRC=http://ha.ckers.org/xss.js></SCRIPT>' => 'js',
			'<IMG SRC="javascript:alert(\'XSS\');">' => 'alert',
			'<iframe src=http://ha.ckers.org/scriptlet.html <' => 'http',
			'<XSS STYLE="xss:expression(alert(\'XSS\'))">' => 'XSS',
			'<sc<script>ript>' => 'script',
		);
		foreach($values as $attack => $test) {
			$clean = GyroString::clear_html($attack);
			//print htmlentities($attack)  . ' ============== ' . htmlentities($clean) . '</br >';
			$this->assertIdentical(false, GyroString::strpos($clean, $test), 'Not filtered: ' . htmlentities($attack, ENT_QUOTES, GyroLocale::get_charset()));
		}
	}
	
	function test_escape() {
		$val = ' <Test> ';
		$this->assertEqual('&lt;Test&gt;', GyroString::escape($val));
		$val = ' "Test" ';
		$this->assertEqual('&quot;Test&quot;', GyroString::escape($val));		
	}
	
	function test_currency() {
		setlocale(LC_ALL, array('de_DE.utf8', 'de_DE.UTF-8', 'de_DE@euro', 'de_DE', 'de', 'ge', 'deu_deu'));
		$val = 2100.02;
		// Achtung: stellt das Eurozeichen nicht richtig dar!
		// echo GyroString::currency($val);
		$this->assertEqual('2.100,02 €',GyroString::currency($val));
		// wie muss das für UK und US heissen?1
		setlocale(LC_ALL, array('en_US.utf8', 'en_US', 'en'));
		$val = 4512.43;
		$this->assertEqual('$4,512.43',GyroString::currency($val));
	}
	
	function test_number() {
		setlocale(LC_ALL, 'C');
		$val = 'abc';
		$this->assertEqual('0.00', GyroString::number($val, 2));
		$this->assertEqual('0', GyroString::number($val, 0));
		$val = 10000.99;
		$this->assertEqual('10000.99', GyroString::number($val, 2));
		$this->assertEqual('10001.0', GyroString::number($val, 1));
		$val = -13.7;
		$this->assertEqual('-13.700000000000000000000000000000', GyroString::number($val, 30));
		$this->assertEqual('-14', GyroString::number($val, 0));
		
		setlocale(LC_ALL, array('de_DE.utf8', 'de_DE.UTF-8', 'de_DE@euro', 'de_DE', 'de', 'ge'));
		$val = 'abc';
		$this->assertEqual(GyroString::number($val, 2), '0,00');		
		$val = 10000.99;
		$this->assertEqual('10.000,99', GyroString::number($val, 2));
		$val = -10000.99;
		$this->assertEqual('-10.000,99', GyroString::number($val, 2));
	}	
	
		
	function test_to_lower() {
		//setlocale(LC_ALL, 'C');
		$val = 'THiS IS A norMal SentencE';
		$this->assertEqual('this is a normal sentence',GyroString::to_lower($val));
		$val = 'THIS IS A NORMAL SENTENCE.';
		$this->assertEqual('this is a normal sentence.',GyroString::to_lower($val));
		$val = '1234567890';
		$this->assertEqual('1234567890',GyroString::to_lower($val));
		$val = '!"§$%&/()=?^°<>#+,.;:|-_´`\{}[]*~"';
		$this->assertEqual('!"§$%&/()=?^°<>#+,.;:|-_´`\{}[]*~"',GyroString::to_lower($val));
		
		//setlocale(LC_ALL, array('de_DE.utf8', 'de_DE@euro', 'de_DE', 'de', 'ge'));
		$val = 'ICH HÄtte Gerne EiN äöüß oder ÄÖÜ';
		$this->assertEqual('ich hätte gerne ein äöüß oder äöü',GyroString::to_lower($val));
		
		// test partial conversion
		$val = 'ABC';
		$this->assertEqual('aBC', GyroString::to_lower($val, 1));
		$this->assertEqual('abC', GyroString::to_lower($val, 2));
		$this->assertEqual('abc', GyroString::to_lower($val, 3));
		$this->assertEqual('abc', GyroString::to_lower($val, 10));
	}
	
	function test_to_upper() {
		//setlocale(LC_ALL, 'C');
		$val = 'tHiS IS A norMal SentencE';
		$this->assertEqual('THIS IS A NORMAL SENTENCE',GyroString::to_upper($val));
		$val = 'this is a normal sentence.';
		$this->assertEqual('THIS IS A NORMAL SENTENCE.',GyroString::to_upper($val));
		$val = '1234567890';
		$this->assertEqual('1234567890',GyroString::to_upper($val));	
		$val = '!"§$%&/()=?^°<>#+,.;:|-_´`\{}[]*~"';
		$this->assertEqual('!"§$%&/()=?^°<>#+,.;:|-_´`\{}[]*~"',GyroString::to_upper($val));
		
		//setlocale(LC_ALL, array('de_DE.utf8', 'de_DE@euro', 'de_DE', 'de', 'ge'));
		$val = 'ich hätte gerne ein äöüß oder ÄÖÜ';
		$this->assertEqual('ICH HÄTTE GERNE EIN ÄÖÜß ODER ÄÖÜ',GyroString::to_upper($val));
		
		// test partial conversion
		$val = 'abc';
		$this->assertEqual('Abc', GyroString::to_upper($val, 1));
		$this->assertEqual('ABc', GyroString::to_upper($val, 2));
		$this->assertEqual('ABC', GyroString::to_upper($val, 3));
		$this->assertEqual('ABC', GyroString::to_upper($val, 10));
	}
	
	function test_length() {
		//setlocale(LC_ALL, 'C');
		$val = 'this';
		$this->assertEqual(4,GyroString::length($val));
		$val = 'this is a normal sentence.';
		$this->assertEqual(26,GyroString::length($val));
		$val = 't';
		$this->assertEqual(1,GyroString::length($val));	
		$val = '';
		$this->assertEqual(0,GyroString::length($val));	
		$val = "!\"§$%&/()=?^°<>\0#+,.;:|-_´`\{}[]*~\""; // contains a 0 byte!
		$this->assertEqual(35,GyroString::length($val));
		
		//setlocale(LC_ALL, array('de_DE.utf8', 'de_DE@euro', 'de_DE', 'de', 'ge'));
		$val = 'ich hätte gerne ein äöüß oder ÄÖÜ'; // tests UTF8 compliance!
		$this->assertEqual(33,GyroString::length($val));
	}
	
	function test_strpos() {
		//setlocale(LC_ALL, 'C');
		$val = 'This is a normal sentence.';
		$this->assertEqual(0,GyroString::strpos($val,'This'));
		$this->assertEqual(4,GyroString::strpos($val,' is',1));
		$this->assertEqual(25,GyroString::strpos($val,'.'));
		$val = '!"§$%&/()=?^°<>#+,.;:|-_´`\{}[]*~"';
		$this->assertEqual(0,GyroString::strpos($val,'!'));
		$this->assertEqual(1,GyroString::strpos($val,'"'));
		$this->assertEqual(1,GyroString::strpos($val,'"', 1));
		$this->assertEqual(33,GyroString::strpos($val,'"', 2));
		$this->assertEqual(2,GyroString::strpos($val,'§'));
		$this->assertEqual(7,GyroString::strpos($val,'()'));
		
		//setlocale(LC_ALL, array('de_DE.utf8', 'de_DE@euro', 'de_DE', 'de', 'ge'));
		$val = 'ich hätte gerne ein äöüß oder ÄÖÜ';
		$this->assertEqual(5,GyroString::strpos($val,'ä'));
	}
	
	function test_strrpos() {
		//setlocale(LC_ALL, 'C');
		$val = 'This is a normal sentence.';
		$this->assertEqual(22,GyroString::strrpos($val,'n'));
		$this->assertEqual(1,GyroString::strrpos($val,'h'));
		$this->assertEqual(20,GyroString::strrpos($val,'t'));
		$val = '!"§$%&/()=?^°<>#+,.;:|-_´`\{}[]*~"';
		$this->assertEqual(0,GyroString::strrpos($val,'!'));
		$this->assertEqual(2,GyroString::strrpos($val,'§'));
		$this->assertEqual(33,GyroString::strrpos($val,'"'));
		
		//setlocale(LC_ALL, array('de_DE.utf8', 'de_DE@euro', 'de_DE', 'de', 'ge'));
		$val = 'ich hätte gerne ein äöüß oder ÄÖÜ';
		$this->assertEqual(18,GyroString::strrpos($val,'n'));
		$this->assertEqual(30,GyroString::strrpos($val,'Ä'));
		$this->assertEqual(23,GyroString::strrpos($val,'ß'));
	}
	
	function test_substr() {
		$val = 'This is a normal sentence';
		$this->assertEqual('This', GyroString::substr($val, 0, 4));		
		$this->assertEqual('s is a no', GyroString::substr($val, 3, 9));
		$this->assertEqual('ce', GyroString::substr($val, 23, 2));
		$val = 'ich hätte gerne ein äöüß oder ÄÖÜ';
		$this->assertEqual('ch hä', GyroString::substr($val, 1, 5));
		$this->assertEqual('äöüß ', GyroString::substr($val, 20, 5));
		$this->assertEqual('der ÄÖÜ', GyroString::substr($val, 26, 7));
		$this->assertEqual('', GyroString::substr($val, 0, 0));
	}
	
	function test_substr_word() {
		$val = 'This is a normal sentence';
		$this->assertEqual($val, GyroString::substr_word($val, 0, 1000));
		$this->assertEqual(' is a normal sentence', GyroString::substr_word($val, 4, 1000));
		$this->assertEqual('This', GyroString::substr_word($val, 0, 5));		
		$this->assertEqual('This', GyroString::substr_word($val, 0, 4));
		$this->assertEqual('', GyroString::substr_word($val, 0, 3));
		$this->assertEqual('This', GyroString::substr_word($val, 0, 6));
		$this->assertEqual('is', GyroString::substr_word($val, 2, 5));
		$this->assertEqual('is is', GyroString::substr_word($val, 2, 6));		
		$this->assertEqual('is is', GyroString::substr_word($val, 2, 7));
	}

	function test_substr_sentence() {
		$val = 'This is a normal sentence';
		// Test substr_word fallback
		$this->assertEqual($val, GyroString::substr_sentence($val, 0, 1000));
		$this->assertEqual(' is a normal sentence', GyroString::substr_sentence($val, 4, 1000));
		$this->assertEqual('This', GyroString::substr_sentence($val, 0, 5));		
		$this->assertEqual('This', GyroString::substr_sentence($val, 0, 4));
		$this->assertEqual('', GyroString::substr_sentence($val, 0, 3));
		$this->assertEqual('This', GyroString::substr_sentence($val, 0, 6));
		$this->assertEqual('is', GyroString::substr_sentence($val, 2, 5));
		$this->assertEqual('is is', GyroString::substr_sentence($val, 2, 6));		
		$this->assertEqual('is is', GyroString::substr_sentence($val, 2, 7));
		
		// Now see if the mthod itself works 
		$val = 'This is a sentence. And another? Yes! Duh.';
		$this->assertEqual($val, GyroString::substr_sentence($val, 0, 1000));
		$this->assertEqual('This is a', GyroString::substr_sentence($val, 0, 17));
		$this->assertEqual('This is a sentence', GyroString::substr_sentence($val, 0, 18));
		$this->assertEqual('This is a sentence.', GyroString::substr_sentence($val, 0, 19)); 
		$this->assertEqual('This is a sentence.', GyroString::substr_sentence($val, 0, 20));
		$this->assertEqual('This is a sentence.', GyroString::substr_sentence($val, 0, 21));
		
		$this->assertEqual('This is a sentence. And another?', GyroString::substr_sentence($val, 0, 32));
		$this->assertEqual('This is a sentence. And another?', GyroString::substr_sentence($val, 0, 33));
		
		$this->assertEqual('This is a sentence. And another? Yes!', GyroString::substr_sentence($val, 0, 37));
		$this->assertEqual('This is a sentence. And another? Yes!', GyroString::substr_sentence($val, 0, 38));
		
		// Some weird stuff
		$val = "Test date: 20.20.2020, and url: www.example.org. New sentence!";
		$this->assertEqual('Test date: 20.20.2020, and', GyroString::substr_sentence($val, 0, 29));
		$this->assertEqual('Test date: 20.20.2020, and url: www.example.org.', GyroString::substr_sentence($val, 0, 56));

		$val = "A sentence. Something with a number: 1234.00 Euro he paid.";
		$this->assertEqual('A sentence.', GyroString::substr_sentence($val, 0, 50));
	}
	
	function test_left() {
		$val = 'ich hätte gerne ein äöüß oder ÄÖÜ';
		$this->assertEqual('ich hä', GyroString::left($val, 6));
		$this->assertEqual('ich hätte gerne ein äöüß oder ÄÖÜ', GyroString::left($val, 100));
		$this->assertEqual('', GyroString::left($val, 0));
	}
	
	function test_right() {
		$val = 'ich hätte gerne ein äöüß oder ÄÖÜ';
		$this->assertEqual('oder ÄÖÜ', GyroString::right($val, 8));
		$this->assertEqual('ich hätte gerne ein äöüß oder ÄÖÜ', GyroString::right($val, 100));
		$this->assertEqual('', GyroString::right($val, 0));
	}	
	
	function test_starts_with() {
		$val = 'This is a normal sentence';
		$this->assertTrue(GyroString::starts_with($val,'T'));
		$val = 'This is a normal sentence';
		$this->assertTrue(GyroString::starts_with($val,'This'));
		$val = 'This is a normal sentence';
		$this->assertFalse(GyroString::starts_with($val,'A'));			
		$val = '!"§$%';
		$val = 'This is a normal sentence';
		$this->assertFalse(GyroString::starts_with($val,''));
	}
	
	function test_ends_with() {
		$val = 'This is a normal sentence';
		$this->assertTrue(GyroString::ends_with($val,'e'));
		$val = 'This is a normal sentence';
		$this->assertFalse(GyroString::ends_with($val,'A'));			
		$val = 'This is a normal sentence';
		$this->assertTrue(GyroString::ends_with($val,'entence'));
		$val = 'This is a normal sentence';
		$this->assertFalse(GyroString::ends_with($val,''));
	}
	
	function test_plain_ascii() {
		$val = 'ich hätte gerne ein äöüß oder ÄÖÜ';
		$this->assertEqual('ich-haette-gerne-ein-aeoeuess-oder-aeoeue', GyroString::plain_ascii($val,'-'));
		$val = 'Camões São P. Cachaça en España y Citroën';
		$this->assertEqual('camoes-sao-p-cachaca-en-espana-y-citroen', GyroString::plain_ascii($val,'-'));
	}
	
	function test_extract_before() {
		$val = 'This is a normal sentence';
		$this->assertEqual('This is a normal s', GyroString::extract_before($val,'ent'));
		$this->assertEqual('This is a normal s', GyroString::extract_before($val,'en'));
		$this->assertEqual('', GyroString::extract_before($val,'This'));
		$this->assertEqual($val, GyroString::extract_before($val,'NOTFOUND'));
	}

	function test_extract_after() {
		$val = 'This is a normal sentence';
		$this->assertEqual('tence', GyroString::extract_after($val,'sen'));
		$this->assertEqual('tence', GyroString::extract_after($val,'en'));
		$this->assertEqual('', GyroString::extract_after($val,'sentence'));
		$this->assertEqual($val, GyroString::extract_after($val,'NOTFOUND'));
	}
}
?>
