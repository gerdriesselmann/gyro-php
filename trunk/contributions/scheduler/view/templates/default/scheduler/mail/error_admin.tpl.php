<?php
print $task->name . ' (' . $task->action . ') - ' . GyroDate::local_date(time());
print "\n\n";
print ConverterFactory::decode($error->message, ConverterFactory::HTML);

