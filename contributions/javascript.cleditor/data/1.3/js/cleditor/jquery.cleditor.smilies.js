/**
 * @author Gerd Riesselmann
 *
 * Smilies plugin for CLEditor
*/

// ==ClosureCompiler==
// @compilation_level SIMPLE_OPTIMIZATIONS
// @output_file_name jquery.cleditor.icon.min.js
// ==/ClosureCompiler==

(function($) {
	function __(s) {
	  return ( undefined === CLEDITOR_I18N[s] ) ? s : CLEDITOR_I18N[s];
	}

	// Define the icon button
	$.cleditor.buttons.smilies = {
		name: "smilies",
		image: "smilies.gif",
		title: __("Insert Smiley"),
		command: "inserthtml",
		popupName: "Smilies",
		popupHover: true,
		popupContent: '',
		buttonClick: function(e, data) {
			if ($(data.popup).html() == "") {
				buildPopup(data);
			}
		},
		popupClick: function(e, data) {
			var $target = $(e.target);
			var title = $target.attr('title');
			var alt = __('Smiley:') + ' ' + title;
			var src = $target.attr('src');
			var width = $target.width();
			var height = $target.height();

			var html = '<img class="smiley" src="' + src + '" alt="' + alt + '" title="' + title + '" width="' + width + '" height="' + height + '" />';
			data.value = html;
		}
	};

	function buildPopup(data) {
		// Build the popup content
		var $content = $("<div>");
		var basepath = data.editor.options.smiliesPath;
		for (var i in data.editor.options.smilies) {
			var s = data.editor.options.smilies[i];
			var url = basepath + "/" + s[0];
			var img = '<img src="' + url + '" title="' + s[1] + '"/ style="float:left;padding:2px;">'
			$content.append(img);
		}

		i = parseInt(i) + 1;
		var rows = Math.round(Math.sqrt(i) - 1);
		if (rows < 1) { rows = 1; }
		var cols = Math.round(i / rows);

		$(data.popup).html($content).width(cols * 18);
	}

	// Add the button to the default controls
	$.cleditor.defaultOptions.controls = $.cleditor.defaultOptions.controls.replace("| cut", "smilies | cut");
	$.cleditor.defaultOptions.smiliesPath = $.cleditor.imagesPath() + "smilies/";
	$.cleditor.defaultOptions.smilies = [];

})(jQuery);