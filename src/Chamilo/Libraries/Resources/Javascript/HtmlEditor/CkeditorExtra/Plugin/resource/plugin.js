(function () {

    CKEDITOR.plugins.add( 'resource', {
        icons: 'resource',
        requires: 'image2',

        beforeInit: function (editor) {

            editor.on("instanceReady", function(ev){
                ev.editor.on("paste", function (ev) {
                    var html=ev.data.dataValue;

                    //On paste, replace p with DIV
                    var re = new RegExp("(<p)([^>]*>.*?)(<\/p>)","gi") ;
                    html = html.replace( re, "<DIV$2</DIV>" ) ;
                    ev.data.dataValue = html;
                })
            });

            editor.on('pluginsLoaded', function( evt ) {
                var editor = evt.editor;

                //replace the src tag of included content objects with the correct url based on the data-co-id attribute
                editor.widgets.addUpcastCallback( function( element ) {
                    var imageElement = element.name === 'img'? element: element.find('img', true); //img can be wrapped in figure or other elements
                    if(imageElement instanceof Array) {
                        imageElement = imageElement[0]; //widget can only contain 1 image but element.find returns an array.
                    }
                    if (!imageElement || !imageElement.attributes['data-co-id']) { //only resource images
                        return ;
                    }

                    var type =  imageElement.attributes['data-type'];

                    if(type ==='image') { //only images get a live preview
                        imageElement.attributes.src = getResourceImageUrl(imageElement.attributes['data-co-id']);
                    } else {
                        imageElement.attributes.src = CKEDITOR.tools.transparentImageData;
                    }

                    imageElement.addClass(' cke_chamilo_' + imageElement.attributes['data-type']);
                } );
            });

        },

        init: function( editor ) {

            editor.addCommand( 'insertResource', {
                exec: function( editor ) {
                    var width = editor.config['filebrowserChamiloWindowWidth'] || editor.config.filebrowserWindowWidth || '80%';
                    var height = editor.config['filebrowserChamiloWindowHeight'] || editor.config.filebrowserWindowHeight
                        || '70%';

                    var params = {};
                    params.CKEditor = editor.name;
                    params.CKEditorFuncNum = CKEDITOR.tools.addFunction(setObject, editor);
                    if (!params.langCode)
                        params.langCode = editor.langCode;

                    var url = addQueryString(editor.config['filebrowserChamiloBrowseUrl'], params);

                    openPopup(url, width, height);
                }
            });

            editor.ui.addButton( 'Resource', {
                label: 'Insert Resource',
                command: 'insertResource',
                toolbar: 'insert'
            });

        }

    });

    function setObject(href, coId, securityCode, type)
    {
        var html =  '<img ' +
            'src="' + href + '" ' +
            'data-co-id="' + coId + '" ' +
            'data-security-code="' + securityCode + '" ' +
            'data-type="'+type+'" ';

        if(type === 'video') {
            html += 'width="600" height="350" ';
        }
        if(type === 'file') {
            html += 'width="" height="600" ';
        }

        html += '/>';

        this.insertHtml(
            html
        );
    }

    function getResourceImageUrl(objectId)
    {
        var ajaxUri = getPath('WEB_PATH') + 'index.php';
        var rendition = '';
        var parameters = {
            'application' : 'Chamilo\\Core\\Repository\\Ajax',
            'go' : 'rendition_implementation',
            'content_object_id' : objectId,
            'format' : 'json',
            'view' : 'image',
            'parameters' : {
                'none' : 'none'
            }
        };

        $.ajax({
            type : "POST",
            url : ajaxUri,
            data : parameters,
            async : false
        }).success(function(json)
        {
            rendition = json.properties.rendition.url;
        });

        return rendition;
    }

    function addQueryString(url, params)
    {
        var queryString = [];

        if (!params)
            return url;
        else
        {
            for ( var i in params)
                queryString.push(i + "=" + encodeURIComponent(params[i]));
        }

        return url + ((url.indexOf("?") != -1) ? "&" : "?") + queryString.join("&");
    }

})();