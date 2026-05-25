<?php
use PHPUnit\Framework\TestCase;

class DBFilterColumnTest extends TestCase {
	public function test_constructor_and_getters() {
		$filter = new DBFilterColumn('status', 'active', 'Status');
		$this->assertEquals('status', $filter->get_column());
		$this->assertEquals('active', $filter->get_value());
		$this->assertEquals('Status', $filter->get_title());
	}

	public function test_default_operator() {
		// Default operator is '='
		$filter = new DBFilterColumn('status', 'active', 'Status');
		$this->assertEquals('Status', $filter->get_key());
	}

	public function test_custom_key() {
		$filter = new DBFilter('My Title', 'my-key');
		$this->assertEquals('My Title', $filter->get_title());
		$this->assertEquals('my-key', $filter->get_key());
	}

	public function test_default_key_is_title() {
		$filter = new DBFilter('Status');
		$this->assertEquals('Status', $filter->get_key());
	}

	public function test_is_default() {
		$filter = new DBFilter('Test');
		$this->assertFalse($filter->is_default());
		$filter->set_is_default(true);
		$this->assertTrue($filter->is_default());
	}

	public function test_set_title() {
		$filter = new DBFilter('Old');
		$filter->set_title('New');
		$this->assertEquals('New', $filter->get_title());
	}
}
