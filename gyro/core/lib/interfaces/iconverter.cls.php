<?php
/**
 * Generic conversion interface
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface IConverter {
	/**
	 * Encode (convert) the given value
	 *
	 * @param mixed $value The value to encode
	 * @param mixed $params Optional converter-specific parameters
	 * @return mixed The encoded value
	 */
	public function encode(mixed $value, mixed $params = false): mixed;

	/**
	 * Decode (reverse-convert) the given value
	 *
	 * @param mixed $value The value to decode
	 * @param mixed $params Optional converter-specific parameters
	 * @return mixed The decoded value
	 */
	public function decode(mixed $value, mixed $params = false): mixed;
}