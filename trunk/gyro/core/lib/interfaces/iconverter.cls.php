<?php
/**
 * Generic conversion interface
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface IConverter {
	public function encode($value, $params = false);
	public function decode($value, $params = false); 
}
?>