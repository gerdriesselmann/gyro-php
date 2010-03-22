<?php
/**
 * This is a class to generate so called identifrac image
 * 
 * An identifrac is similar to Don Park's "identicon", only that is uses
 * fractal functions to generate the image.
 * 
 * This code is based upon Identifrac v0.9! A Visually Unique Identification System
 * by Jesse Dubay (jesse@thefortytwo.net)
 * Once available at adb.thefortytwo.net, it now can only be retrievend from archive.org: 
 * http://web.archive.org/web/20071212034846/adb.thefortytwo.net/projects/identifrac/ 
 *
 * The code has been changed in several ways:
 * 
 * - Less options: No alpha blending, no saturation, no brightness.
 * - Use more bytes from hash so color changes from color one to color two, rather than from black to  
 *   color one
 * - Draws a border
 * - It's a class now
 * 
 * Requires the PHP GD extension to be installed 
 * @see http://php.net/manual/en/book.image.php
 * 
 * Example usage:
 * 
 * @code
 * // This could be a page called as e.g. example.php?identity=Gerd+Riesselmann 
 * $identifrac = new IdentiFrac(md5($_GET['identity']), 100);
 * $identifrac->output_image(); 
 * @endcode
 * 
 * @code
 * // Some more advanced usage, same invocation
 * $identifrac = new IdentiFrac(md5($_GET['identity']), 100);
 * $result = $identifrac->get_as_string();
 * // Do some etag checking
 * $etag = md5($result);
 * if (isset($_SERVER['IF_NONE_MATCH']) && $_SERVER['IF_NONE_MATCH'] == $etag) {
 *   if (substr(php_sapi_name(), 0, 3) == 'cgi') {
 *     	header('Status: 304 Not Modified');
 *   }
 *   else {
 *     header('HTTP/1.x 304 Not Modified');
 *   }
 *   exit; 
 * }
 * header('ETag: ' . $etag);
 * $identifrac->send_headers();
 * print $result;
 * @endcode
 * 
 * The class is overloadable in many ways, so it should be possible to change behavior 
 * without needing to touch the original code.
 * 
 * Report bugs here: http://www.gyro-php.org/bugreport.html
 * 
 * Licensed under the GPL (http://www.gnu.org/licenses/gpl.txt)
 * 
 * @author Gerd Riesselmann
 * @version 0.8
 * @ingroup IdentiFrac
 */
class IdentiFrac {
	protected $size;
	protected $hash;
	
	const ITERATIONS = 50; 

	/**
	 * Constructor
	 * 
	 * @param string $hash A Hash (SHA1, MD5) value, hexadecimal and at least 30 characters long
	 * @param int $size Size in pixel.   
	 */
	public function __construct($hash, $size = 100) {
		$this->size = $size;
		$this->hash = $hash;
	}
	
	// get_nibbles(string &$hex_stream, int $num_nibbles)
	// Pulls a few characters from a string of hex and returns an int. It modifies
	// the string in the process. Really it's more of a buffer than a stream,
	// but whatever >_>
	protected function get_nibbles(&$hex_stream, $num_nibbles) {
	    if (strlen($hex_stream) < $num_nibbles) return -1;
	    $data = hexdec(substr($hex_stream, 0, $num_nibbles));
	    $hex_stream = substr($hex_stream, $num_nibbles);
	    return $data;
	}
	
	/**
	 * Returns number of iterations
	 * 
	 * @return int
	 */
	protected function get_iterations() {
		return self::ITERATIONS;
	}

	/**
	 * Create all the colors used 
	 */
	protected function get_colors($img, &$hash) {
		$ret = array();
		$color = array();
		// Pull two RRGGBB colors from the stream
		for ($i = 0; $i < 6; $i++) {
		    $color[$i] = $this->get_nibbles($hash, 2);
		}
		// This is new code. GR
		$color_delta = array();
		for ($i = 0; $i < 3; $i++) {
			$d = 0 - $color[$i+3] * $color[$i];
		    $color_delta[$i] = $d;
		}

		// Build the color table
		$max = $this->get_iterations();
		for($i = 0; $i < $max; $i++) {
			// Tis part has been changed. GR
			$ret[] = imagecolorallocate($img, ($color[0] + ($i / $max) * $color_delta[0]) & 0x00ff,
	                                          ($color[1] + ($i / $max) * $color_delta[1]) & 0x00ff,
	                                          ($color[2] + ($i / $max) * $color_delta[2]) & 0x00ff);
		}
		
		return $ret;		
	}
	
