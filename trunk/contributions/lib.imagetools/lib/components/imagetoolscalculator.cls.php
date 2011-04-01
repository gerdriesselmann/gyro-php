<?php
Load::components('imagetoolsrect');

/**
 * Offers some often used calculations for image resizing et al. 
 */
class ImageToolsCalculator {
	/**
	 * Fit source into destination widthout changing ratio
	 * 
	 * Example: Fit 200x100 into 100x100 will result in 100x50
	 * 
	 * @param int $src_width Source width
	 * @param int $src_height Source height
	 * @param int $dst_width Destination width
	 * @param int $dst_height Destiantion height
	 * 
	 * @return ImageToolsRect
	 */
	public static function fit($src_width, $src_height, $dst_width, $dst_height) {
		$w_target = min($src_width, $dst_width);
		$h_target = min($src_height, $dst_height);
		
		$x_ratio = $w_target / $src_width;
		$y_ratio = $h_target / $src_height;
		$ratio = min($x_ratio, $y_ratio);
		
		$w_target = intval($src_width * $ratio);
		$h_target = intval($src_height * $ratio);
		
		return new ImageToolsRect($w_target, $h_target);		
	}
	
	/**
	 * Center an image 
	 *
	 * @param int $src_width Source width
	 * @param int $src_height Source height
	 * @param int $dst_width Destination width
	 * @param int $dst_height Destiantion height
	 * 
	 * @return ImageToolsRect
	 */
	public static function center($src_width, $src_height, $dst_width, $dst_height) {
		$x_target = ($dst_width - $src_width) / 2;
		$y_target = ($dst_height - $src_height) / 2; 

		return new ImageToolsRect($src_width, $src_height, $x_target, $y_target);
	}
}