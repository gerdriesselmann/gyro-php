<?php
print $task->name . ' (' . $task->action . ') - ' . GyroDate::local_date(time());
print "\n\n";
print $error->render(Status::OUTPUT_PLAIN);

