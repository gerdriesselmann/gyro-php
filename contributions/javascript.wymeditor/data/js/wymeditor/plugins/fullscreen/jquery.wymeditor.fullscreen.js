/*
 * WYMeditor : what you see is What You Mean web-based editor
 * Copyright (c) 2005 - 2009 Jean-Francois Hovinne, http://www.wymeditor.org/
 * Dual licensed under the MIT (MIT-license.txt)
 * and GPL (GPL-license.txt) licenses.
 *
 * For further information visit:
 *        http://www.wymeditor.org/
 *
 * File Name:
 *        jquery.wymeditor.fullscreen.js
 *        Fullscreen plugin for WYMeditor
 *
 * File Authors:
 *        Luis Santos (luis.santos a-t openquest dotpt)
 *        Gerd Riesselmann (gerd a-t gyro-php dot org) : Fixed issue with new skin layout
 */

//Extend WYMeditor
WYMeditor.editor.prototype.fullscreen = function() {
  var wym = this;

 //construct the button's html
  var html = "<li class='wym_tools_fullscreen'>"
         + "<a name='Fullscreen' href='#'"
         + " style='background-image:"
         + " url(" + wym._options.basePath +"plugins/fullscreen/icon_fullscreen.gif)'>"
         + "Fullscreen"
         + "</a></li>";

  //add the button to the tools box
  jQuery(wym._box)
    .find(wym._options.toolsSelector + wym._options.toolsListSelector)
    .append(html);

  //handle click event
  jQuery(wym._box).find('li.wym_tools_fullscreen a').click(function() {
    var iframe = jQuery(wym._box).find('.wym_iframe iframe'); 
    if (jQuery('.wym_box').css('position') != 'fixed') {
      var screen_height = jQuery(window).height();
      var iframe_height = (screen_height - 100) + 'px';
      screen_height = screen_height + 'px';
      
      var screen_width = jQuery(window).width();
      var editor_width = (screen_width - 40) + 'px'; // This may not work with all skins
      screen_width = screen_width + 'px';
   
      // Store old iframe height
      jQuery.data(wym, 'iframe_height', jQuery(iframe).css('height'));
      
      jQuery('body').append('<div id="loader"></div>');
      jQuery('#loader').css({'position' : 'fixed', 'background-color': 'rgb(0, 0, 0)', 'opacity': '0.8', 'z-index': '98', 'width': screen_width, 'height': screen_height, 'top': '0px', 'left': '0px'});
      jQuery(wym._box).css({'position' : 'fixed', 'z-index' : '99', 'top': '15px', 'left': '15px', 'width': editor_width});
      // With new skin layout, the iframe must be adjusted in height. Rest will follow
      // Especially height of wym._box must be left untouched!
      jQuery(iframe).css({'height': iframe_height});
    } else {
      jQuery('#loader').remove();
      jQuery(wym._box).css({'position' : 'static', 'z-index' : '99', 'width' : '100%', 'top': '0px', 'left': '0px'});
      // Restore old height
      jQuery(iframe).css({'height': jQuery.data(wym, 'iframe_height')});
    }

    return(false);
  });
};
