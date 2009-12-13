<?php
class WidgetInputTest extends GyroUnitTestCase {
	public function test_no_label() {
		$test = WidgetInput::output('name', tr('Zonename', 'app'), array(), WidgetInput::TEXT, false, WidgetInput::NO_LABEL | WidgetInput::NO_BREAK);
		$this->assertFalse(strpos($test, '<label'));		
		$this->assertFalse(strpos($test, '<br'));
		
		$this->assertNotIdentical(strpos($test, '<div class="label"'), false);
	}
}