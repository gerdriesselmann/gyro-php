<?php 
if (count($logs) == 0) {
	print tr('Success. Everything already is up to date!', 'systemupdates');
}
else {
	$success = true;
	$out = '';
	foreach($logs as $log) {
		$out .= $log['component'] . ': ';
		$status = $log['status'];
		$status_message = '';
		if ($status->is_ok()) {
			$out .= tr('OK', 'systemupdates'); 
		}
		else {
			$success = false;
			$out .= tr('Error', 'systemupdates');
			$out .= "\n -- " . tr('Message: %message', 'systemupdate', array('%message' => $status->to_string(Status::OUTPUT_PLAIN)));
		}
		$out .= "\n -- ". tr('Task: %task', 'systemupdates', array('%task' => $log['task']));
		$out .= "\n\n";
	}
		
	if ($success) {
		$out .= tr('Success. All updates where successfull!', 'systemupdates');
	}
	else {
		$out .= tr('Some errors occured, please review the update scripts', 'systemupdates');
	}
	print $out;
}
