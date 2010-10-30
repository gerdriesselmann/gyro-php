<?php
/**
 * Wrapper around common image operations based upon IMagick PECL extension
 * 
 * @see http://pecl.php.net/package/imagick
 * 
 * @author Gerd Riesselmann
 * @ingroup ImageTools
 */
class ImageToolsIMagick /* implements IImageToolsImpl */ {
	public function resize($image_data, $width, $height, $resolution = false) {
		$img = new Imagick();
		$img->readImageBlob($image_data);
		$img->resizeImage($width, $height, 0, 1, true);
		if ($resolution > 0) {
			$img->resampleImage($resolution, $resolution, 0, 1);
		}
		return new ImageInformationIMagick($img);		
	}
}

class ImageInformationIMagick implements IImageInformation {
	private $img;
	public function __construct($img) {
		$this->img = $img;
	}	
	
	/**
	 * Returns height in pixel
	 * @return int
	 */
	public function get_height() {
		return $img->getImageHeight();
	}
	
	/**
	 * Returns width in pixel
	 * @return int
	 */
	public function get_width() {
		return $img->getImageWidth();
	}
	
	/**
	 * Returns raw image data
	 * @return string
	 */
	public function get_binary_data() {
		return $img->getImageBlob();
	}
	
	/**
	 * Returns image mime type
	 * @return string
	 */	
	public function get_mime_type() {
		
	}
	
	/**
	 * Returns image file extension
	 * @return int
	 */
	public function get_extension() {
		
	}	
}
