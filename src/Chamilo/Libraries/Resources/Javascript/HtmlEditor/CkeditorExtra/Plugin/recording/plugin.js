(function () {
    function addQueryString(url, params) {
        var queryString = [];

        if (!params)
            return url;
        else {
            for (var i in params)
                queryString.push(i + "=" + encodeURIComponent(params[i]));
        }

        return url + ((url.indexOf("?") != -1) ? "&" : "?") + queryString.join("&");
    }

    var openRecordingViewerCmd = {
        exec: function (editor) {
            var width = editor.config['filebrowserChamiloWindowWidth'] || editor.config.filebrowserWindowWidth || '30%';
            var height = editor.config['filebrowserChamiloWindowHeight'] || editor.config.filebrowserWindowHeight || '50%';

            var params = {};
            params.CKEditor = editor.name;
            params.CKEditorFuncNum = editor._.chamiloFn;
            if (!params.langCode)
                params.langCode = editor.langCode;
            params.record = editor.lang.recording.record;
            params.stop = editor.lang.recording.stop;
            params.insert = editor.lang.recording.insert;
            params.download = editor.lang.recording.download;
            params.recordings = editor.lang.recording.recordings;

            var url = addQueryString('Chamilo/Libraries/Resources/Javascript/HtmlEditor/CkeditorExtra/Plugin/recording/html', params);
            openPopup(url, width, height);
        }
    };

    function setObject(href, objectId, objectSecurityCode, objectType) {
        html = '<div class="align-center" data-co-id="' + objectId.toString() + '" data-render-inline="1" data-security-code="' + objectSecurityCode + '" data-type="' + objectType + '"></div>';
        html += '<p>&nbsp;</p>';

        this.insertHtml(html);
    }

    function createChamiloFakeElement(editor, realElement, isResizable) {
        return editor.createChamiloFakeParserElement(realElement, 'cke_chamilo_' + realElement.attributes.type,
            'chamilo', isResizable);
    }

    var pluginName = 'recording';

    // Register a plugin named "recording".
    CKEDITOR.plugins.add(pluginName, {
        lang: 'nl,en',
        icons: 'recording',
        init: function (editor) {

            editor._.chamiloFn = CKEDITOR.tools.addFunction(setObject, editor);
            editor.on('destroy', function () {
                CKEDITOR.tools.removeFunction(this._.chamiloFn);
            });

            editor.addCommand(pluginName, openRecordingViewerCmd);
            editor.ui.addButton && editor.ui.addButton('Recording', {
                label: editor.lang.recording.label,
                command: pluginName
            });

            // If the "menu" plugin is loaded, register the menu items.
            if (editor.addMenuItems) {
                editor.addMenuItems({
                    recording: {
                        label: editor.lang.recording.properties,
                        command: 'dialog',
                        group: 'recording'
                    }
                });
            }

            // If the "contextmenu" plugin is loaded, register the listeners.
            if (editor.contextMenu) {
                editor.contextMenu.addListener(function (element, selection) {
                    if (element && element.is('img') && !element.isReadOnly()
                        && element.data('cke-real-element-type') == 'chamilo') {
                        return {
                            chamilo: CKEDITOR.TRISTATE_OFF
                        };
                    } else {
                        return null;
                    }
                });
            }
        },

        afterInit: function (editor) {
            var dataProcessor = editor.dataProcessor, dataFilter = dataProcessor && dataProcessor.dataFilter;

            if (dataFilter) {
                dataFilter.addRules({
                    elements: {
                        'resource': function (element) {
                            return createChamiloFakeElement(editor, element, true);
                        }
                    }
                }, 5);
            }
        }
    });
})();
