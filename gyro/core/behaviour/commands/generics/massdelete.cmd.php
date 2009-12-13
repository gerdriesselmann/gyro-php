<?php
/**
 * Executes a mass delete
 * 
 * - Instance must be a type string
 *  - Params are set as "WHERE $field = $value" when key values pairs
 *  - IF params are DBWhereObject they get added directly  
 *  
 * @author Gerd Riesselmann
 * @ingroup Behaviour
 */
class MassDeleteCommand extends CommandTransactional {
	/**
	 * Executes commands
	 * 
	 * @return Status
	 */
	protected function do_execute() {
		$ret = new Status();
		$o = DB::create($this->get_instance());
		if ($o) {
			foreach(Arr::force($this->get_params(), false) as $key => $value) {
				if ($value instanceof IDBWhere) {
					$o->add_where_object($value);
				}
				else if ($value instanceof DBCondition) {
					$o->add_where($value->column, $value->operator, $value->value);
				}
				else if (is_int($key)) {
					$o->add_where($value);
				}
				else {
					$o->add_where($key, '=', $value);
				}
			}
			$ret->merge($o->delete(DataObjectBase::WHERE_ONLY));
		}
		else {
			$ret->append(tr('Delete Command: No valid instance type set', 'core'));
		}
		return $ret;
	}	
}