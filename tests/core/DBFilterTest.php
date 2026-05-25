<?php
use PHPUnit\Framework\TestCase;

class DBFilterTest extends TestCase {
	public function test_constructor() {
		$filter = new DBFilter('Active', 'active');
		$this->assertEquals('Active', $filter->get_title());
		$this->assertEquals('active', $filter->get_key());
	}

	public function test_constructor_key_from_title() {
		$filter = new DBFilter('Active');
		$this->assertEquals('Active', $filter->get_title());
		$this->assertEquals('Active', $filter->get_key());
	}

	public function test_set_title() {
		$filter = new DBFilter('Old');
		$filter->set_title('New');
		$this->assertEquals('New', $filter->get_title());
	}

	public function test_set_key() {
		$filter = new DBFilter('Title', 'old_key');
		$filter->set_key('new_key');
		$this->assertEquals('new_key', $filter->get_key());
	}

	public function test_is_default() {
		$filter = new DBFilter('Test');
		$this->assertFalse($filter->is_default());

		$filter->set_is_default(true);
		$this->assertTrue($filter->is_default());

		$filter->set_is_default(false);
		$this->assertFalse($filter->is_default());
	}
}
