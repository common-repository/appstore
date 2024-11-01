function appstoreButton() {
	var searchwindow = window.open('http://appsuche.touchtalk.at/index.php?wpsearch=true','mywindow','width=450,height=600')
	/*var searchwindow = window.open('http://touchtalk.dreamhosters.com/AppstoreTest/index.php?wpsearch=true','mywindow','width=450,height=600')*/
    /* return "[app ]"; */
	return "";
}

(function() {

    tinymce.create('tinymce.plugins.appstoreButton', {

        init : function(ed, url){
            ed.addButton('appstoreButton', {
                title : 'Insert Appstore Link',
                onclick : function() {
                    ed.execCommand(
                        'mceInsertContent',
                        false,
                        appstoreButton()
                        );
                },
                image: url + "/mcebutton.png"
            });
        },

        getInfo : function() {
            return {
                longname : 'AppStore Button',
                author : 'Stephan',
                authorurl : 'http://ste-bi.net',
                infourl : '',
                version : "1.0"
            };
        }
    });

    tinymce.PluginManager.add('appstoreButton', tinymce.plugins.appstoreButton);
    
})();
