<?php
/**
 * A unique constraint
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBConstraintUnique extends DBConstraint {
	/**
	 * Check if constraints are fullfiled.  
	 *
	 * @param array Associative array of form fieldname => fieldvalue
	 * @return Status
	 */
	public function validate($arr_fields, $key_fields) {
		$ret = new Status();
		
		$dao = DB::create($this->tablename);
		$query = new DBQuerySelect($dao);
		$query->set_fields(array('count(*)' => 'c'));
		
		// Set unique conditions
		$unique_fields = $this->get_fields();
		foreach($unique_fields as $col_name) {
			$col_value = Arr::get_item($arr_fields, $col_name, null);
			$query->add_where($col_name, '=', $col_value);
		}		
		
		// Add keys
		foreach($key_fields as $col_name => $col_value) {
			if (!is_null($col_value)) {
				$query->add_where($col_name, '!=', $col_value);			
			}
		}
		
		$result = DB::query($query->get_sql(), $dao->get_table_driver());
		$arr_result = $result->fetch();

		if ($arr_result['c'] > 0) {
			$num_unique_fields = count($unique_fields);
			$tr_unique_fields = array();
			foreach($unique_fields as $col_name) {
				$tr_unique_fields[] = tr($col_name, 'global'); 
			}
			$err_msg_fields = implode(', ', $tr_unique_fields);
			 
			if ($num_unique_fields == 1) {
				$ret->append(tr(
					'There are already records for the provided value of %field',
					'core',
					array(
						'%field' => $err_msg_fields
					)
				));
			}
			else {
				$ret->append(tr(
					'There are already records for the given combination of %fields',
					'core',
					array(
						'%fields' => $err_msg_fields
					)
				));				
			}
		}
		
		return $ret;
	}
}
