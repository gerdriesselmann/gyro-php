 	<?php print $form_validation; ?>

	<fieldset>
	<legend><?=tr('User Data', 'users')?></legend>

	<?php print WidgetInput::output('name', tr('Username', 'users'), $form_data) ?>
	<?php print WidgetInput::output('email', tr('E-mail', 'users'), $form_data) ?>

	<?php 
	if ($context == 'edit') {
		print html::note(tr('If the e-mail changes, the user gets a mail send to the new address to confirm this address exists.', 'users')); 
	}
	?>

	<?php print WidgetInput::output('pwd1', tr('Password', 'users'), $form_data, WidgetInput::PASSWORD) ?>
	<?php print WidgetInput::output('pwd2', tr('Repeat Password', 'users'), $form_data, WidgetInput::PASSWORD) ?>

	<p><?php print tr('Leave these fields empty to not change the password.',  'users')?></p> 

	<?php
	if ($context != 'create') { 
		if (!isset($form_data['roles'])) {
			foreach($user->get_roles() as $role) {
				$form_data['roles'][] = $role->id;
			}
		}	
	}	
	?>
	<?php print WidgetInput::output('roles', tr('Roles', 'users'), $form_data, WidgetInput::MULTISELECT, array('options' => $role_options))?>
	</fieldset>