<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  xml:lang="<?=GyroLocale::get_language()?>" lang="<?=GyroLocale::get_language()?>">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=<?=strtoupper(Locale::get_charset())?>" />
	<meta name="language" content="<?=Locale::get_language()?>" />

	<?php 
	// Example how to include CSS
	$page_data->head->add_css_file('css/style.css'); 
	print $page_data->head->render(HeadData::ALL);
	?> 
</head>
<body>
	<div id="page">
	<?php 
	// Breadcrumb, if any
	print $breadcrumb; 
	
	// Error and success messages
	if ($status) {
		$status->display();
	}

	// Page content
	print $content;

	// Debug block (renders only if APP_TESTMODE is true
	print WidgetDebugBlock::output(); 
	?>
	</div>
</body>
</html>
