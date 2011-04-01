<?php
/**
 * Wrapper around common image operations based upon GD
 * 
 * @author Gerd Riesselmann
 * @ingroup ImageTools
 */
class ImageToolsGD implements IImageTools {
	/**
	 * Create an IImageInformation from a file
	 * 
	 * @return IImageInformation
	 */
	public function create_from_file($file) {
		$ret = false;
		$imageinfo = getimagesize($file);
		if ($imageinfo !== false) {
			$handle = false;
			switch($imageinfo[2]) {
				case IMAGETYPE_JPEG:
					$handle = imagecreatefromjpeg($file);
					break;
				case IMAGETYPE_PNG:
					$handle = imagecreatefrompng($file);
					break;
				case IMAGETYPE_GIF:
					$handle = imagecreatefromgif($file);
					break;
			}
			if ($handle) {
				$ret = new ImageInformationGD($handle, $imageinfo[2]);
			}
		} 
		return $ret;
	}
	
	/**
	 * Create an IImageInformation from a file
	 * 
	 * @return IImageInformation
	 */
	public function create_from_binary_data($data) {
		$ret = false;
		$tmp = tempnam(Config::get_value(Config::TEMP_DIR), 'imgi');
		if ($tmp) {
			if (file_put_contents($tmp, $data) !== false) {
				$ret = $this->create_from_file($tmp);
			}
		}
		unlink($tmp);
		return $ret;
	}
	
	/**
	 * Resize given image
	 * 
	 * @return IImageInformation
	 */
	public function resize(IImageInformation $src, $width, $height) {
		$handle = imagecreatetruecolor($width, $height);
		imagecopyresampled($handle, $src->handle, 0, 0, 0, 0, $width, $height, $src->get_width(), $src->get_height());
		return new ImageInformationGD($handle, $src->type);		
	}
	
	/**
	 * Resize given image. Image is not streched, so ratio is not changed.
	 * 
	 * Resizing an image of 200 x 100 to 100 x 100 will result in a image of size 100 x 50
	 * 
	 * @return IImageInformation False on failure
	 */
	public function resize_fit(IImageInformation $src, $width, $height) {
		Load::components('imagetoolscalculator');
		$rect = ImageToolsCalculator::fit($src->get_width(), $src->get_height(), $width, $height);
		$handle = imagecreatetruecolor($rect->width, $rect->height);
		imagecopyresampled($handle, $src->handle, 0, 0, 0, 0, $rect->width, $rect->height, $src->get_width(), $src->get_height());
		return new ImageInformationGD($handle, $src->type);
	}	
	
	/**
	 * Cuts portion of image
	 * 
	 * @return IImageInformation
	 */
	public function crop(IImageInformation $src, $x, $y, $width, $height) {
		$handle = imagecreatetruecolor($width, $height);
		imagecopy($handle, $src->handle, 0, 0, $x, $y, $width, $height);
		return new ImageInformationGD($handle, $src->type);
	}
	
	/**
	 * Fit image in given height and width. If image is larger than given size, it
	 * will be downsampled. If it is smaller, it will be untouched. Background
	 * is filled with $backgroundcolor 
	 * 
	 * @return IImageInformation False on failure
	 */
	public function fit(IImageInformation $src, $width, $height, $backgroundcolor = 0xFFFFFF) {
		$handle = imagecreatetruecolor($width, $height);
		imagefill($handle, 0, 0, $backgroundcolor);
		
		Load::components('imagetoolscalculator');
		$rect = ImageToolsCalculator::fit($src->get_width(), $src->get_height(), $width, $height);
		$rect = ImageToolsCalculator::center($rect->width, $rect->height, $width, $height);
		
		imagecopyresampled($handle, $src->handle, $rect->x, $rect->y, 0, 0, $rect->width, $rect->height, $src->get_width(), $src->get_height());
		return new ImageInformationGD($handle, $src->type);
	}	
	
	/**
	 * Add a Watermark
	 * 
	 * @param IImageInformation $src Image to add watermark to
	 * @param string $text Text of Watermark, if emtpy "© {Application Title}" is taken
	 * 
	 * @return IImageInformation
	 */
	public function watermark(IImageInformation $src, $text = false) {
		if (empty($text)) {
			$text = '© ' . Config::get_value(Config::TITLE);
		}
		$w = $src->get_width();
		$h = $src->get_height();
		$size = 40;
		$font = Load::get_module_dir('lib.imagetools'). '3rdparty/arial.ttf';
		$watermark = imagecreatetruecolor($w, $h);
		imagecopy($watermark, $src->handle, 0, 0, 0, 0, $w, $h);
		$color = imagecolorallocatealpha($watermark, 0xFF, 0xFF, 0xFF, 0x70);
		if ($w >= $h) {
			// Landscape
			imagettftext($watermark, $size, 0, 3, $h - $sie - 3, $color, $font, $text);
		}
		else {
			// Portrait
			imagettftext($watermark, $size, 90, $w - $size - 3, $h - 3, $color, $font, $text);
		}		
		return new ImageInformationGD($watermark, $src->type);	
	}
}

class ImageInformationGD implements IImageInformation {
	public $handle;
	public $type;
	
	public function __construct($handle, $imagetype) {
		$this->handle = $handle;
		$this->type = $imagetype;
	}	
	
	public function __destruct() {
		@imagedestroy($this->handle);
	}
	
	/**
	 * Returns height in pixel
	 * @return int
	 */
	public function get_height() {
		return imagesy($this->handle);
	}
	
	/**
	 * Returns width in pixel
	 * @return int
	 */
	public function get_width() {
		return imagesx($this->handle);
	}
	
	/**
	 * Returns raw image data
	 * @return string
	 */
	public function get_binary_data() {
		$ret = false;
		$tmp = tempnam(Config::get_value(Config::TEMP_DIR), 'imgo');
		if ($this->save_to_file($tmp, false)) {
			$ret = file_get_contents($tmp);
		}
		unlink($tmp);
		return $ret;
	}
	
	/**
	 * Saves to given file
	 * @return bool
	 */
	public function save_to_file($file, $add_extension = true) {
		$ret = false;
		if ($add_extension) {
			$file .= '.' . $this->get_extension();
		}
		switch ($this->type) {
			case IMAGETYPE_JPEG:
				$ret = imagejpeg($this->handle, $file);
				break;
			case IMAGETYPE_PNG:
				$ret = imagepng($this->handle, $file);
				break;
			case IMAGETYPE_GIF:
				$ret = imagegif($this->handle, $file);
				break;
		}
		return $ret;
	}
	
	/**
	 * Returns image mime type
	 * @return string
	 */	
	public function get_mime_type() {
		return image_type_to_mime_type($this->type); 	
	}
	
	/**
	 * Returns image file extension (without dot)
	 * @return int
	 */
	public function get_extension() {
		switch($this->type) {
			case IMAGETYPE_JPEG:
				return 'jpg';
			case IMAGETYPE_PNG:
				return 'png';
			case IMAGETYPE_GIF:
				return 'gif';
		}
	}	
}
