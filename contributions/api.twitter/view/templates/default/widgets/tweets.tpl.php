<?php
$out = '';
foreach($tweets as $tweet) {
	$out .= html::p($tweet->message_html, 'tweet');
}
if ($out) {
	$out = html::div($out, 'tweets');
}
print $out;