<?php
/**
 * @var PageData $page_data
 * @var bool $is_logged_in
 * @var DAOUsers $current_user
 */
?>
<!DOCTYPE html>
<html lang="<?=Locale::get_language()?>">
<head>
	<meta charset="<?=Locale::get_charset()?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php
	// ##Installed by CSS.Bootstrap3## Don't remove this!
	print WidgetJCSS::output($page_data, WidgetJCSS::META);
	print WidgetJCSS::output($page_data, WidgetJCSS::CSS);
	?>
</head>
<body>
	<a class="sr-only" href="#content">Skip navigation</a>
	<header class="navbar navbar-inverse navbar-fixed-top" role="banner">
		<div class="container">
			<div class="navbar-header">
				<button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".bs-navbar-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a href="../" class="navbar-brand"><?=$appname?></a>
			</div>
			<nav class="collapse navbar-collapse bs-navbar-collapse" role="navigation">
				<ul class="nav navbar-nav">
					<li class="active">
						<a href="#">Nav1</a>
					</li>
					<li>
						<a href="#">Nav2</a>
					</li>
					<li>
						<a href="#">Nav3</a>
					</li>
					<li>
						<a href="#">Nav4</a>
					</li>
					<li>
						<a href="#">Nav5</a>
					</li>
				</ul>
			</nav>
		</div>
	</header>

	<div class="container bs-docs-container" id="content">
		<div class="row">
			<div class="col-md-3">
				<div class="bs-sidebar" role="complementary">
					<?php
					print WidgetBlock::output($page_data, BlockBase::LEFT);
					?>
				</div>
			</div>
			<div class="col-md-9" role="main">
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
	</div>


	<footer class="bs-footer" role="contentinfo">
		<div class="container">
			Footer
		</div>
	</footer>
	<?php print WidgetJCSS::output($page_data, WidgetJCSS::JS); ?>
</body>
</html>
