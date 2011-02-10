/**
 * Init WYSIWYG-Editor
 */
function init_ckeditor() {
	$(".rte").ckeditor( function() {}, {
       	toolbar : [
			['Bold','Italic','Underline','Strike'],
			['Link','Unlink','Image'],
			['Smiley'],
			['Font','FontSize'],
			['TextColor','BGColor'],
			['Maximize', 'ShowBlocks', 'Source'],
			'/',
			['Format'],
			['NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote'],
			['Undo','Redo','-','Replace'],
			['Subscript','Superscript'],
			['JustifyLeft','JustifyCenter','JustifyRight'],
			['Table','SpecialChar']
		],
        width: '98%',
        resize_maxWidth: '98%',
        resize_minWidth: '98%',
        format_tags: 'p;div;h1;h2;h3;h4;h5;h6;address;pre',
        lang: 'en',
        scayt_autoStartup: false
	});
}

CKEDITOR.on( 'dialogDefinition', function( ev ) {
	var dialogName = ev.data.name;
	var dialogDefinition = ev.data.definition;
	if ( dialogName == 'image' ) {
		// FCKConfig.ImageDlgHideAdvanced = true	
		dialogDefinition.removeContents( 'advanced' );
		// FCKConfig.ImageDlgHideLink = true
		dialogDefinition.removeContents( 'Link' );
	}
});

/**
 * Initialize 
 */
$(document).ready(function() {
	init_ckeditor();
 });