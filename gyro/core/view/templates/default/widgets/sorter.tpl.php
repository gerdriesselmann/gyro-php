<?php if (!empty($sorter_data['current_column'])): ?>
<div class="sorter">
	<p><?php 
	$sorter_current_column = $sorter_data['current_column'];
	print tr(
		'Sorted by <strong>%column</strong>.', 
		array('app', 'core'), 
		array('%column' => GyroString::escape($sorter_current_column['title']))
	);
	?> 
	<span class="noprint"><?=tr('Sort by:', array('app', 'core'))?> 
	<?php
		$out = array();
		foreach($sorter_data['columns'] as $sorter_loop_column) {
			if ($sorter_loop_column['column'] != $sorter_current_column['column']) {
				$sorter_loop_column_title = GyroString::escape($sorter_loop_column['title']);
				$out[] = html::a(
					GyroString::escape($sorter_loop_column_title),
					$sorter_loop_column['link'],
					tr('Sort by %column', array('app', 'core'), array('%column' => $sorter_loop_column_title))
				); 
			}
		}
		print implode(', ', $out);
	?>
	</span>
	</p>			
	<p><?php 
	print tr(
		'Sort direction: <strong>%direction</strong>', 
		array('app', 'core'), 
		array('%direction' => GyroString::escape($sorter_current_column['sort_title']))
	);
	?> 
	<?php if (isset($sorter_current_column['other_sort_link'])): ?> 
	<span class="noprint"><?=tr('Rearrange:', array('app', 'core'))?> 
		<a href="<?=$sorter_current_column['other_sort_link']?>"><?=$sorter_current_column['other_sort_title']?></a>
	</span>
	<?php endif; ?>
	</p>
</div>
<?php endif; ?>
 