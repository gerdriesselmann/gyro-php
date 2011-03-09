<li>
<?php
Load::models('abuses');
$abuse = Abuses::get($notification->source_id);
$link_abuse = ActionMapper::get_url('edit', $abuse);
?>
<a href="<?=$notification->create_click_track_link(Notifications::DELIVER_DIGEST, $link_abuse)?>"><?=$notification->get_title()?></a>
</li>
 
