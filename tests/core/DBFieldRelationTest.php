<?php
use PHPUnit\Framework\TestCase;

class DBFieldRelationTest extends TestCase {
	public function test_get_set() {
		$rel = new DBFieldRelation('source_id', 'target_id');
		$this->assertEquals('source_id', $rel->get_source_field_name());
		$this->assertEquals('target_id', $rel->get_target_field_name());
	}

	public function test_reverse() {
		$rel = new DBFieldRelation('source_id', 'target_id');
		$reversed = $rel->reverse();

		$this->assertEquals('target_id', $reversed->get_source_field_name());
		$this->assertEquals('source_id', $reversed->get_target_field_name());

		// Original unchanged
		$this->assertEquals('source_id', $rel->get_source_field_name());
		$this->assertEquals('target_id', $rel->get_target_field_name());
	}

	public function test_reverse_twice() {
		$rel = new DBFieldRelation('a', 'b');
		$double_reversed = $rel->reverse()->reverse();
		$this->assertEquals('a', $double_reversed->get_source_field_name());
		$this->assertEquals('b', $double_reversed->get_target_field_name());
	}
}
