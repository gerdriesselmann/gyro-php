/**
 * Init WYSIWYG-Editor
 */
function init_ckeditor() {
	$(".rte").ckeditor( function() {}, {
		toolbar : [
			 ['Bold','Italic','Underline','Strike'],
			 ['Link','Unlink'],
			 ['Smiley'],
			 ['Font','FontSize'],
			 ['TextColor','BGColor'],
			 ['NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote'],
			 '/',
			 ['Undo','Redo','-','Find','Replace', '-', 'SpellChecker', 'Scayt'],
			 ['Subscript','Superscript'],
			 ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
			 ['Table','SpecialChar'],
			 ['Maximize', 'ShowBlocks', 'Source']
		 ]
	});
}

/**
 * Initialize 
 */
$(document).ready(function() {
	init_ckeditor();
 });