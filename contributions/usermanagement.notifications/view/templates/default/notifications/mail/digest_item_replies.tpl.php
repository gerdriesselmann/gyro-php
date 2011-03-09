<li>
<?php
Load::models('posts');
$post = Posts::get(Arr::get_item($notification->source_data, 'id_post', false));
$link_post = ActionMapper::get_url('view', $post);
?>
<a href="<?=$notification->create_click_track_link(Notifications::DELIVER_DIGEST, $link_post)?>"><?=$notification->get_title()?></a>
</li>
 
