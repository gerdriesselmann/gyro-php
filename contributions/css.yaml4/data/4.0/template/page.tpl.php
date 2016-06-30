<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php
	// ##Installed by CSS.YAML## Don't remove this!
	print WidgetJCSS::output($page_data, WidgetJCSS::ALL);
	?>
	<!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>
<body id="<?=GyroString::plain_ascii($route_id)?>" class="<?=strtolower($route_controller)?>">
<ul class="ym-skiplinks">
	<li><a class="ym-skip" href="#nav">Skip to navigation (Press Enter)</a></li>
	<li><a class="ym-skip" href="#main">Skip to main content (Press Enter)</a></li>
</ul>

<div class="ym-wrapper">
<div class="ym-wbox">
<header>
	<div class="ym-wrapper">
		<div class="ym-wbox">
			<h1><?=$appname?></h1>
		</div>
	</div>
</header>
<nav id="nav">
	<div class="ym-wrapper">
		<div class="ym-hlist">
			<ul>
				<li class="active"><strong>Active</strong></li>
				<li><a href="#">Link</a></li>
				<li><a href="#">Link</a></li>
				<li><a href="#">Link</a></li>
				<li><a href="#">Link</a></li>
			</ul>
			<form class="ym-searchform">
				<input class="ym-searchfield" type="search" placeholder="Search..." />
				<input class="ym-searchbutton" type="submit" value="Search" />
			</form>
		</div>
	</div>
</nav>
<div id="main">
	<div class="ym-column linearize-level-1">
		<div class="ym-col1">
			<div class="ym-cbox">
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
		</div>
		<aside class="ym-col2">
			<div class="ym-cbox">
				<?php
				print WidgetBlock::output($page_data, BlockBase::LEFT);
				?>
			</div>
		</aside>
		<aside class="ym-col3">
			<div class="ym-cbox">
				<?php
				print WidgetBlock::output($page_data, BlockBase::RIGHT);
				?>
			</div>
		</aside>
	</div>
</div>
<footer>
	<p>© <?=$appname?> 2012 &ndash; Layout based on <a href="http://www.yaml.de">YAML</a></p>
</footer>
</div>
</div>

<!-- full skip link functionality in webkit browsers -->
<script src="/yaml/core/js/yaml-focusfix.js"></script>
</body>
</html>