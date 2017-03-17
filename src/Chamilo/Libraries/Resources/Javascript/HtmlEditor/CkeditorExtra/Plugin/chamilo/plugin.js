(function()
{
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
    
    var openRepoViewerCmd = {
        exec : function(editor)
        {
            var width = editor.config['filebrowserChamiloWindowWidth'] || editor.config.filebrowserWindowWidth || '80%';
            var height = editor.config['filebrowserChamiloWindowHeight'] || editor.config.filebrowserWindowHeight
                    || '70%';
            
            var params = {};
            params.CKEditor = editor.name;
            params.CKEditorFuncNum = editor._.chamiloFn;
            if (!params.langCode)
                params.langCode = editor.langCode;
            
            var url = addQueryString(editor.config['filebrowserChamiloBrowseUrl'], params);
            openPopup(url, width, height);
        }
    };
    
    var openDialog = {
        exec : function(editor)
        {
            var element = editor.getSelection().getSelectedElement();
            if (element.is('img') && element.data('cke-real-element-type') == 'chamilo')
            {
                var realElement = CKEDITOR.dom.element.createFromHtml(decodeURIComponent(element
                        .getAttribute('data-cke-realelement')), this.document);
                var objectType = realElement.getAttribute('type');

                var context = (objectType == 'video') ? 'Hogent' : 'Chamilo';

                if (!CKEDITOR.dialog.exists(objectType + 'Dialog'))
                {
                    CKEDITOR.dialog.add(objectType + 'Dialog', web_path + context + '/Core/Repository/ContentObject/' + toTitleCase(objectType)
                        + '/Resources/Javascript/HtmlEditor/Ckeditor/dialog.js');
                }

                editor.openDialog(objectType + 'Dialog');
            }
        }
    };

    function toTitleCase(str)
    {
        return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
    }

    function concatObject(obj)
    {
        str = '';
        for (prop in obj)
        {
            str += prop + " value :" + obj[prop] + "\n";
        }
        return (str);
    }
    
    function setObject(href, objectId, objectType, objectSecurityCode, data)
    {
        if (!CKEDITOR.dialog.exists(objectType + 'Dialog'))
        {
            CKEDITOR.dialog.add(objectType + 'Dialog', href);
        }
        
        this.openDialog(objectType + 'Dialog', function(dialog)
        {
            var object = this;
            
            object.on('setVars', function(event)
            {
                object.setValueOf('info', 'security_code', objectSecurityCode);
                object.setValueOf('info', 'source', objectId);
                object.setValueOf('info', 'type', objectType);
            });
            
            object.on('show', function(event)
            {
                object.fireOnce('setVars');
            });
        });
    }
    
    function createChamiloFakeElement(editor, realElement, isResizable)
    {
        return editor.createChamiloFakeParserElement(realElement, 'cke_chamilo_' + realElement.attributes.type,
                'chamilo', isResizable);
    }
    
    var pluginName = 'chamilo';
    
    // Register a plugin named "chamilo".
    CKEDITOR.plugins.add(pluginName, {
        lang : 'nl,en-gb',
        icons : 'chamilo',
        init : function(editor)
        {
            
            editor._.chamiloFn = CKEDITOR.tools.addFunction(setObject, editor);
            editor.on('destroy', function()
            {
                CKEDITOR.tools.removeFunction(this._.chamiloFn);
            });
            
            editor.addCommand(pluginName, openRepoViewerCmd);
            editor.addCommand('dialog', openDialog);
            editor.ui.addButton && editor.ui.addButton('Chamilo', {
                label : editor.lang.chamilo.label,
                command : pluginName
            // , icon : this.path + 'chamilo.png'
            });
            
            // If the "menu" plugin is loaded, register the menu items.
            if (editor.addMenuItems)
            {
                editor.addMenuItems({
                    chamilo : {
                        label : editor.lang.chamilo.properties,
                        command : 'dialog',
                        group : 'chamilo'
                    }
                });
            }
            
            editor.on('doubleclick', function(evt)
            {
                var element = evt.data.element;
                
                if (element.is('img') && element.data('cke-real-element-type') == 'chamilo')
                {
                    var realElement = CKEDITOR.dom.element.createFromHtml(decodeURIComponent(element
                            .getAttribute('data-cke-realelement')), this.document);
                    var objectType = realElement.getAttribute('type');
                    if (!CKEDITOR.dialog.exists(objectType + 'Dialog'))
                    {
                        CKEDITOR.dialog.add(objectType + 'Dialog', web_path + 'Chamilo/Core/Repository/ContentObject/' + toTitleCase(objectType)
                            + '/Resources/Javascript/HtmlEditor/Ckeditor/dialog.js');
                    }

                    evt.data.dialog = objectType + 'Dialog';
                }

            });
            
            // If the "contextmenu" plugin is loaded, register the listeners.
            if (editor.contextMenu)
            {
                editor.contextMenu.addListener(function(element, selection)
                {
                    if (element && element.is('img') && !element.isReadOnly()
                            && element.data('cke-real-element-type') == 'chamilo')
                    {
                        return {
                            chamilo : CKEDITOR.TRISTATE_OFF
                        };
                    }
                    else
                    {
                        return null;
                    }
                });
            }
        },
        
        afterInit : function(editor)
        {
            var dataProcessor = editor.dataProcessor, dataFilter = dataProcessor && dataProcessor.dataFilter;
            
            if (dataFilter)
            {
                dataFilter.addRules({
                    elements : {
                        'resource' : function(element)
                        {
                            return createChamiloFakeElement(editor, element, true);
                        }
                    }
                }, 5);
            }
        }
    });
})();
