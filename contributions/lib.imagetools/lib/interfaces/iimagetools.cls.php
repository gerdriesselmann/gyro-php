<?php
/**
 * Interface for image tool delegations
 * 
 * Uses either gd or imagick extension, if installed. imagick is preferred
 * 
 * @author Gerd Riesselmann
 * @ingroup ImageTools
 */
interface IImageTools {
	/**
	 * Create an IImageInformation from a file
	 * 
	 * @return IImageInformation False on failure
	 */
	public function create_from_file($file);
	
	/**
	 * Create an IImageInformation from a file
	 * 
	 * @return IImageInformation False on failure
	 */
	public function create_from_binary_data($data);
	
	/**
	 * Resize given image. Image is streched to fit
	 * 
	 * @return IImageInformation False on failure
	 */
	public function resize(IImageInformation $src, $width, $height);

	/**
	 * Resize given image. Image is not streched, so ratio is not changed.
	 * 
	 * Resizing an image of 200 x 100 to 100 x 100 will result in a image of size 100 x 50
	 * 
	 * @return IImageInformation False on failure
	 */
	public function resize_fit(IImageInformation $src, $width, $height);
	
	/**
	 * Cuts portion of image
	 * 
	 * @return IImageInformation
	 */
	public function crop(IImageInformation $src, $x, $y, $width, $height);

	/**
	 * Fit image in given height and width. If image is larger than given size, it
	 * will be downsampled. If it is smaller, it will be untouched. Ratio is not changed.
	 * Background is filled with $backgroundcolor 
	 * 
	 * @return IImageInformation False on failure
	 */
	public function fit(IImageInformation $src, $width, $height, $backgroundcolor = 0xFFFFFF);
	
	/**
	 * Add a Watermark
	 * 
	 * @param IImageInformation $src Image to add watermark to
	 * @param string $text Text of Watermark, if emtpy "© {Application Title}" is taken
	 * 
	 * @return IImageInformation
	 */
	public function watermark(IImageInformation $src, $text = false);

	/**
	 * Rotate image
	 *
	 * @param IImageInformation $src Image to rotate
	 * @param float $degrees Degrees to rotate (0-360)
	 * @param int $backgroundcolor Backgroundcolor of new portions
	 * @return IImageInformation False on failure
	 */
	public function rotate(IImageInformation $src, $degrees, $backgroundcolor = 0xFFFFFF);
}