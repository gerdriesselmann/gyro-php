<h1>Updates executed</h1>

<?php 
if (count($logs) == 0) {
	print html::info(tr('Everything already is up to date!', 'systemupdates'));
}
else {
	$li = array();
	$success = true;
	foreach($logs as $log) {
		$out = html::b(GyroString::escape($log['component']));
		$status = $log['status'];
		$status_message = '';
		if ($status->is_ok()) {
			$status_message = html::span(tr('OK', 'systemupdates'), 'success'); 
		}
		else {
			$success = false;
			$status_message = html::span($status->to_string(), 'error');
		}
		
		$sub_li = array();
		$sub_li[] = html::b(tr('Task:', 'systemupdates')) . ' ' . GyroString::escape($log['task']) ;
		$sub_li[] = html::b(tr('Status:', 'systemupdates')) . ' ' . $status_message; 

		$out .= html::li($sub_li);
		$li[] = $out;
	}
	
	if ($success) {
		print html::success(tr('All updates where successfull!', 'systemupdates'));
	}
	else {
		print html::error(tr('Some errors occured, please review the update scripts', 'systemupdates'));
	}
	print html::li($li);
}
