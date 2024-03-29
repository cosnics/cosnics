(function () {
    CKEDITOR.plugins.add( 'resource', {
        icons: 'resource',
        requires: 'image2',
        lang: ['en', 'nl'],

        beforeInit: function (editor) {

            /**
             * Prevent drag & drop outside ck editor
             */
            window.addEventListener("dragover",function(e){
                e = e || event;
                e.preventDefault();
            },false);
            window.addEventListener("drop",function(e){
                e = e || event;
                e.preventDefault();
            },false);

            editor.on("instanceReady", function(ev){
                ev.editor.on("paste", function (ev) {
                    //drag & drop
                    if(ev.data.dataTransfer.getData("data-co-id")){ //@todo: needs review!
                        //ev.data.preventDefault(true);
                        var coId = ev.data.dataTransfer.getData("data-co-id");
                        var type = ev.data.dataTransfer.getData("data-type");
                        var securityCode = ev.data.dataTransfer.getData("data-security-code");
                        var renderInline = (ev.editor.config["render_resource_inline"] ? 1 : 0);

                        // Always show inline version for these objects, no matter how the config is set
                        if(type === 'audio' || type === 'video' || type === 'image')
                        {
                            renderInline = 1;
                        }

                        var html = '';
                        if(type === 'image') {
                            var url = ''; //todo make html generation uniform
                            html += '<img src="' + url + '" ' +
                                'data-co-id="' + coId + '" ' +
                                'data-security-code="' + securityCode + '" ' +
                                'data-type="' + type + '" ' +
                                '><br>';
                        }
                        else {
                            html += '<div ' +
                                'data-co-id="' + coId + '" ' +
                                'data-security-code="' + securityCode + '" ' +
                                'data-type="'+type+'"' +
                                'data-render-inline"' + renderInline +'"' +
                                '></div><br>';
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
             * Command for the resource button: open repo viewer
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
                label: editor.lang.resource.insertResource,
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
                        data.renderInline = el.attributes['data-render-inline'];

                        if(!el.hasClass('cke_chamilo_' + el.attributes['data-type'])) {
                            el.addClass('cke_chamilo_' + el.attributes['data-type']);
                        }

                        if(!el.hasClass('align-center')) {
                            el.addClass('align-center');
                        }

                        el.children = []; //remove the existing rendition

                        var coProperties = getResourceRendition(data.coId, data.securityCode, data.type);
                        if(typeof coProperties === 'string')
                            coProperties = JSON.parse(coProperties);

                        if(data.renderInline === "1") {
                            var renditionFragment = CKEDITOR.htmlParser.fragment.fromHtml(
                                '<h3 style="margin-top: 75px"> ' + coProperties.title
                                + '</h3><p>(wordt volledig weergegeven na opslaan)</p>' //@todo translations
                            );
                            el.add(renditionFragment); //add the newly fetched rendition*/

                            el.attributes['style'] = 'height: 350px;width:100%;';
                        } else {
                            var renditionFragment = CKEDITOR.htmlParser.fragment.fromHtml(
                                '<h6 style="margin-top: 50px"> ' + coProperties.title
                                + '</h6>' //@todo translations
                            );
                            el.add(renditionFragment); //add the newly fetched rendition*/

                            el.attributes['style'] = 'height: 80px;width:100px;';
                        }

                        return true;
                    }
                },

                downcast: function( el ) {
                    var attributes = [];
                    attributes[ 'data-co-id' ] = this.data.coId;
                    attributes[ 'data-type' ] = this.data.type;
                    attributes[ 'data-security-code' ] = this.data.securityCode;
                    attributes['data-render-inline'] = this.data.renderInline;
                    attributes[ 'class'] = el.attributes['class'];

                    return new CKEDITOR.htmlParser.element( 'div', attributes );
                }
            }, true );

            // Register the definition as 'embed' widget.
            editor.widgets.add( 'resource', widgetDefinition );
        }

    });

    function setObject(href, coId, securityCode, type)
    {
console.log(this);
        if(type === 'image') {
            var html =  '<img src="' + href + '" ';
        }
        else {
            var html = '<div ';
        }
        html +=
            'data-co-id="' + coId + '" ' +
            'data-security-code="' + securityCode + '" ' +
            'data-type="'+type+'" ' +
            'data-render-inline="' + (this.config['render_resource_inline'] ? 1 : 0) + '"';

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

        var rendition = {
            url: ''
        };

        $.ajax({
            type : "POST",
            url : ajaxUri,
            data : parameters,
            async : false
        }).success(function(json)
        {
            if(json.result_code == 200) {
                rendition = json.properties.rendition;
            }
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