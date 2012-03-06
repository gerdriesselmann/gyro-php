<?php
/**
 *
 */
class UpdateCommandTest extends GyroUnitTestCase {
	public function test_update_command() {
		$dao = DB::create('teacherstest');
		$dao->id = 18;
		$params = array(
			'name' => 'Stocker',
			'description' => new DBNull()
		);

		$conn = $dao->get_table_driver();
		$cmd = CommandsFactory::create_command($dao, 'update', $params);
		$this->assertStatusSuccess($cmd->execute());
		$this->assertEqual(
			"UPDATE `db`.`teacherstest` AS `teacherstest` SET `teacherstest`.`name` = 'Stocker', `teacherstest`.`description` = NULL WHERE (((`teacherstest`.`id` = 18)))",
			$conn->queries[0]
		);

	}
}