<?php print WidgetPager::output($pager_data) ?>
<?php print WidgetSorter::output($sorter_data) ?>
<?php print WidgetFilter::output($filter_data) ?>
<?php print WidgetFilterText::output($filtertext_data) ?>


<?php foreach ($users as $user): ?>
<div class="user">
	<p>
	  <strong><?=$user->name?> (<?=$user->email?>)</strong>
	</p>
	<?php print WidgetItemMenu::output($user, 'list') ?>	
</div>
<?php endforeach; ?>

<?php print WidgetPager::output($pager_data) ?>