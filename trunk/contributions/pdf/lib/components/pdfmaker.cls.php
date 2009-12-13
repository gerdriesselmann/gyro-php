<?php

/**
 * Custom PDF implementation
 *
 * @author Gerd Riesselmann
 */

$thirdparty_dir = dirname(__FILE__) . '/../../3rdparty/fpdf/';
if (!defined('FPDF_FONTPATH')) {
	define('FPDF_FONTPATH', $thirdparty_dir . 'font/');
} 
require_once $thirdparty_dir . 'fpdf.php';
require_once $thirdparty_dir . 'fpdi.php';
 
class PDFMaker extends FPDI
{
	private $text;
	private $template;
	private $template_pagecount = 0;
	private $filename;
	
	private $style = array();
	private $cell = false;
	private $skipFirstLF = false;

	public function __construct($text, $filename, $template = "") {
		$this->text = $text;
		$this->filename = $filename;
		$this->template = $template;
		if (!empty($this->template)) {
			$this->template_pagecount = $this->setSourceFile($this->template);
		}
				
		FPDI::FPDI("P", "mm", "A4");
	}
	
	/**
	 * Creates the PDF and stores it under given filename
	 *
	 * @param Integer Text start top in mm
	 * @param Integer Text left in mm
	 * @param Integer Text bottom in mm
	 *
	 * @return Status
	 */
	function create($top = 10, $left = 10, $bottom = 20) {		
		$this->AddPage();
		$this->SetMargins($left, $top, $left);
		$this->SetAutoPageBreak(true, $bottom);
		$this->SetFont("helvetica", "", 9);
		$this->SetXY($left, $top);
		$this->writeFormatted(5, $this->text);
		$this->Output($this->filename, "F");
	}

	function Footer() {
		$page_no = $this->PageNo();
		$tpl_page = 0;
		if ($page_no <= $this->template_pagecount) {
			$tpl_page = $page_no;
		}
		else if ($this->template_pagecount > 0) {
			$tpl_page = $this->template_pagecount;
		}
		if ($tpl_page) {
			$tplidx = $this->ImportPage($tpl_page);
			$this->useTemplate($tplidx);
		}
	}
	
	function writeFormatted($lineHeight, $text) {
		$arrText = preg_split('/\<(.*)\>/U', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
		foreach($arrText as $index => $content) {
			if($index % 2 == 0) {
				if ($this->cell !== false) {
					$arrCell = array(
						"w" => 0,
						"h" => 5,
						"border" => 0,
						"align" => "L",
						"fill" => 0
					);
				                 
					Arr::clean($arrCell, $this->cell);
					
		    	//Text
		    	$this->Cell(
		    		$arrCell["w"], 
		    		$arrCell["h"], 
		    		$content,
			      $arrCell["border"], 
			      0, 
			      $arrCell["align"], 
			      $arrCell["fill"]
					);
				}
				else {
					if (false && $this->skipFirstLF === true && strlen($content) > 0) {
						if (strpos($content, "\r\n") === 0) {
							$content = substr($content, 2);
						}
						else if(strpos($content, "\n") === 0) {
							$content = substr($content, 1);
						}
						$this->skipFirstLF = false;
					}
						
					$this->Write(5, $content);
				}
      }
      else {
     		//Tags
				if($content{0} == '/') {
					$this->closeTag(strtoupper(substr($content,1)));
				}
				else {
    				//Extract attributes
					$arrAttrs = explode(' ', $content);
					$tag = strtoupper(array_shift($arrAttrs));
					$arrAttrValues = array();
					foreach($arrAttrs as $attrExpression) {
    				if ( ereg('^([^=]*)=["\']?([^"\']*)["\']?$', $attrExpression, $temp)) {
            	$arrAttrValues[strtoupper($temp[1])] = $temp[2];
    				}
					}
    			$this->openTag($tag, $arrAttrValues);
    		}
    	}
    }
	}
	
	function openTag($tag, &$arrAttributes) {
		switch ($tag) {
			case "B":
			case "I":
			case "U":
				$this->setStyle($tag, 1);
				break;
			case "CELL":
				$this->cell = array();
				foreach($arrAttributes as $name => $value) {
					$this->cell[strtolower($name)] = strtoupper($value);
				}
				break;
		}
	}
	
	function closeTag($tag) {
		switch ($tag) {
			case "B":
			case "I":
			case "U":
				$this->setStyle($tag, 0);
				break;
			case "CELL":
				$this->cell = false;
				$this->skipFirstLF = true;
				break;
		}
	}
	
	function setStyle($tag, $enable) {
		//Modify style and select corresponding font
		if (array_key_exists($tag, $this->style) == false) {
			$this->style[$tag] = 0;
		}
			
		$this->style[$tag] += ($enable ? 1 : -1);
		$style='';
		foreach($this->style as $s => $count) {
			if($count > 0) {
				$style.=$s;
			}
		}
		$this->SetFont('',$style);
	}
}