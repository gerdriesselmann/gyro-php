<?php
/**
 * A Converter Chain
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class ConverterChain implements IConverter {
	protected $converters = array();
	protected $params = array();
	
	public function encode($value, $params = false) {
		reset($this->params);
		foreach($this->converters as $c) {
			$p = current($this->params);
			$value = $c->encode($value, $p);
			next($this->params);
		}
		return $value;
	}
	
	public function decode($value, $params = false) {
		reset($this->params);
		foreach($this->converters as $c) {
			$p = current($this->params);
			$value = $c->decode($value, $p);
			next($this->params);
		}
		return $value;
	} 	
	
	/**
	 * Append a converter to the chain
	 * 
	 * @param IConverter $converter The converter
	 * @param mixed $params The converters params
	 */
	public function append(IConverter $converter, $params = false) {
		$this->converters[] = $converter;
		$this->params[] = $params;
	}
} 


