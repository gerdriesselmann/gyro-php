<?php
Load::components('rsswriter');

// Title section
$feed_title = new FeedWriterTitle();
$feed_title->copyright = $appname;
$feed_title->title = tr('Your %appname Notifications', 'notifications', array('%appname' => $appname));
$feed_title->editor =  Config::get_value(Config::MAIL_SUPPORT) . " (Support $appname)";
$feed_title->generator = Config::get_url(Config::URL_BASEURL);

// Items
$items = array();
foreach($notifications as $n) {
	/* @var $n DAONotifications */
	$item = new FeedWriterItem();
	$item->author_name = tr($n->source);
	$item->content = $n->message;
	$item->description = String::clear_html($n->message);
	$item->link = ActionMapper::get_url('view', $n);
	$item->baseurl = $feed_title->generator;
	$item->guid = $item->link;
	$item->pubdate = $n->creationdate;
	$item->last_update = $n->creationdate;
	$item->title = $n->get_title();
	
	$items[] = $item;
}

$feedwriter = new RSSWriter($feed_title, $items);
$self->assign(MimeView::MIMETYPE, $feedwriter->get_mime_type());
print $feedwriter->render();		