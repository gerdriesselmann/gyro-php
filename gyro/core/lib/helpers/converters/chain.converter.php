<?php
/**
 * A Converter Chain
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class ConverterChain implements IConverter {
	protected array $converters = array();
	protected array $params = array();
	
	public function encode(mixed $value, mixed $params = false): mixed {
		reset($this->params);
		foreach($this->converters as $c) {
			$p = current($this->params);
			$value = $c->encode($value, $p);
			next($this->params);
		}
		return $value;
	}
	
	public function decode(mixed $value, mixed $params = false): mixed {
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
	public function append(IConverter $converter, mixed $params = false): void {
		$this->converters[] = $converter;
		$this->params[] = $params;
	}
} 


