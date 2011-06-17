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
	var r = getRange(editor);
	if ($.browser.msie) return r.parentElement()
	return r.commonAncestorContainer
  }
  // getRange - gets the current text range object
  function getRange(editor) {
    if ($.browser.msie) return getSelection(editor).createRange();
    return getSelection(editor).getRangeAt(0);
  }

  // getSelection - gets the current text range object
  function getSelection(editor) {
    if ($.browser.msie) return editor.doc.selection;
    return editor.$frame[0].contentWindow.getSelection();
  }
})(jQuery);