/**
 * Init WYSIWYG-Editor
 */
function init_wymeditor() {
	$(".rte").wymeditor({ 
		logoHtml: '',
		toolsItems: [
		    {'name': 'Bold', 'title': 'Strong', 'css': 'wym_tools_strong'}, 
		    {'name': 'Italic', 'title': 'Emphasis', 'css': 'wym_tools_emphasis'},
		    {'name': 'InsertOrderedList', 'title': 'Ordered_List', 'css': 'wym_tools_ordered_list'},
		    {'name': 'InsertUnorderedList', 'title': 'Unordered_List', 'css': 'wym_tools_unordered_list'},
		    {'name': 'Indent', 'title': 'Indent', 'css': 'wym_tools_indent'},
		    {'name': 'Outdent', 'title': 'Outdent', 'css': 'wym_tools_outdent'},
		    {'name': 'Undo', 'title': 'Undo', 'css': 'wym_tools_undo'},
		    {'name': 'Redo', 'title': 'Redo', 'css': 'wym_tools_redo'},
		    {'name': 'CreateLink', 'title': 'Link', 'css': 'wym_tools_link'},
		    {'name': 'Unlink', 'title': 'Unlink', 'css': 'wym_tools_unlink'},		    
		    {'name': 'InsertTable', 'title': 'Table', 'css': 'wym_tools_table'},
		    {'name': 'ToggleHtml', 'title': 'HTML', 'css': 'wym_tools_html'},
		    {'name': 'Preview', 'title': 'Preview', 'css': 'wym_tools_preview'}
	  	],
	  	lang: 'en',
	  	updateSelector: ':submit',
	    updateEvent: 'click',
	    skin: 'default',
	    stylesheet: '/js/wymeditor/classes.css',
	    postInit: function(wym) {
			_wym_init_plugins(wym);
		}
	});
}

/**
 * Initialize 
 */
$(document).ready(function() {
	init_wymeditor();
 });