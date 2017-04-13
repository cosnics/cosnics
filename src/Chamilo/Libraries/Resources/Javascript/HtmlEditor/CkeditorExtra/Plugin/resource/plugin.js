(function () {


    CKEDITOR.plugins.add( 'resource', {
        icons: 'resource',
        requires: 'image2',

        beforeInit: function (editor) {

            editor.on("instanceReady", function(ev){
                ev.editor.on("paste", function (ev) {
                    //drag & drop
                    if(ev.data.dataTransfer.getData("data-co-id")){ //@todo: needs review!
                        //ev.data.preventDefault(true);
                        var coId = ev.data.dataTransfer.getData("data-co-id");
                        var type = 'file';
                        var securityCode = 'meh';


                        var html = '';
                        //resize = true??
                        if(type === 'image') {
                            var url = ''; //todo make html generation uniform
                            html += '<img src="' + url + '" ' +
                                'data-co-id="' + coId + '"' +
                                'data-security-code="' + securityCode + '"' +
                                'data-type="' + type + '"' +
                                + '"><br>';
                        }
                        else {
                            html += '<div ' +
                                'data-co-id="' + coId + '"' +
                                'data-security-code="' + securityCode + '"' +
                                'data-type="'+type+'"' +
                                '"></div><br>';
                        }

                        ev.data.dataValue = html;
                    }
                });
            });

            editor.on('pluginsLoaded', function( evt ) {
                var editor = evt.editor;

                //replace the src tag of included content object images with the correct url based on the data-co-id attribute
                editor.widgets.addUpcastCallback( function( element ) {
                    var imageElement = element.name === 'img'? element: element.find('img', true); //img can be wrapped in figure or other elements
                    if(imageElement instanceof Array) {
                        imageElement = imageElement[0]; //widget can only contain 1 image but element.find returns an array.
                    }
                    if (!imageElement || !imageElement.attributes['data-co-id']) { //only resource images
                        return ;
                    }

                    imageElement.attributes.src = getResourceRendition(imageElement.attributes['data-co-id'], imageElement.attributes['data-security-code'], 'image').url;
                    imageElement.addClass(' cke_chamilo_' + imageElement.attributes['data-type']);
                } );
            });

        },

        init: function( editor ) {

            /**
             * Command for the resource button
             */
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
            /*
            ------------------------------
             */

            //create resource widget for non resizable content object div placeholders.
            var widgetDefinition = CKEDITOR.plugins.embedBase.createWidgetBaseDefinition( editor );
            // Extend the base definition with additional properties.
            CKEDITOR.tools.extend( widgetDefinition, {
                upcast: function( el, data ) {
                    if ( el.name === 'div' && el.attributes[ 'data-co-id' ] && el.attributes['data-type'] !== 'image' ) {
                        data.coId = el.attributes[ 'data-co-id' ];
                        data.type = el.attributes['data-type'];
                        data.securityCode = el.attributes['data-security-code'];

                        if(!el.hasClass('cke_chamilo_' + el.attributes['data-type'])) {
                            el.addClass('cke_chamilo_' + el.attributes['data-type']);
                        }

                        if(!el.hasClass('align-center')) {
                            el.addClass('align-center');
                        }

                        el.children = []; //remove the existing rendition

                        var coProperties = JSON.parse(getResourceRendition(data.coId, data.securityCode, data.type));

                        var renditionFragment = CKEDITOR.htmlParser.fragment.fromHtml(
                            '<h3> ' + coProperties.title
                            + '</h3><p>(wordt volledig weergegeven na opslaan)</p>' //@todo translations
                        );
                        el.add(renditionFragment); //add the newly fetched rendition*/

                        el.attributes['style'] = 'height: 350px;width:100%;';

                        return true;
                    }
                },

                downcast: function( el ) {
                    var attributes = [];
                    attributes[ 'data-co-id' ] = this.data.coId;
                    attributes[ 'data-type' ] = this.data.type;
                    attributes[ 'data-security-code' ] = this.data.securityCode;

                    attributes[ 'class'] = el.attributes['class'];

                    return new CKEDITOR.htmlParser.element( 'div', attributes );
                }
            }, true );


            /**
             * Command for changing the style in context menu
             * @todo refactor
             */

            editor.addMenuGroup('basicstyles', 1);

            editor.getStylesSet(function(styleSet) {
                var style = new CKEDITOR.style(styleSet[6]);
                editor.addCommand('bald', new CKEDITOR.styleCommand(style));
            });


            editor.addMenuItems( {
                'large': {
                    label: 'Large size',
                    command: 'toggleLargeStyle',
                    group: 'basicstyles'
                },
                'medium': {
                    label: 'Medium size',
                    command: 'bald',
                    group: 'basicstyles'
                },
                'small': {
                    label: 'Small size',
                    command: 'toggleLargeStyle',
                    group: 'basicstyles'
                }
            } );
            editor.contextMenu.addListener( function( element, selection, path ) {
                if(element.hasClass('cke_widget_resource')) {
                    if(element.findOne('.embed-360p')) {
                        editor.getCommand('bald').setState(CKEDITOR.TRISTATE_ON);
                        return {
                            'medium': CKEDITOR.TRISTATE_ON
                        }
                    }
                    else {
                        editor.getCommand('bald').setState(CKEDITOR.TRISTATE_OFF);
                        return {
                            'medium': CKEDITOR.TRISTATE_OFF
                        }
                    }
                }

            } );

            // Register the definition as 'embed' widget.
            editor.widgets.add( 'resource', widgetDefinition );
        }

    });

    function setObject(href, coId, securityCode, type)
    {

        if(type === 'image') {
            var html =  '<img src="' + href + '" ';
        }
        else {
            var html = '<div ';
        }
        html +=
            'data-co-id="' + coId + '" ' +
            'data-security-code="' + securityCode + '" ' +
            'data-type="'+type+'" ';

        if(type !== 'image') {
            html += '></div>';
        }
        else {
            html += '/>';
        }
        html += '<p>&nbsp;</p>'; //otherwise the object can be removed when downcasting. Don't know reason.

        this.insertHtml(
            html
        );
    }

    function getResourceRendition(objectId, securityCode, type)
    {
        if(type === 'image') {
            var view = 'image';
        } else {
            var view = 'full';
        }

        var ajaxUri = getPath('WEB_PATH') + 'index.php';
        var rendition = '';
        var parameters = {
            'application' : 'Chamilo\\Core\\Repository\\Ajax',
            'go' : 'rendition_implementation',
            'content_object_id' : objectId,
            'security_code': securityCode,
            'format' : 'json',
            'view' : view,
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
            rendition = json.properties.rendition;
        }).error(function(error) {
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