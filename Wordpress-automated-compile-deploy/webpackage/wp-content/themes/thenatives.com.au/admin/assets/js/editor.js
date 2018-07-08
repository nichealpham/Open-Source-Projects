jQuery(document).ready(function ($) {
  tinymce.create('tinymce.plugins.thenatives', {
    init : function(ed, url) {
      ed.addButton('show_related_post', {
        title : 'Add related post shortcode',
        cmd : 'show_related_post',
        image : url + '/icon-related.png'
      });
      ed.addCommand('show_related_post', function() {
          shortcode = '[show_related_post id="" align="" /]';
          ed.execCommand('mceInsertContent', 0, shortcode);
      });
    },
    // ... Hidden code
  });
  // Register plugin
  tinymce.PluginManager.add( 'thenatives', tinymce.plugins.thenatives );
});