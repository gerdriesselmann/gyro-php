<?php
print '-- ' . $notification->get_title(). " --\n";
print wordwrap(ConverterFactory::decode($notification->get_message(Notifications::DELIVER_DIGEST), ConverterFactory::HTML_EX), 65); 
