<?php
/**
 * Base class for all Data Access Objects. 
 * 
 * Behaves somewhat like DB_Dataobject
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DataObjectBase implements IDataObject, IActionSource {
 	/**
 	 * Table for this DataObject
 	 *
 	 * @var IDBTable
 	 */
	protected $table = null;
 	/**
 	 * Result of Select query
 	 *
 	 * @var IDBResultSet
 	 */
	protected $resultset = null;
	/**
	 * Where to collect where statements
	 *
	 * @var DBWhereGroup
	 */
	protected $where = null;
	/**
	 * A limit 
	 *
	 * @var array
	 */
	protected $limit = array(0,0);
	protected $order_by = array();
	/**
	 * Array of associative array witj keys 'table' and 'conditions'
	 *
	 * @var array
	 */
	protected $joins = array();
	protected $queryhooks = array();
	
 	
 	public function __construct() {
 		$this->table = $this->init_table_object();
 		$this->where = new DBWhereGroup($this); 
 	}
 	
 	/**
 	 * Init table (create, retrieve) DBTable instance
 	 * 
 	 * @return IDBTable
 	 */
 	protected function init_table_object() {
 		// we assume we are named 'DAOtablename'
 		$name = strtolower(get_class($this));
 		if (substr($name, 0, 3) == 'dao') {
 			$name = substr($name, 3); 	
 		}
 		$ret = DBTableRepository::get($name);
 		if (empty($ret)) {
 			$ret = $this->create_table_object();
 			DBTableRepository::register($ret, $name);
 		}
 		return $ret;
 	}
 	
 	public function __destruct() {
 		if ($this->resultset) {
 			$this->resultset->close();
 		}
 		unset($this->resultset);
 		unset($this->table);
 		unset($this->where);
 	}
 	
 	public function __clone() {
 		$this->resultset = null;
 		$this->where = new DBWhereGroup($this);
 		$this->limit = array(0,0);
 		$this->order_by = array();
 	}
 	
    public function __sleep() {
        $properties = get_object_vars($this);
        foreach($this->get_properties_to_exclude_for_sleep() as $prop) {
        	unset($properties[$prop]);
        }
        return array_keys($properties);
    }
    
    protected function get_properties_to_exclude_for_sleep() {
    	return array('table', 'where', 'limit', 'order_by', 'resultset', 'joins');
    }

    public function __wakeup() {
 		$this->table = $this->create_table_object();
 		$this->where = new DBWhereGroup($this);
    } 	
    
    public function __toString() {
    	return $this->to_string();
    }
    
    public function to_string() {
		$ret = '';
		$fields = array();
		foreach($this->get_table_keys() as $key_field) {
			/* @var $key_field DBField */
			$col_name = $key_field->get_field_name(); 
			$fields[$col_name] = $this->$col_name;
		}
		
		$ret .= $this->get_table_name();
		if (count($fields)) {
			$ret .= '[' . Arr::implode('|', $fields, '=') . ']';	
		}
		
		return $ret;
    }

 	// ----------------------------------------
 	// Interface IDataObject
 	// ----------------------------------------
 	
	/**
	 * Sets all default values on instance. Previous values are overwritten! 
	 */
	public function set_default_values() {
 		foreach($this->get_table_fields() as $column => $field) {
 			$this->$column = $field->get_field_default();
 		}
 	}
    
 	/**
 	 * Either update or insert object
 	 * 
 	 * This function handles autoupdating IDs
 	 * 
 	 * @return Status
 	 */
 	public function save() {
 		$bInsert = true;
		foreach($this->get_table_keys() as $column => $field) {
 			$bInsert = $bInsert && empty($this->$column);
 		}
 		if ($bInsert) {
 			return $this->insert();
 		}
 		else {
 			return $this->replace();
 		} 		
 	}
 	
 	/**
 	 * Insert data. Autoincrement IDs will be automatically set.
 	 * 
 	 * @return Status
 	 */
 	public function insert() {
 		$query = $this->create_insert_query();
 		$connection = $query->get_table()->get_table_driver();
		$ret = DB::execute($query->get_sql(), $connection);
		// find insert id
		$this->set_last_insert_id($connection);
		return $ret;
 	}
 	
 	/**
	 * Set the last insert id if there is an autoincrement INTEGER field
 	 */
 	protected function set_last_insert_id($connection) {
		// find insert id
		$table_keys = $this->get_table_keys();
		$id_field = array_shift($table_keys);
		if ($id_field && $id_field instanceof DBFieldInt && $id_field->has_policy(DBFieldInt::AUTOINCREMENT)) {
			$fieldname = $id_field->get_field_name();
			if (empty($this->$fieldname)) {
				$this->$fieldname = DB::last_insert_id($connection);
			}
		}
 	}
 	
 	/**
 	 * Returns aoociative array of fields and values
 	 */
 	protected function get_field_values($only_set = true) {
 		$b_all = !$only_set; 
 		$ret = array();
 		foreach($this->get_table_fields() as $column => $field) {
 			if ($b_all || isset($this->$column)) {
				$v = $this->$column;
				if ($v instanceof DBNull) {
					$v = NULL;
				}
 				$ret[$column] = $v;
 			}
 		}
 		return $ret;
 	}
 	
 	/**
 	 * Inserts if data does not exist, updates else
 	 * 
 	 * This function queries the database, that is it does not use MySQL "replace into"
 	 * It does not handle autoupdated ids. That is: It only works if all keys are set
 	 * 
 	 * @return Status 
 	 */
 	public function replace() {
 		$arr_keys = $this->get_table_keys();
 		$b_keys_complete = true;
 		foreach($arr_keys as $key_column => $field) {
 			// All keys empty => Insert
 			if (empty($this->$key_column)) {
 				$b_keys_complete = false;
 				break;
 			}
 		}
		
		// Only run if all keys are set
 		if (!$b_keys_complete) {
 			return new Status(tr(
 			 	'Replace cannot be called with a key beeing empty',
 				'core' 
 			));
 		}

 		$ret = new Status();
 		$driver = $this->get_table_driver();
 		if ($driver->has_feature(IDBDriver::FEATURE_REPLACE)) {
 			$query = $this->create_replace_query();
 			$ret->merge(DB::execute($query, $driver));
 		}
		else {
			$ret->merge($this->replace_manual());
		}
 		return $ret;
 	}

 	/**
 	 * Inserts if data does not exist, updates else
 	 * 
 	 * This function queries the database, that is it does not use MySQL "replace into"
 	 * It does not handle autoupdated ids. That is: It only works if all keys are set
 	 * 
 	 * @return Status 
 	 */
 	protected function replace_manual() {
 		$arr_keys = $this->get_table_keys();
 		
		// Retrieve old value
		$query = new DBQuerySelect($this, DBQuerySelect::FOR_UPDATE);
		foreach($arr_keys as $key_column => $field) {
 			$query->add_where($key_column, '=', $this->$key_column);
 		} 		
 		$result = DB::query($query->get_sql(), $query->get_table()->get_table_driver());

 		$ret = $result->get_status();
 		if ($ret->is_ok()) {
 			if ($result->get_row_count() > 0) {
				$ret->merge($this->update());	 			
 			}
 			else {
 				$ret->merge($this->insert());
 			}
 		}
 		return $ret;
 	}
 	
 	/**
 	 * Update current item
 	 * 
 	 * @param int $policy If DBDataObject::WHERE_ONLY is used, no conditions are build automatically
 	 * @return Status
 	 */
 	public function update($policy = self::NORMAL) {
 		$query = $this->create_update_query($policy);
 		return DB::execute($query->get_sql(), $query->get_table()->get_table_driver());	
 	}
 	
 	/**
 	 * Delete this object
 	 * 
 	 * @param int $policy If DBDataObject::WHERE_ONLY is used, no conditions are build automatically
 	 * @return Status
 	 * 
 	 */
 	public function delete($policy = self::NORMAL) {
 		$query = $this->create_delete_query($policy);
    	return DB::execute($query->get_sql(), $query->get_table()->get_table_driver());	
 	}
	 	
 	/**
 	 * read properties of object form array
 	 * 
 	 * @param array $values Associative array with property name as key and property value as value 
 	 */
 	public function read_from_array($values) {
 		foreach($this->get_table_fields() as $prop => $field) {
 			$value = $field->read_from_array($values);
 			if (!is_null($value)) {
 				$this->$prop = $value;
 			}
 		}
 	}
 	
 	/**
	 * Removes all properties from array that are marked using DBField::INTERNAL
	 * 
	 * @param array $arr_properties Associative array with field name as key 
	 * @return array The cleaned array 
 	 */
 	public function unset_internals($arr_properties) {
 		$ret = array();
 		// Copy only elements that are not INTERNAL
 		foreach($arr_properties as $field => $value) {
 			$dbfield = $this->get_table_field($field);
 			if ($dbfield && $dbfield->has_policy(DBField::INTERNAL)) {
 				continue;
 			}
 			$ret[$field] = $value;
 		}
 		// Remove primary keys from cleaned array
 		foreach($this->get_table_keys() as $field => $tmp) {
 			unset($ret[$field]);
 		}
 		return $ret;
 	}
 	
 	/**
 	 * Validate this object
 	 * 
 	 * @return Status Error
 	 */
 	public function validate() {
 		$ret = new Status();
 		$fields = $this->get_field_values(false);
 		foreach($this->get_table_fields() as $column => $field) {
 			$val = Arr::get_item($fields, $column, null);
 			$ret->merge($field->validate($val));
 		}
 		if ($ret->is_ok()) {
	 		// Validate relations
	 		foreach($this->get_table_relations() as $relation) {
 				/* @var $relation IDBRelation */
 				$arr_val = array();
	 			foreach($relation->get_fields() as $column => $fieldrelation) {
	 				// TODO refactor this
	 				$arr_val[$column] = Arr::get_item($fields, $column, null);	
	 			}
	 			$ret->merge($relation->validate($arr_val));
	 		}
	 		foreach($this->get_table_constraints() as $constraint) {
	 			/* @var $constraint IDBConstraint */
	 			$arr_val = array();
	 			$arr_keys = array();
				foreach($constraint->get_fields() as $column) {
					$arr_val[$column] = Arr::get_item($fields, $column, null);		
				}
				foreach($this->get_table_keys() as $column => $field_object) {
					$arr_keys[$column] = Arr::get_item($fields, $column, null);
				}
				$ret->merge($constraint->validate($arr_val, $arr_keys));
	 		}
 		}
 		return $ret;
 	}

 	/**
 	 * Finds and fetches first result of a select for $value on $column_or_value. 
 	 * 
 	 * If $value is empty, this function assumes $column_or_value contains the value of first key column
 	 *
 	 * @param mixed $column_or_value
 	 * @param mixed $value
 	 * @return Bool. True on success, false, if no record was found
 	 */
 	public function get($column_or_value, $value = null) {
 		if (empty($value)) {
 			$value = $column_or_value;
 			$keys = array_keys($this->get_table_keys());
 			$column_or_value = Arr::get_item($keys, 0, false);	
 		}
 		if (empty($column_or_value)) {
 			throw new Exception(tr('No column set for get', 'core'));
 		}
 		
    	$query = new DBQuerySelect($this);
    	$query->add_where($column_or_value, '=', $value);
    	$query->set_limit(1);

    	$this->resultset = DB::query($query->get_sql(), $query->get_table()->get_table_driver());
    	return $this->fetch();
 	} 	
 	
 	/**
     * Find results, either normal or crosstable
     *
     * for example
     *
     * $object = new mytable();
     * $object->ID = 1;
     * $object->find();
     *
     * @param int $policy If set to DataObejctBase::AUTOFETCH, first record is fetched automatically
     * @return int Number of rows found
     */
    public function find($policy = self::NORMAL) {
    	$query = $this->create_select_query();
    	return $this->query($query->get_sql(), $policy);
    }
 	
	/**
	 * Return array of elements, based on current configuration
	 * 
	 * @return array Array of IDataObject
	 */
	public function find_array() {
		$this->find();
		return $this->fetch_array();
	}

    /**
     * fetches next row into this objects var's
     *
     * returns true on success false on failure
     *
     * Example
     * $object = new mytable();
     * $object->name = "fred";
     * $object->find();
     * $store = array();
     * while ($object->fetch()) {
     *   echo $this->ID;
     *   $store[] = $object; // builds an array of object lines.
     * }
	 *
     * @return boolean True on success
     */
    public function fetch() {
    	$ret = false;
    	if (!empty($this->resultset)) {
    		$arr_data = $this->resultset->fetch();
    		if ($arr_data) {
    			foreach($arr_data as $prop => $value) {
    				$field = $this->get_table_field($prop);
    				if ($field) {
    					$value = $field->convert_result($value);
    				}
    				$this->$prop = $value;
    			}
    			$ret = true;
    		}
    		else {
    			$this->resultset = null;
    		}
    	}
    	return $ret;
    }

	/**
 	 * Fetch and return an array
 	 * 
 	 * @return array Array of IDataObject
 	 */
 	public function fetch_array() {
		$arrRet = array();
		while ($this->fetch()) {
			$arrRet[] = clone($this);
		}
	 	
	 	return $arrRet; 		
 	}
  	
	/**
	 * Adds where to this
	 * 
	 * Example:
	 * 
	 * $object = new mytable();
	 * $object->add_where('id', '>', 3);
	 * $object->add_where('name', 'in', array('Hans', 'Joe'));
	 * $object->add_where("(email like '%provider1%' or email like '%provider2%')");
	 * 
	 * Will query using the following SQL:
	 * 
	 * SELECT * FROM mytable WHERE (id > 3 AND name IN ('Hans', 'Joe') AND (email like '%provider1%' or email like '%provider2%'));
	 * 
	 * @param string $column Column to query upon, or a full sql where statement
	 * @param string $operator Operator to execute
	 * @param mixed $value Value(s) to use
	 * @mode string Either DBWhere::AND or DBWhere::OR
	 * 
	 */
	public function add_where($column, $operator = null, $value = null, $mode = 'AND') {
		$this->where->add_where($column, $operator, $value, $mode);
	}

	/**
	 * Adds IDBWhere instance to this
	 */
	public function add_where_object(IDBWhere $where) {
		$this->where->add_where_object($where);	
	}

	/**
	 * Returns root collection of wheres
	 *
	 * @return DBWhereGroup
	 */
	public function get_wheres() {
		return $this->where;
	}		
	
	/**
	 * Join another table to this
	 *
	 * @param IDBTable $other
	 * @param array $conditions Array of DBJoinCondition. When ommited Gyro will figure out join conditions by itself
	 * @param string $type One of DBQueryJoined::INNER, DBQueryJoined::LEFT, or DBQueryJoined::RIGHT  
	 */
	public function join(IDBTable $other, $conditions = array(), $type = DBQueryJoined::INNER) {
		$this->joins[] = array(
			'table' => $other,
			'conditions' => Arr::force($conditions, false),
			'type' => $type
		);	 
	}
	
 	/**
 	 * Quote and escape a string
 	 * 
 	 * @param string $val 
 	 * @return string
 	 */
 	public function quote($val) {
		return DB::quote($val, $this->get_table_driver());
 	}
 	
 	/**
 	 * Returns true if a both this and the given object refer to the same instance in the DB
 	 * 
 	 * The codes tests if table and key fields are identical. Note that this function 
 	 * returns true even if some properties are not identical. Use equals() to test this
 	 * 
 	 * @param IDataObject $other
 	 * @return bool 
 	 */
 	public function is_same_as($other) {
 		$ret = ($other instanceof IDataObject);
 		$ret = $ret && ($this->get_table_name() === $other->get_table_name());
 		if ($ret) {
 			foreach($this->get_table_keys() as $name => $field_object) {
 				$ret = $ret && ($this->$name === $other->$name);
 			}
 		}
 		return $ret;
 	}
 	
 	/**
 	 * Returns true if a both this and the given object are identical
 	 * 
 	 * The codes tests if table and all fields are identical. Note that this function
 	 * may return false in cases when is_same_as() returns true. 
 	 * 
 	 * @param IDataObject $other
 	 * @return bool 
 	 */
 	public function equals($other) { 	
 		$ret = ($other instanceof IDataObject);
 		$ret = $ret && ($this->get_table_name() === $other->get_table_name());
 		if ($ret) {
 			foreach($this->get_table_fields() as $name => $field_object) {
 				$ret = $ret && ($this->$name === $other->$name);
 			}
 		}
 		return $ret;
	}

 	// ********************************************
 	// ISearchAdapter
 	// ********************************************
 	
	/**
	 * Count resulting items
	 * 
	 * @return int
	 */
	public function count() {
		$query = $this->create_count_query();
		$result = DB::query($query->get_sql(), $query->get_table()->get_table_driver());
		$ret = false;
		if ($result->get_status()->is_ok()) {
			$arr = $result->fetch();
			$ret = Arr::get_item($arr, 'c', 0);
		}
		return $ret;		
	}
	
	/**
	 * Limit result
	 */
	public function limit($start = 0, $number_of_items = 0) {
		$this->limit[0] = $start;
		$this->limit[1] = $number_of_items;
	}
	 	
 	/**
 	 * Implemented from ISearchAdapter. Synomy to find_array()
 	 * 
 	 * This function behaves like find_array(), it may however be overloaded to behave differently!
 	 */
 	public function execute() {
 		return $this->find_array();
 	}
 
	/**
	 * Return array of sortable columns. Array has column name as key and some sort of sort-column-object or an array as values  
	 */
	public function get_sortable_columns() {
		return array();
	}

	/**
	 * Get the column to sort by default
	 */
	public function get_sort_default_column() {
		return '';
	}
	
	/**
	 * Sort result by colum
	 * 
	 * @param String column name
	 * @param Enum 'asc' or 'desc'
	 */ 
	public function sort($column, $order = self::ASC) {
		if ($column == self::CLEAR) {
			$this->order_by = array();
		}
		else {
			$this->order_by[] = array($column => $order);
		}
	} 

	/**
	 * Return array of filters. Array has filter as key and a readable description as value  
	 */
	public function get_filters() {		
		return false;
	}
	
	/**
	 * Apply a modifier
	 * 
	 * @param IDBQueryModifier The modifier to be applied
	 */
	public function apply_modifier($modifier) {
		if ($modifier) {
			$modifier->apply($this);
		}
	} 
	
	/**
	 * Set a table alias for this instance
	 */
	public function set_table_alias($alias) {
		$this->table = new DBTableAlias($this->table, $alias);
	}
	
	// *************************************
	// IDBTable
	// *************************************
	
	/**
	 * Returns name of table
	 * 
	 * @return string
	 */
	public function get_table_name() {
		return $this->table->get_table_name();
	}

	/**
	 * Returns alias of table, if any
	 * 
	 * @return string
	 */
	public function get_table_alias() {
		return $this->table->get_table_alias();
	}
	
	/**
	 * Returns name of table, but escaped
	 * 
	 * @return string
	 */
	public function get_table_name_escaped() {
		return $this->table->get_table_name_escaped();
	}

	/**
	 * Returns alias of table, if any - but escaped
	 * 
	 * @return string
	 */
	public function get_table_alias_escaped() {
		return $this->table->get_table_alias_escaped();
	}	
	
	/**
	 * Returns array of columns
	 * 
	 * @return array Associative array with column name as key and IDBField instance as value 
	 */
 	public function get_table_fields() {
		return $this->table->get_table_fields(); 		
 	}
 	
 	/**
 	 * Returns field fpr given column
 	 *
 	 * @param string $column Column name
 	 * @return IDBField Either field or false if no such field exists
 	 */
 	public function get_table_field($column) {
		return $this->table->get_table_field($column); 		
 	}
 	
 	/**
 	 * Returns array of keys
 	 * 
 	 * @return array Associative array with column name as key and IDField instance as value
 	 */
 	public function get_table_keys() {
		return $this->table->get_table_keys(); 		
 	}

  	/**
 	 * Returns array of relations 
 	 * 
 	 * @return array Array with IDBRelation instance as value 
 	 */
 	public function get_table_relations() {
 		return $this->table->get_table_relations();
 	}

 	/**
 	 * Returns relations between two tables
 	 *
 	 * @param IDBTable $other
 	 * @return array Array of IDBRelations
 	 */
 	public function get_matching_relations(IDBTable $other) {
		return $this->table->get_matching_relations($other);
 	} 	

 	/**
 	 * Returns array of constraints
 	 * 
 	 * @return array Array with IDBConstraint instance as value 
 	 */
 	public function get_table_constraints() {
 		return $this->table->get_table_constraints();
 	}
 	
 	/**
 	 * Returns DB driver fro this table
 	 *  
 	 * @return IDBDriver
 	 */
 	public function get_table_driver() {
 		return $this->table->get_table_driver();
 	} 	 	
 	 	
	// **********************************
	// IActionSource
	// **********************************
	
	/**
	 * Get all actions
	 *
	 * This function sends first calls get_actions_for_context() on this instance and afterwards
	 * invokes an event "get_actions". Clients may overload, extend or delete actions by returning 
	 * an array with according key.
	 * 
	 * Actions may be passed in two ways. Either as an instance of IAction (which also includes commands) 
	 * or as a string. In this case the array key is the action (or command) name, the value is the description 
	 * that will get displayed. For Commands, however, the command's description may be used.
	 * 
	 * Optionally, params can be added in brackets like 'status[DISABLED]' => 'Disable this item'.
	 * 
	 * All actions will get access checked, and removed if access is denied 
	 * 
	 * @param mixed "Access Request Object", e.g. a user 
	 * @param String The context. Some actions may not be approbiate in some situations. For example, 
	 *               action 'edit' should not be returned when editing. This can be expressed through a 
	 *               context named 'edit'. Default context is 'view'.
	 * @param mixed  Any params   
	 * @return Array Array of IAction instances 
	 */
	public function get_actions($user, $context = 'view', $params = false) {
		$actions = $this->get_actions_for_context($context, $user, $params);
		// Raise event to collect more actions
		$actions_event_result = array();
		$actions_event_params = array(
			'source' => $this,
			'source_name' => $this->get_action_source_name(),
			'context' => $context,
			'params' => $params,
			'instance_actions' => $actions
		);
		EventSource::Instance()->invoke_event('get_actions', $actions_event_params, $actions_event_result);
		$actions = array_merge($actions, $actions_event_result);
		
		// Delete current action
		unset($actions[$context]);
		$ret = array();
		foreach($actions as $name => $iaction_or_description) {
			if ($iaction_or_description instanceof IAction) {
				$ret[] = $iaction_or_description;
			}
			else {
				$description = $iaction_or_description;
				if (empty($description)) {
					continue;
				}
				$actionsource = $this;
				$action = $name;
				if (strpos($name, '::') !== false) {
					$tmp = explode('::', $name);
					$actionsource = array_shift($tmp);
					$action = implode('_', $tmp);
					$name = str_replace('::', '_', $name);
				}
				
				$cmd = $this->find_action_command($name);
				if ($cmd) {
					if ($cmd->can_execute($user)) {
						$ret[] = $cmd;
					}
				}
				else {
					if (AccessControl::is_allowed($action, $actionsource, $params, $user)) {
						$ret[] = new ActionBase($this, $name, $description);
					}
				}
			}
		}
		
		return $ret;
	}
	
	/**
	 * Identify for generic actionh processing
	 * 
	 * @return string
	 */
	public function get_action_source_name() {
		return $this->get_table_name();
	}

	/**
	 * To be overloaded. Returns array of actions with action title as key and action description as value 
	 *
	 * Subclasses can return array of actions, this class will detect if they are commands or actions.
	 * 
	 * Optionally, params can be added in brackets like 'status[DISABLED]' => 'Disable this item'.  
	 * 
	 * @param string $context
	 * @param mixed $user
	 * @param mixed $params
	 * @return array
	 */
	protected function get_actions_for_context($context, $user, $params) {
		return array();
	}

	/**
	 * Resolve action name to command
	 * 
	 * Optionally, params can be added in brackets like 'status[DISABLED]'
	 * 
	 * @param string $name
	 * @return ICommand Command, if any, false otherwise
	 */
	protected function find_action_command($name) {
		$params = false;
		$pos_params = strpos($name, '[');
		if ($pos_params !== false) {
			$params = trim(substr($name, $pos_params + 1), ']');
			$name = substr($name, 0, $pos_params);
			$params = explode(',', $params); 
		}
		$ret = CommandsFactory::create_command($this, $name, $params);
		return $ret;
	}
	
	// *************************************
	// Query Related
	// *************************************

	/**
	 * Prepare a select query
	 *
	 * @param int $policy If set to DataObjectBase::WHERE_ONLY, current values are ignored
	 * @return DBQuerySelect
	 */
	public function create_select_query($policy = self::NORMAL) {
		$query = new DBQuerySelect($this, DBQuerySelect::NONE);
    	$this->configure_select_query($query, $policy);
    	$this->execute_query_hooks($query);
    	return $query;
	}

	/**
	 * Configure a select query
	 *
	 * @param DBQuerySelect $query
	 * @param int $policy
	 */
	protected function configure_select_query($query, $policy) {
    	$query->add_where_object($this->where);
    	
    	if (!Common::flag_is_set($policy, self::WHERE_ONLY)) {
    		$recent_values = $this->get_field_values();
    		foreach($recent_values as $column => $value) {
    			if (is_null($value)) {
    				$query->add_where($column, DBWhere::OP_IS_NULL);
    			}
    			else {
    				$query->add_where($column, '=', $value);
    			}	
    		}
    	}
    	
    	$query->set_limit($this->limit[0], $this->limit[1]);
    	foreach($this->order_by as $order_by) {
    		$query->add_order(key($order_by), current($order_by));
    	}
    	
    	$use_distinct = false;
    	foreach($this->joins as $join) {
    		$joined_table = $join['table'];
    		$conditions = $join['conditions'];
    		$joined_query = $query->add_join($joined_table);
    		$joined_query->set_join_type(Arr::get_item($join, 'type', DBQueryJoined::INNER));
    		if (count($conditions) > 0) {
    			$joined_query->set_policy(DBQuery::NONE);
    			foreach($conditions as $cond) {
    				$joined_query->add_join_condition_object($cond);
    			}
    		}
    		$use_distinct = $use_distinct || ($joined_query->get_relation_type() != DBRelation::ONE_TO_ONE);
    		if ($joined_table instanceof DataObjectBase) {
    			$joined_table->configure_select_query($joined_query, self::NORMAL);
    		}
    	}
    	if ($use_distinct) {
    		$query->set_policy($query->get_policy() | DBQuerySelect::DISTINCT);
    	}
	}

	/**
	 * Prepare an insert query
	 *
	 * @return DBQueryInsert
	 */
	public function create_insert_query() {
 		$query = new DBQueryInsert($this);
 		$this->configure_insert_query($query);
 		$this->execute_query_hooks($query);
 		return $query;
	}

	/**
	 * Configure an insert query
	 *
	 * @param DBQueryInsert $query
	 */
	protected function configure_insert_query($query) {
		$fields = $this->get_field_values();
		//foreach($this->get_table_keys() as $column => $field) {
		//	unset($fields[$column]);	 		
		//}
 		
		$query->set_fields($fields);
	}

	/**
	 * Prepare an update query
	 *
	 * @param int $policy If set to DataObjectBase::WHERE_ONLY, current values are ignored
	 * @return DBQueryUpdate
	 */
	public function create_update_query($policy = self::NORMAL) {
 		$query = new DBQueryUpdate($this);
 		$this->configure_update_query($query, $policy);
 		$this->execute_query_hooks($query);
 		return $query;
	}

	/**
	 * Configure an update query
	 *
	 * @param DBQueryUpdate $query
	 * @param int $policy
	 */
	protected function configure_update_query($query, $policy) {
 		$fields = $this->get_field_values();
 		
 		$where = new DBWhereGroup($query->get_table());
 		$where->add_where_object($this->where);

 		if (!Common::flag_is_set($policy, self::WHERE_ONLY)) {
			foreach($this->get_table_keys() as $column => $field) {
				if (!isset($fields[$column])) {
					// Prevent unwanted mass updates...
					throw new Exception(tr('Trying to UPDATE without all keys set. If intended, use WHERE_ONLY policy', 'core'));
				}
				unset($fields[$column]);
				$where->add_where($column, '=', $this->$column);
			}			
 		}
 		
 		$query->add_where_object($where);
 		$query->set_fields($fields);
	}

	/**
	 * Prepare an insert query
	 *
	 * @return DBQueryInsert
	 */
	public function create_replace_query() {
 		$query = new DBQueryReplace($this);
 		$this->configure_replace_query($query);
 		$this->execute_query_hooks($query);
 		return $query;
	}

	/**
	 * Configure an insert query
	 *
	 * @param DBQueryInsert $query
	 */
	protected function configure_replace_query($query) {
		$fields = $this->get_field_values();
		$query->set_fields($fields);
	}	
	
	/**
	 * Prepare a delete query
	 *
	 * @param int $policy If set to DataObjectBase::WHERE_ONLY, current values are ignored
	 * @return DBQueryDelete
	 */
	public function create_delete_query($policy = self::NORMAL) {
 		$query = new DBQueryDelete($this);
 		$this->configure_delete_query($query, $policy);
 		$this->execute_query_hooks($query);
    	return $query;	
	}
	
	/**
	 * Configure a delete query
	 *
	 * @param DBQueryDelete $query
	 * @param int $policy
	 */
	protected function configure_delete_query($query, $policy) {
 		$where = new DBWhereGroup($query->get_table());
 		$where->add_where_object($this->where);
 		
 		if (!Common::flag_is_set($policy, self::WHERE_ONLY)) {
			$fields = $this->get_field_values();
 			foreach($this->get_table_keys() as $column => $field) {
				if (!isset($fields[$column])) {
					// Prevent unwanted mass updates...
					throw new Exception(tr('Trying to DELETE without all keys set. If intended, use WHERE_ONLY policy', 'core'));
				}
				$where->add_where($column, '=', $this->$column);
			}			
 		}
		
 		$query->add_where_object($where);
 		
 		$query->set_limit($this->limit[0], $this->limit[1]);
    	foreach($this->order_by as $order_by) {
    		$query->add_order(key($order_by), current($order_by));
    	}
	}

	/**
	 * Prepare a count query
	 *
	 * @param int $policy If set to DataObjectBase::WHERE_ONLY, current values are ignored
	 * @return DBQuerySelect
	 */
	public function create_count_query($policy = self::NORMAL) {
		$query = new DBQueryCount($this);
		$this->configure_count_query($query, self::NORMAL);
		$this->execute_query_hooks($query);
		return $query;
	}	

	/**
	 * Configure a count query
	 *
	 * @param DBQuerySelect $query
	 * @param int $policy
	 */
	protected function configure_count_query($query, $policy) {
    	$this->configure_select_query($query, $policy);
	}
	
	/**
	 * Execute a query (SELECT)
	 * 
	 * Similar to find, but excepts any string.
	 * 
	 * Do not use with INSERT, UPDATE, or similar. This can be executed using DB::execute()
	 *
	 * @param string $query
     * @param int $policy If set to DataObejctBase::AUTOFETCH, first record is fetched automatically
     * @return int Number of rows found
	 */
	public function query($query, $policy = self::NORMAL, $connection = false) {
		$connection = ($connection) ? $connection : $this->get_table_driver();
    	$this->resultset = DB::query($query, $connection);
		$ret = $this->resultset->get_row_count();
		
    	if (Common::flag_is_set($policy, self::AUTOFETCH)) {
    		$this->fetch();	
    	}
    	
    	return $ret;		
	}
	
	/**
	 * Register hook to be called for every query created
	 *
	 * @param IDataObjectQueryHook $hook
	 */
	public function register_query_hook(IDataObjectQueryHook $hook) {
		$this->queryhooks[] = $hook;
	}
	
	// *************************************
	// Helper functions
	// *************************************
	
	/**
	 * Create the table object describing this dataobejcts table
	 */
	protected function create_table_object() {
		return new DBTable('test');		
	}
	
	protected function reset() {
		$this->resultset = null;
	}
	
	/**
	 * Run hooks
	 *
	 * @param IDBQuery $query
	 */
	protected function execute_query_hooks($query) {
		foreach($this->queryhooks as $hook) {
			$hook->configure_query($query);
		}
	}
}
