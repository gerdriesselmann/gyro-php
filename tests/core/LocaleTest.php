<?php
use PHPUnit\Framework\TestCase;

class LocaleTest extends TestCase {
	private string $old_lang;
	private string $old_charset;

	protected function setUp(): void {
		$this->old_lang = GyroLocale::get_language();
		$this->old_charset = GyroLocale::get_charset();
	}

	protected function tearDown(): void {
		GyroLocale::set_locale($this->old_lang, $this->old_charset);
	}

	public function test_set_language() {
		GyroLocale::set_language('fr');
		$this->assertEquals('fr', GyroLocale::get_language());
		GyroLocale::set_language('de');
		$this->assertEquals('de', GyroLocale::get_language());
	}

	public function test_set_charset() {
		GyroLocale::set_charset('Latin1');
		$this->assertEquals('Latin1', GyroLocale::get_charset());
		GyroLocale::set_charset('UTF-8');
		$this->assertEquals('UTF-8', GyroLocale::get_charset());
	}

	public function test_get_locales() {
		$arr = GyroLocale::get_locales('de');
		$this->assertContains('de_DE', $arr);
		$this->assertContains('de', $arr);

		$arr = GyroLocale::get_locales('fr');
		$this->assertContains('fr_FR', $arr);
		$this->assertContains('fr', $arr);

		$arr = GyroLocale::get_locales('en');
		$this->assertContains('en_US', $arr);
		$this->assertContains('en', $arr);

		$arr = GyroLocale::get_locales('pt_BR');
		$this->assertContains('pt_BR', $arr);
		$this->assertNotContains('pt_BR_PT_BR', $arr);
	}
}