	/**
	 * Plots the Julia Set
	 */
	protected function draw_frac($img, $img_size, $img_colors, &$hash) {
		// Pull some data out and generate a constant for the Julia polynomial
		$a = ($this->get_nibbles($hash, 6) - pow(2, 23)) / pow(2, 23); // Real coefficient
		$b = ($this->get_nibbles($hash, 6) - pow(2, 23)) / pow(2, 23); // Imaginary coefficient

		// This algorithm is heavily based off of BASIC code found at
		// http://library.thinkquest.org/26242/full/progs/a2.html
		for($screen_y = 0; $screen_y < $img_size; $screen_y++) {
		    for($screen_x = 0; $screen_x < $img_size; $screen_x++) {
		        // Map the canvas coords to complex plane, range -1.5..1.5
		        // These are our initial coordinates
		        $x = (($screen_x / $img_size) * 3) - 1.5;
		        $y = (($screen_y / $img_size) * 3) - 1.5;
		        
		        // Time to iterate.
		        $max = $this->get_iterations();
		        for ($iterations = 0; $iterations < $max; $iterations++) {
		            // If it leaves the boundary, it's not in the Julia set
		            if ($x * $x + $y * $y > 4) {
		            	break;
		            }
		            
		            // The polynomial itself
		            $new_x = $x * $x - $y * $y + $a;
		            $new_y = 2 * $x * $y + $b;
		            
		            $x = $new_x;
		            $y = $new_y;
		        }
		        // Plot the point, referencing the color table
		        imagesetpixel($img, $screen_x, $screen_y, $img_colors[$iterations]);
		    }
		}
	}
	
	/**
	 * Draw the border
	 */
	protected function draw_border($img, $img_size, &$hash) {
		$border_color = imagecolorallocate($img, $this->get_nibbles($hash, 2), $this->get_nibbles($hash, 2), $this->get_nibbles($hash, 2));
		imagerectangle($img, 0, 0, $img_size - 1, $img_size - 1, $border_color);
		imagerectangle($img, 1, 1, $img_size - 2, $img_size - 2, $border_color);
		imagerectangle($img, 2, 2, $img_size - 3, $img_size - 3, $border_color);				
	}
	
	/**
	 * Antialiasing
	 */
	protected function draw_antialiasing($img, $img_size, $final_size) {
	    // "Poor Man's Antialiasing" inspired by Don Park
	    // Take the slightly-oversized image and scale it down to the specified size.
	    $img_final = imagecreatetruecolor($final_size, $final_size);
    	imagecopyresampled(
    		$img_final, $img, 
    		0, 0, 0, 0, 
    		$final_size, $final_size, $img_size, $img_size
    	);
		return $img_final;
	}
	
	/**
	 * Create the image
	 * 
	 * @return handle Handle to image. 
	 * 
	 * @attention Calling code is resposible to call imagedestroy()!
	 */
	protected function create($final_size, $hash) {
		// We want to generate more data than necessary, so when we
		// resize the image at the very end, we have some cushion for the
		// "poor man's antialiasing" algorithm
		$img_size = $final_size * 2;
		$img = imagecreate($img_size, $img_size);
		
		$img_colors = $this->get_colors($img, $hash);
		$this->draw_frac($img, $img_size, $img_colors, $hash);
		$this->draw_border($img, $img_size, $hash);
		
		$img_final = $this->draw_antialiasing($img, $img_size, $final_size);
		imagedestroy($img);
		
		return $img_final;
	}

	/**
	 * Returns the image as a string of biaray data
	 * 
	 * @return string
	 */
	public function get_as_string() {
		$img = $this->create($this->size, $this->hash);
		$file = tempnam("/tmp", "IFR");
		imagepng($img, $file);
		imagedestroy($img);
		
		// Writing and reading to a file is done, since ob_* may already be used
		$ret = file_get_contents($file);
		unlink($file);

		return $ret;
	}
	
	/**
	 * Prints the image and sends cache related headers
	 * 
	 * @return void
	 */
	public function output_image() {
		$this->send_headers();
		$img = $this->create($this->size, $this->hash);
		imagepng($img);
		imagedestroy($img);
	}

	/**
	 * Sends content-type and cache related headers so the browsers caches the image for 
	 * about two months. This reduces traffic.
	 */
	public function send_headers() {
		header('Content-Type: image/png');
		header('Cache-Control: public');
		header('Pragma:');
		header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 62 * 24 * 60 * 60 )); // Expire 2 months		
	}
}