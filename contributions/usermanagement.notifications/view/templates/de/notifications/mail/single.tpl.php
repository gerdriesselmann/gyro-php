Hallo,

bei <?php print $appname ?> ist etwas geschehen.

-- <?php print $notification->get_title();?> --
<?php print wordwrap(ConverterFactory::decode($notification->message, ConverterFactory::HTML_EX), 65); ?> 

Um Ihre Einstellungen für Benachrichtigungen zu ändern loggen Sie sich bei <?php print $appname ?> ein und besuchen Sie
<?php print ActionMapper::get_url('notifications_settings')?>.

Mit freundlichen Grüßen,
Das Team von <?php print $appname?>

