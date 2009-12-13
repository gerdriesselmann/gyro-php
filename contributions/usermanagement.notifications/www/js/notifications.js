// JavaScript stuff for notification lists
$(document).ready(function() {
	$('.notification-item .list_menu').hide();
	$('.notification-item h3').css('cursor', 'pointer').click(function() {
		var item = $(this).parents('.notification-item').eq(0);
		// Try to get id
		var id = item.attr('id').replace('notification-item-', '');
		jQuery.ajax({
			cache: false,
			data: 'id=' + id,
			error: function (XMLHttpRequest, textStatus, errorThrown) {
			} ,
			success: function (data, textStatus) {
				data = data.toLowerCase();
				$(item).removeClass('read').removeClass('new').addClass(data);
			},
			type: 'POST',
			url: '/ajax/notifications/toggle'
		})
	});
});