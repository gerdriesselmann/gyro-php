<?php
/**
 * This routes auto-generates a token if given a notification
 * otherwiese takes current user to generate it 
 */
class NotificationsExcludeRoute extends ParameterizedRoute {
	/**
	 * Build the URL (except base part)
	 * 
	 * @param mixed $params Further parameters to use to build URL
	 * @return string
	 */
	protected function build_url_path($params) {
		$arr = $params;
		if ($params instanceof DAONotifications) {
			$user = $params->get_user();
			$arr = array(
				'source' => $params->source,
				'source_id' => $params->source_id,
			);
		}
		else {
			$user = Users::get_current_user();
		}
		$token = $user->create_token('exclude', $arr);
		$arr['token'] = $token;			
		$ret = parent::build_url_path($arr);
		return $ret;
	}			
}