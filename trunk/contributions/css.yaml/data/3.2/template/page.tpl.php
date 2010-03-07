<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  xml:lang="<?=GyroLocale::get_language()?>" lang="<?=GyroLocale::get_language()?>">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=<?=strtoupper(Locale::get_charset())?>" />
	<meta name="language" content="<?=Locale::get_language()?>" />

	<?php
	// ##Installed by CSS.YAML## Don't remove this! 
	print WidgetJCSS::output($page_data, WidgetJCSS::ALL);
	?>
</head>
<body>
  <div class="page_margins">
    <div class="page">
      <div id="header">
        <div id="topnav">
          <!-- start: skip link navigation -->
          <a class="skip" title="skip link" href="#navigation">Skip to the navigation</a><span class="hideme">.</span>
          <a class="skip" title="skip link" href="#content">Skip to the content</a><span class="hideme">.</span>
          <!-- end: skip link navigation --><a href="#">Login</a> | <a href="#">Contact</a> | <a href="#">Imprint</a>
        </div>
      </div>
      <div id="nav">
        <!-- skiplink anchor: navigation -->
        <a id="navigation" name="navigation"></a>
        <div class="hlist">
          <!-- main navigation: horizontal list -->
          <ul>
            <li class="active"><strong>Button 1</strong></li>
            <li><a href="#">Button 2</a></li>
            <li><a href="#">Button 3</a></li>
            <li><a href="#">Button 4</a></li>
            <li><a href="#">Button 5</a></li>
          </ul>
        </div>
      </div>
      <div id="main">
        <div id="col1">
          <div id="col1_content" class="clearfix">
           	<?php 
			// Breadcrumb, if any
			if (!empty($page_data->breadcrumb)){ print $page_data->breadcrumb; } 
			
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
        </div>
        <div id="col2">
          <div id="col2_content" class="clearfix">
            <?php 
            print WidgetBlock::output($page_data, BlockBase::LEFT);
            ?>
          </div>
        </div>
        <div id="col3">
          <div id="col3_content" class="clearfix">
			<?php
            print WidgetBlock::output($page_data, BlockBase::RIGHT);
            ?>
          </div>
          <!-- IE Column Clearing -->
          <div id="ie_clearing">   </div>
        </div>
      </div>
      <!-- begin: #footer -->
      <div id="footer">Layout based on <a href="http://www.yaml.de/">YAML</a>
      </div>
    </div>
  </div>
</body>
</html>