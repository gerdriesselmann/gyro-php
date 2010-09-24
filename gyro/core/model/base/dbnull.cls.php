<?php
/**
 * A class to mark something as NULL explicitely
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBNull {	
    public function __toString() {
    	return 'NULL';
    }	
}
