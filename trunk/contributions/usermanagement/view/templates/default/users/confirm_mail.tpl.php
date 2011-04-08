<?php
$title = tr('Waiting for e-mail verification', 'users');
$page_data->head->title = $title;
?>

<h1><?=$title?></h1>

<p class="important"><?php print tr(
	'An e-mail has been send to <strong>%email</strong>. Please <strong>click the link inside the mail</strong> to confirm your e-mail address. <strong>Refresh this page</strong> afterwards.', 
	'users',
	array('%email' => String::escape($user->email))
)?></p>
