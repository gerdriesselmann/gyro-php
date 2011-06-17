/**
 * Blockquote plugin for CLEditor
 *
 * @author Gerd Riesselmann
*/

// ==ClosureCompiler==
// @compilation_level SIMPLE_OPTIMIZATIONS
// @output_file_name jquery.cleditor.quote.min.js
// ==/ClosureCompiler==

(function($) {
  function __(s) {
    return ( undefined === CLEDITOR_I18N[s] ) ? s : CLEDITOR_I18N[s];
  }

  // Define the quote button
  $.cleditor.buttons.quote = {
    name: "quote",
    image: "quote.gif",
    title: __("Quote"),
    command: "indent",
    buttonClick: quoteButtonClick
  };

  // Add the button to the default controls
  $.cleditor.defaultOptions.controls = $.cleditor.defaultOptions.controls
    .replace("indent ", "indent quote ");
        
  // Quote button click event handler
  function quoteButtonClick(e, data) {
	var elem = getParentOfRange(data.editor);
	while(elem && elem.tagName != 'BODY') {
		if(elem.tagName == 'BLOCKQUOTE') {
			data.command = "outdent";
			break;
		}
		elem = elem.parentNode;
	}
  }

  // getParent Element of Range
  function getParentOfRange(editor) {
	var r = $.cleditor.getRange(editor);
	if ($.browser.msie) {
		return r.parentElement();
	} else {
		return r.commonAncestorContainer
	}
  }
})(jQuery);