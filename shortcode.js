(function() {
    tinymce.PluginManager.add('better_share_link_tc_button', function( editor, url ) {
        editor.addButton( 'better_share_link_tc_button', {
            text: 'Share link',
            icon: false,
            onclick: function() {
                editor.insertContent('[better_share_link url="||||||"]');
            }
        });
    });
})();