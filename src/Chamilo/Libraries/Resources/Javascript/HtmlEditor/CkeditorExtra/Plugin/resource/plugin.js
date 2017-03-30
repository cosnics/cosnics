(function () {

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

    function setObject(href, coId, securityCode, type)
    {
        var html =  '<img ' +
            'src="' + href + '" ' +
            'data-co-id="' + coId + '" ' +
            'data-security-code="' + securityCode + '" ' +
            'data-type="'+type+'" ';

        if(type == 'video') {
            html += 'width="600" height="350" ';
        }
        if(type == 'file') {
            html += 'width="" height="600" ';
        }

        html += '/>';

        this.insertHtml(
            html
        );
    }

    /*CKEDITOR.on('dialogDefinition', function (ev) {
        var dialogName = ev.data.name;
        var dialogDefinition = ev.data.definition;
        if (dialogName == 'image2') {

            //var infoTab = dialogDefinition.getContents('info');
            //infoTab.remove('browse');
        }
        dialogDefinition.onShow = function(event){
            var dialog = event.sender;
            //we need to manipulate html here if we want to hide url...
        };
    });*/

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
            editor.on('widgetDefinition', function (event) {
                var widgetDefinition = event.data;

                if (widgetDefinition.name !== 'image') { //only modify image plugin
                    return;
                }

                if(!widgetDefinition.origUpcast) {
                    widgetDefinition.origUpcast = widgetDefinition.upcast;
                    widgetDefinition.upcast = function (element, data) { //from the saved/source html to the real html
                        element = widgetDefinition.origUpcast.call(this, element, data);

                        if(!element) {
                            return;
                        }

                        var imageElement = element.name === 'img'? element: element.getFirst('img'); //img can be wrapped in figure or other elements
                        if (!imageElement || !imageElement.attributes['data-co-id']) { //only resource images
                            return element;
                        }

                        var type =  imageElement.attributes['data-type'];

                        if(type ==='image') { //only images get a live preview
                            imageElement.attributes.src = getResourceImageUrl(imageElement.attributes['data-co-id']);
                        } else {
                            imageElement.attributes.src = CKEDITOR.tools.transparentImageData;
                        }

                        if(!imageElement.attributes.class) {
                            imageElement.attributes.class = '';
                        }

                        imageElement.attributes.class += ' cke_chamilo_' + imageElement.attributes['data-type'];
                        
                        return element;
                    };
                }

                if(!widgetDefinition.origDowncast) {
                    widgetDefinition.origDowncast = widgetDefinition.downcast;
                    widgetDefinition.downcast = function (element, data) { //from the real html to the saved/source html

                        element = widgetDefinition.origDowncast.call(this, element, data);
                        if(!element) {
                            return;
                        }

                        var imageElement = element.name === 'img'? element: element.getFirst('img'); //img can be wrapped in figure or other elements
                        if (!imageElement || !imageElement.attributes['data-co-id']) { //only resource images
                            return element;
                        }

                        imageElement.attributes.src = 'PLACEHOLDER';

                        return element;
                    };
                }

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
})();