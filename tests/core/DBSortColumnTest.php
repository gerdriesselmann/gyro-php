<?php
use PHPUnit\Framework\TestCase;

class DBSortColumnTest extends TestCase {
	public function test_constructor_and_getters() {
		$col = new DBSortColumn('name', 'Name', DBSortColumn::TYPE_TEXT);
		$this->assertEquals('name', $col->get_column());
		$this->assertEquals('Name', $col->get_title());
		$this->assertEquals(DBSortColumn::ORDER_FORWARD, $col->get_direction());
		$this->assertFalse($col->get_is_single_direction());
	}

	public function test_direction_change() {
		$col = new DBSortColumn('price', 'Price', DBSortColumn::TYPE_CURRENCY);
		$col->set_direction(DBSortColumn::ORDER_BACKWARD);
		$this->assertEquals(DBSortColumn::ORDER_BACKWARD, $col->get_direction());
	}

	public function test_single_direction_ignores_change() {
		$col = new DBSortColumn('score', 'Score', DBSortColumn::TYPE_NUMERIC, DBSortColumn::ORDER_FORWARD, true);
		$this->assertTrue($col->get_is_single_direction());
		$col->set_direction(DBSortColumn::ORDER_BACKWARD);
		// Direction should NOT change for single_direction columns
		$this->assertEquals(DBSortColumn::ORDER_FORWARD, $col->get_direction());
	}

	public function test_sort_order_text() {
		$col = new DBSortColumn('name', 'Name', DBSortColumn::TYPE_TEXT);
		$this->assertEquals(ISearchAdapter::ASC, $col->get_sort_order(DBSortColumn::ORDER_FORWARD));
		$this->assertEquals(ISearchAdapter::DESC, $col->get_sort_order(DBSortColumn::ORDER_BACKWARD));
	}

	public function test_sort_order_date_reversed() {
		// Date type: forward = DESC (newer first), backward = ASC
		$col = new DBSortColumn('created', 'Date', DBSortColumn::TYPE_DATE);
		$this->assertEquals(ISearchAdapter::DESC, $col->get_sort_order(DBSortColumn::ORDER_FORWARD));
		$this->assertEquals(ISearchAdapter::ASC, $col->get_sort_order(DBSortColumn::ORDER_BACKWARD));
	}

	public function test_sort_order_match_reversed() {
		// Match type: forward = DESC (most important first), backward = ASC
		$col = new DBSortColumn('relevance', 'Match', DBSortColumn::TYPE_MATCH);
		$this->assertEquals(ISearchAdapter::DESC, $col->get_sort_order(DBSortColumn::ORDER_FORWARD));
		$this->assertEquals(ISearchAdapter::ASC, $col->get_sort_order(DBSortColumn::ORDER_BACKWARD));
	}

	public function test_opposite_order() {
		$col = new DBSortColumn('x', 'X', DBSortColumn::TYPE_TEXT);
		$this->assertEquals(DBSortColumn::ORDER_BACKWARD, $col->get_opposite_order(DBSortColumn::ORDER_FORWARD));
		$this->assertEquals(DBSortColumn::ORDER_FORWARD, $col->get_opposite_order(DBSortColumn::ORDER_BACKWARD));
	}
}
