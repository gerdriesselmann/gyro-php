<?php
class JCSSCompressedFiles {
	/**
	 * Find all compressed files of given type
	 */
	public static function find($type) {
		$dao = new DAOJcsscompressedfiles();
		$dao->type = $type;
		return $dao->find_array();
	}
	
	/**
	 * Update DB to trace what gets compressed 
	 * 
	 * @param string $type One of the TYPE_X constants
	 * @param string $out_filename Path of compressed file
	 * @param array $sources Input files for compression
	 * @param Status $err
	 * @return DAOJCsscompressedfiles
	 */
	public static function update_db($type, $out_filename, $sources, $err) {
		$hash = md5_file($out_filename);
		
		$filename = JCSSManager::make_relativ($out_filename);
		
		$dao = new DAOJcsscompressedfiles();
		$dao->type = $type;
		$dao->filename = $filename;
		if ($dao->find(DataObjectBase::AUTOFETCH)) {
			if ($dao->hash != $hash) {
				$dao->hash = $hash;
				$dao->sources = $sources;
				$dao->version++;
				$err->merge($dao->update());
			}
		}	
		else {
			$dao = new DAOJcsscompressedfiles();
			$dao->type = $type;
			$dao->filename = $filename;
			$dao->version = 1;
			$dao->hash = $hash;
			$dao->sources = $sources;
			$err->merge($dao->insert());	 
		}	
		
		return $dao;
	}
	
	/**
	 * Remove a compression from DB
	 */
	public static function remove($type, $out_filename) {
		$dao = new DAOJcsscompressedfiles();
		$dao->type = $type;
		$dao->filename = $out_filename;
		return $dao->delete();
	}
}