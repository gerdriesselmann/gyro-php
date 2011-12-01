<?php
/**
 * @var MailBaseCommand $mail_cmd
 * @var bool $is_logged_in
 * @var DAOUsers $current_user
 * @var MailCommand $mailcmd
 * @var IView $self
 * @var string $name
 * @var string $appname
 * @var string $message
 */
$mailcmd->set_is_html(true);
$mailcmd->set_alt_message($self);
?>
<p><b>Hello,</b></p>

<p><?=$name?> send the following message using the contact form on <?=$appname ?>.</p>
<hr />
<?php print ConverterFactory::encode($message, ConverterFactory::HTML); ?>
