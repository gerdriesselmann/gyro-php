<?php
Load::commands('generics/create');

/**
 * Create a binary, including binaray data
 * 
 * @author Gerd Riesselmann
 * @ingroup Binaries
 */
class CreateBinariesBaseCommand extends CreateCommand {		
	/**
	 * Execute this command
	 */
	protected function do_execute() {
		$ret = new Status();
		
		$ret->merge(parent::do_execute());
		if ($ret->is_ok()) {
			$params = array(
				'id_binary' => $this->get_result()->id,
				'data' => Arr::get_item($this->get_params(), 'data', '')
			);
			$cmd = CommandsFactory::create_command('binariesdata', 'create', $params);
			$ret->merge($cmd->execute()); 
		}
		
		return $ret;
	}
} 
