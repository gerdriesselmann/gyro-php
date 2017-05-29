<?php
/**
 * Facade class for binaries
 * 
 * @author Gerd Riesselmann
 * @ingroup Binaries
 */
class Binaries {
	/**
	 * Returns binary with given ID
	 *
	 * @param int $id
	 * @return DAOBinaries
	 */
	public static function get($id) {
		return DB::get_item('binaries', 'id', $id);	
	}
	
	/**
	 * Returns content of binary with given ID
	 *
	 * @param int $id
	 * @return string
	 */
	public static function read_content($id) {
		$ret = '';
		/* @var $b DAOBinaries */
		$b = self::get($id);
		if ($b) {
			$ret = $b->get_data();
		}
		return $ret;
	}
	
	/**
	 * Returns true if the data passed represents an upload.
	 * 
	 * This is: The data is an array, and the error is  not UPLOAD_ERR_NO_FILE 
	 *
	 * @param array $data
	 */
	public static function is_upload($data) {
		$ret = false;
		if (is_array($data)) {
			$ret = (Arr::get_item($data, 'error', UPLOAD_ERR_NO_FILE) != UPLOAD_ERR_NO_FILE);
		}
		return $ret;
	}
	
	/**
	 * Create a new binary instance
	 *
	 * @param string $data
	 * @param string $name
	 * @param string $mime_type
	 * @param DAOBinaries $created
	 * @return Status
	 */
	public static function create($data, $name, $mime_type, &$created) {
		$ret = new Status();
		$params = array(
			'name' => $name,
			'mimetype' => $mime_type,
			'data' => $data
		);
		$cmd = CommandsFactory::create_command('binaries', 'create', $params);
		$ret->merge($cmd->execute());
		$created = $cmd->get_result();
		return $ret; 
	}
	
	/**
	 * Create a binary from a file
	 *
	 * @param string $file File name to load from 
	 * @param string $name Name for new binary 
	 * @param string $mime_type Mime type. Leave empty for auto detection
	 * @param DAOBinaries $created The created instances
	 * @return Status
	 */
	public static function create_from_file($file, $name, $mime_type, &$created) {
		$ret = new Status();
		if (file_exists($file)) {
			if (empty($mime_type)) {
				$mime_type = 'application/octect-stream';
				if (function_exists('finfo_open')) {
					$handle = finfo_open(FILEINFO_MIME); // return mime type ala mimetype extension
					$mime_type = finfo_file($handle, $file);
					finfo_close($handle);
				}
				else if (function_exists('mime_content_type')) {
					$mime_type = mime_content_type($file);
				}
			}
			$ret->merge(self::create(file_get_contents($file), $name, $mime_type, $created));
		}
		else {
			$ret->append(tr('File %f not found', 'binaries', array('%f' => $file)));
		}
		return $ret;
	}
	
	/**
	 * Create a binary from POST data
	 * 
	 * POST date MUST! be retrieved from $page_data->get_post()
	 * 
	 * If no file was uploaded, an error gets returned
	 * 
	 * @param array $post_item 
	 * @param DAOBinaries $created
	 * @return Status
	 */
	public static function create_from_post($post_item, &$created) {
		$ret = new Status();
		$i_err = Arr::get_item($post_item, 'error', UPLOAD_ERR_NO_FILE);
		switch ($i_err) {
			case UPLOAD_ERR_OK:
				$tmp_file = Arr::get_item($post_item, 'tmp_name', '');
				$org_file = Arr::get_item($post_item, 'name', '');
				$mime = Arr::get_item($post_item, 'type', '');
				$ret->merge(Binaries::create_from_file($tmp_file, $org_file, $mime, $created));
				break;		
			case UPLOAD_ERR_NO_FILE:
				$ret->append(tr('No file was uploaded', 'binaries'));
				break;				
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				$ret->append(tr('The uploaded file is too big', 'binaries'));
				break;
			default:
				$ret->append(tr('An unknown error code was retrieved while uploading the file.', 'binaries'));
				break;
		}
		return $ret;
	}
}
