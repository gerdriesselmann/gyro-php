<?php
class StatusTest extends GyroUnitTestCase {
	public function test_contruct() {
		$s = new Status();
		$this->assertStatusSuccess($s);
		
		$s = new Status('');
		$this->assertStatusSuccess($s);
		
		$s = new Status('message');
		$this->assertStatusError($s);
		$this->assertEqual('message', $s->to_string());
		
		$s = new Message('message');
		$this->assertStatusSuccess($s);
		$this->assertEqual('message', $s->to_string());
	}
	
	public function test_append() {
		$s = new Status();
		$this->assertStatusSuccess($s);
		
		$s->append('message1');
		$this->assertStatusError($s);
		$this->assertEqual('message1', $s->to_string());

		$s->append('message2');
		$this->assertStatusError($s);
		$this->assertEqual('message1<br />message2', $s->to_string());
		
		$s = new Message('message1');
		$this->assertStatusSuccess($s);
		$this->assertEqual('message1', $s->to_string());
		
		$s->append('message2');
		$this->assertStatusSuccess($s);
		$this->assertEqual('message1<br />message2', $s->to_string());
	}

	public function test_merge() {
		$s = new Status();
		$s2 = new Status();
		
		$s->merge($s2);
		$this->assertStatusSuccess($s);
		
		$s2->append('message1');
		$s->merge($s2);
		$this->assertStatusError($s);
		$this->assertEqual('message1', $s->to_string());

		$s->merge($s2);
		$this->assertStatusError($s);
		$this->assertEqual('message1<br />message1', $s->to_string());

		// Exception
		$s = new Status();
		$s2 = new Exception('message1');
		
		$s->merge($s2);
		$this->assertStatusError($s);
		$this->assertEqual('message1', $s->to_string());

		$s->merge($s2);
		$this->assertStatusError($s);
		$this->assertEqual('message1<br />message1', $s->to_string());
		
		// String
		$s = new Status();
		
		$s->merge('message1');
		$this->assertStatusError($s);
		$this->assertEqual('message1', $s->to_string());

		$s->merge('message1');
		$this->assertStatusError($s);
		$this->assertEqual('message1<br />message1', $s->to_string());
	}
	
	public function test_to_string() {
		$s = new Status();
		$this->assertEqual('', $s->to_string());
		$this->assertEqual('', $s->to_string(Status::OUTPUT_PLAIN));
		
		$s = new Status('message1');
		$this->assertEqual('message1', $s->to_string());
		$this->assertEqual('message1', $s->to_string(Status::OUTPUT_PLAIN));
		
		$s->append('<script>');
		$this->assertEqual('message1<br />&lt;script&gt;', $s->to_string());
		$this->assertEqual("message1\n<script>", $s->to_string(Status::OUTPUT_PLAIN));
	}
	
	public function test_render() {
		$s = new Status();
		$this->assertEqual('', $s->render());
		$this->assertEqual('', $s->render(Status::OUTPUT_PLAIN));

		$s = new Status('message1');
		$this->assertEqual(html::error('message1'), $s->render());
		$this->assertEqual('message1', $s->to_string(Status::OUTPUT_PLAIN));

		$s = new Message('message1');
		$this->assertEqual(html::success('message1'), $s->render());
		$this->assertEqual('message1', $s->to_string(Status::OUTPUT_PLAIN));
	}
}