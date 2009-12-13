<?php
// $Id: atomwriter.cls.php,v 1.2 2005/11/06 22:13:15 gr Exp $

/**
 * Build Atom file, targeted by FeedWriter class
 *
 * @author Gerd Riesselmann http://www.gerd-riesselmann.net
 */

/*
Copyright (C) 2005 Gerd Riesselmann

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

http://www.gnu.org/licenses/gpl.html
*/

require_once dirname(__FILE__) . "/feedwriter.cls.php";

class AtomWriter extends FeedWriter
{
	function sendHeader()
	{
		header("Content-Type: application/atom+xml");
		//header("Content-Type: text/xml");
	}

	function printItem(&$item)
	{
	?>
	<entry xml:base="<?php print $item->baseURL; ?>">
		<title><?php print $item->title; ?></title>
		<link rel="alternate" type="text/html" href="<?php print $item->link; ?>" />
		<author><name><?php print (empty($item->authorName)) ? t("Unknown") : $item->authorName; ?></name></author>
		<id><?php print $item->guid . "/"; ?></id>
		<updated><?php $this->_outputDate($item->lastUpdate); ?></updated>
		<published><?php $this->_outputDate($item->pubDate); ?></published>
		<summary type="text"><?php print $this->clear($item->description); ?></summary>
		<content type="html"><![CDATA[<?php print $item->content; ?>]]></content>
		<?php
		foreach($item->categories as $cat)
		{
			$cat->validate();
			?>
			<category scheme="<?php print $cat->domain; ?>" term="<?print $cat->title; ?>" />
			<?php
		}
		?>
		<?php
		foreach($item->enclosures as $enc)
		{
			$enc->validate();
			?>
			<content type="<?php print $enc->type; ?>" src="<?php print $enc->url; ?>" />
			<?php
		}
		?>				
	</entry>
	<?php
	}

	function printTitle(&$title)
	{
	print '<?xml version="1.0" encoding="'. $title->encoding . '"?>';
	?>
	<feed xmlns="http://www.w3.org/2005/Atom"
  		xmlns:dc="http://purl.org/dc/elements/1.1/"
  		xml:lang="<?php print $title->language; ?>">
		<title><?php print $title->title ?></title>
		<id><?php print $title->link . "/"; ?></id>
		<link rel="alternate" type="text/html" href="<?php print $title->link; ?>" />
		<link rel="self" type="application/atom+xml" href="<?php print $title->thisURL; ?>" />
		<subtitle><?php print $title->description; ?></subtitle>
		<updated><?php $this->_outputDate($title->lastUpdated); ?></updated>
		<generator><?php print $title->generator; ?></generator>
	<?php
	}

	function printEnd()
	{
	?>
	</feed>
	<?php
	}
	
	function _outputDate($date)
	{
		$out = date("Y-m-d\TH:i:s", $date);
		$greenwich = date("O", $date);
		$out .= substr($greenwich, 0, 3) . ":" . substr($greenwich, -2, 2);
		print $out;
	}
}
?>