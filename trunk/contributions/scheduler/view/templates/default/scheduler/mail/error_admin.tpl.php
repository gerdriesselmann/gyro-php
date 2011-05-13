<?php
$mailcmd->set_is_html(true);
$mailcmd->set_alt_message($self);
?>
<p><?=$task->name . ' (' . $task->action . ') - ' . GyroDate::local_date(time())?></p>
<br /><br />
<p><?=$error->render(Status::OUTPUT_PLAIN)?></p>

