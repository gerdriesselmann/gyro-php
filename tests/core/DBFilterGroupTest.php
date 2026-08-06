<?php
use PHPUnit\Framework\TestCase;

class DBFilterGroupTest extends TestCase {
	public function test_constructor() {
		$group = new DBFilterGroup('status', 'Status Filter');
		$this->assertEquals('status', $group->get_group_id());
		$this->assertEquals('Status Filter', $group->get_name());
		$this->assertEquals(0, $group->count());
	}

	public function test_add_and_get_filter() {
		$group = new DBFilterGroup('status', 'Status');
		$filter1 = new DBFilter('Active');
		$filter2 = new DBFilter('Inactive');

		$group->add_filter('active', $filter1);
		$group->add_filter('inactive', $filter2);

		$this->assertEquals(2, $group->count());
		$this->assertSame($filter1, $group->get_filter('active'));
		$this->assertSame($filter2, $group->get_filter('inactive'));
		$this->assertFalse($group->get_filter('nonexistent'));
	}

	public function test_get_keys() {
		$group = new DBFilterGroup('g', 'Group');
		$group->add_filter('a', new DBFilter('Alpha'));
		$group->add_filter('b', new DBFilter('Beta'));
		$group->add_filter('c', new DBFilter('Gamma'));

		$this->assertEquals(array('a', 'b', 'c'), $group->get_keys());
	}

	public function test_get_filters() {
		$f1 = new DBFilter('Alpha');
		$f2 = new DBFilter('Beta');

		$group = new DBFilterGroup('g', 'Group');
		$group->add_filter('a', $f1);
		$group->add_filter('b', $f2);

		$filters = $group->get_filters();
		$this->assertCount(2, $filters);
		$this->assertSame($f1, $filters[0]);
		$this->assertSame($f2, $filters[1]);
	}

	public function test_default_key() {
		$f1 = new DBFilter('Alpha');
		$f2 = new DBFilter('Beta');

		$group = new DBFilterGroup('g', 'Group');
		$group->add_filter('a', $f1);
		$group->add_filter('b', $f2);

		$group->set_default_key('b');
		$this->assertEquals('b', $group->get_default_key());
		$this->assertTrue($f2->is_default());
		$this->assertFalse($f1->is_default());

		$group->set_default_key('a');
		$this->assertTrue($f1->is_default());
		$this->assertFalse($f2->is_default());
	}

	public function test_current_key() {
		$f1 = new DBFilter('Alpha');
		$f2 = new DBFilter('Beta');

		$group = new DBFilterGroup('g', 'Group');
		$group->add_filter('a', $f1);
		$group->add_filter('b', $f2);

		$group->set_current_key('a');
		$this->assertEquals('a', $group->get_current_key());
		$this->assertSame($f1, $group->get_current_filter());

		$group->set_current_key('b');
		$this->assertSame($f2, $group->get_current_filter());
	}

	public function test_current_filter_fallback_to_default() {
		$f1 = new DBFilter('Alpha');
		$f2 = new DBFilter('Beta');

		$group = new DBFilterGroup('g', 'Group');
		$group->add_filter('a', $f1);
		$group->add_filter('b', $f2);
		$group->set_default_key('a');

		// Set current to nonexistent key -> falls back to default
		$group->set_current_key('nonexistent');
		$this->assertSame($f1, $group->get_current_filter());
	}

	public function test_constructor_with_filters() {
		$f1 = new DBFilter('Alpha');
		$f2 = new DBFilter('Beta');

		$group = new DBFilterGroup('g', 'Group', array('a' => $f1, 'b' => $f2), 'b');
		$this->assertEquals(2, $group->count());
		$this->assertEquals('b', $group->get_default_key());
		$this->assertTrue($f2->is_default());
	}
}
