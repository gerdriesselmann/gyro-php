// JavaScript stuff for notification lists
function notifications_init_ajax_status() {
	$('.notification-item :input.action_status').unbind('click').click(function(ev) {
		var item = $(this).parents('.notification-item').eq(0);
		// Try to get id
		var id = item.attr('id').replace('notification-item-', '');
		jQuery.ajax({
			cache: false,
			data: 'id=' + id,
			error: function (XMLHttpRequest, textStatus, errorThrown) {
			} ,
			success: function (data, textStatus) {
				$(item).replaceWith(data);
				notifications_init_ajax_status();
			},
			type: 'POST',
			url: '/ajax/notifications/toggle'
		})
		ev.preventDefault();
	});	
}

$(document).ready(function() {
	notifications_init_ajax_status();
});