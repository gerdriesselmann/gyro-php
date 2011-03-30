<?php
$title = 'Image Tools Test Page';
$page_data->head->title = $title;
$page_data->breadcrumb = array($title);
$page_data->head->robots_index = ROBOTS_NOINDEX;
?>

<h1><?=$title?></h1>

<form enctype="multipart/form-data" action="<?=ActionMapper::get_path('imagetools_test_index')?>" method="post">
<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
Upload an image to see imagetool's results: <input name="upload" type="file" /><br />
<input type="submit" value="Upload File" />
</form>

<p>This is the soure file: </p>
<div><img src="<?=ActionMapper::get_path('imagetools_test_image', array('type' => 'src'))?>" alt="Source" /></div>

<p>This is file resized to 300x100: </p>
<div><img src="<?=ActionMapper::get_path('imagetools_test_image', array('type' => 'resize'))?>" alt="Resized to 300x100" /></div>

<p>This is file fitted to 300x100: </p>
<div><img src="<?=ActionMapper::get_path('imagetools_test_image', array('type' => 'fit'))?>" alt="Fitted to 300x100" /></div>

<p>This is file croped in the middle 100x100: </p>
<div><img src="<?=ActionMapper::get_path('imagetools_test_image', array('type' => 'crop'))?>" alt="Cropped 100x100" /></div>

<p>Watermark added: </p>
<div><img src="<?=ActionMapper::get_path('imagetools_test_image', array('type' => 'watermark'))?>" alt="Watermark added" /></div>
