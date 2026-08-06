<?php
use PHPUnit\Framework\TestCase;

class StatusTest extends TestCase {
	public function test_construct(): void {
		$s = new Status();
		$this->assertTrue($s->is_ok());

		$s = new Status('');
		$this->assertTrue($s->is_ok());

		$s = new Status('message');
		$this->assertFalse($s->is_ok());
		$this->assertEquals('message', $s->to_string());

		$s = new Message('message');
		$this->assertTrue($s->is_ok());
		$this->assertEquals('message', $s->to_string());
	}

	public function test_append(): void {
		$s = new Status();
		$this->assertTrue($s->is_ok());

		$s->append('message1');
		$this->assertFalse($s->is_ok());
		$this->assertEquals('message1', $s->to_string());

		$s->append('message2');
		$this->assertFalse($s->is_ok());
		$this->assertEquals('message1<br />message2', $s->to_string());

		$s = new Message('message1');
		$this->assertTrue($s->is_ok());
		$this->assertEquals('message1', $s->to_string());

		$s->append('message2');
		$this->assertTrue($s->is_ok());
		$this->assertEquals('message1<br />message2', $s->to_string());
	}

	public function test_merge(): void {
		$s = new Status();
		$s2 = new Status();

		$s->merge($s2);
		$this->assertTrue($s->is_ok());

		$s2->append('message1');
		$s->merge($s2);
		$this->assertFalse($s->is_ok());
		$this->assertEquals('message1', $s->to_string());

		$s->merge($s2);
		$this->assertFalse($s->is_ok());
		$this->assertEquals('message1', $s->to_string()); // Messages are unique

		// Exception
		$s = new Status();
		$s2 = new \Exception('message1');
		$s->merge($s2);
		$this->assertFalse($s->is_ok());
		$this->assertEquals('message1', $s->to_string());

		// String
		$s = new Status();
		$s->merge('message1');
		$this->assertFalse($s->is_ok());
		$this->assertEquals('message1', $s->to_string());

		$s->merge('message2');
		$this->assertEquals('message1<br />message2', $s->to_string());
	}

	public function test_to_string(): void {
		$s = new Status();
		$this->assertEquals('', $s->to_string());
		$this->assertEquals('', $s->to_string(Status::OUTPUT_PLAIN));

		$s = new Status('message1');
		$this->assertEquals('message1', $s->to_string());
		$this->assertEquals('message1', $s->to_string(Status::OUTPUT_PLAIN));

		$s->append('<script>');
		$this->assertEquals('message1<br />&lt;script&gt;', $s->to_string());
		$this->assertEquals("message1\n<script>", $s->to_string(Status::OUTPUT_PLAIN));
	}

	public function test_render_empty(): void {
		$s = new Status();
		$this->assertEquals('', $s->render());
		$this->assertEquals('', $s->render(Status::OUTPUT_PLAIN));
	}
}
