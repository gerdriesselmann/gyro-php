<?php
class TestGyroJSON extends GyroUnitTestCase {
	public function test_decode() {
		$in = '{"abc":12,"foo":"bar","bool0":false,"bool1":true,"arr":[1,2,3,null,5],"float":1.2345}';
		$result = ConverterFactory::decode($in, CONVERTER_JSON);
		$this->assertIsA($result, 'StdClass');
		$this->assertEqual(12, $result->abc);
		$this->assertEqual('bar', $result->foo);
		$this->assertEqual(false, $result->bool0);
		$this->assertEqual(true, $result->bool1);
		$this->assertEqual(array(1,2,3,null,5), $result->arr);
		$this->assertEqual(1.2345, $result->float);

		$in='{"lng":-74.0059729,"lat":40.7142691}';
		$result = ConverterFactory::decode($in, CONVERTER_JSON);
		$this->assertEqual(-74.0059729, $result->lng);
		$this->assertEqual(40.7142691, $result->lat);		
		
		$in = '{"totalResultsCount":2,"geonames":[{"adminCode2":"113","countryName":"USA","adminCode1":"FL","fclName":"city, village,...","elevation":70,"countryCode":"US","lng":-87.2008048,"adminName2":"Santa Rosa County","adminName3":"","fcodeName":"populated place","adminName4":"","timezone":{"dstOffset":-5,"gmtOffset":-6,"timeZoneId":"America/Indiana/Knox"},"fcl":"P","name":"New York","fcode":"PPL","geonameId":4165941,"lat":30.8385202,"population":0,"adminName1":"Florida"},{"adminCode2":"071","countryName":"USA","adminCode1":"NY","fclName":"city, village,...","elevation":136,"countryCode":"US","lng":-74.35682,"adminName2":"Orange County","adminName3":"","fcodeName":"populated place","adminName4":"","timezone":{"dstOffset":-4,"gmtOffset":-5,"timeZoneId":"America/New_York"},"fcl":"P","name":"Florida","fcode":"PPL","geonameId":5117451,"lat":41.3317607,"population":2885,"adminName1":"New York"}]}';
		$result = ConverterFactory::decode($in, CONVERTER_JSON);
		$item = $result->geonames[0];
		$this->assertEqual(-87.2008048, $item->lng);
	}
	
	public function test_encode() {
		$val = array(
			"abc" => 12,
			"foo" => "bar",
			"bool0" => false,
			"bool1" => true,
			"arr" => array(1, 2, 3, null, 5),
			"float" => 1.2345
		);
		$result = ConverterFactory::encode($val, CONVERTER_JSON);
		$test = '{"abc":12,"foo":"bar","bool0":false,"bool1":true,"arr":[1,2,3,null,5],"float":1.2345}';
		$this->assertEqual($test, $result);
	}
}
