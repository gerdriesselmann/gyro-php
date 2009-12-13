<?php
/* @var $page_data PageData */
switch ($page_data->status_code) {
	case CONTROLLER_NOT_FOUND:
		print "Not Found\n";
		break;
	case CONTROLLER_ACCESS_DENIED:
		print "Access Denied\n";
		break;
	case CONTROLLER_INTERNAL_ERROR:
		print "Internal Error\n";
		break;
	case CONTROLLER_REDIRECT:
		print "Redirect\n";
		break;
	default:
		break;
}
if ($page_data->status && $page_data->status->message) {
	print ConverterFactory::decode($page_data->status->message, ConverterFactory::HTML) . "\n";
}
if ($content) {
	print $content . "\n";
}
