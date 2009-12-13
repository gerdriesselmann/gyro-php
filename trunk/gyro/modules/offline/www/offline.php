<?php
$status = '503 Service Unavailable';
if ( substr(php_sapi_name(), 0, 3) == 'cgi' ) { 
	header('Status: ' . $status);
}
else {
	header('HTTP/1.x ' . $status);
}
header('Retry-After: 120'); // Retry after 2 min
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<title>Maintenance</title>
	</head>
	<body>
	<h1>Maintenance</h1>
	<p>Sorry, this site is down for maintenance at the moment. Please come back later.</p>
	</body>
</html>