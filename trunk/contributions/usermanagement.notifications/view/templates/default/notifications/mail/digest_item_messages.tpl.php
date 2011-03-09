<li>
<?php
Load::models('messages');
$msg = Messages::get(Arr::get_item($notification->source_data, 'id_message', false));
$link_msg = ActionMapper::get_url('view', $msg);
?>
<a href="<?=$notification->create_click_track_link(Notifications::DELIVER_DIGEST, $link_msg)?>"><?=$notification->get_title()?></a>
</li>
 
