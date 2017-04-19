(function () {

    /**
     * Plugin for changing the style in context menu of resources and embedded media
     */
    CKEDITOR.plugins.add('resourcestylecontextmenu', {
        requires: 'stylescombo',
        icons: '',
        init: function (editor) {
            editor.addMenuGroup('styles', 2);

            editor.on('stylesSet', function (evt) {
                var styleSet = evt.data.styles;

                function getCommands(widget) {
                    var commands = [];
                    var styleObjects = [];

                    styleSet.forEach(function (style) {
                        if (style.widget === widget) {
                            var styleObj = new CKEDITOR.style(style);
                            styleObjects.push(styleObj);

                            //command to toggle the style
                            var cmd = new CKEDITOR.command(editor, {
                                exec: function (editor) {
                                    if (styleObj.checkActive(editor.elementPath(), editor)) {
                                        styleObj.remove(editor);
                                    } else {
                                        styleObj.apply(editor);
                                        styleObjects.forEach(function (style) {
                                            if (style !== styleObj) {
                                                style.remove(editor); //remove the other styles (only 1 is active)
                                            }
                                        });
                                    }
                                }
                            });
                            cmd.style = styleObj;
                            commands.push(
                                cmd
                            );

                            editor.addCommand(style.name + 'cmd', commands[commands.length - 1]);
                            editor.addMenuItem(
                                style.name, {
                                    label: style.name,
                                    command: commands[commands.length - 1].name,
                                    group: 'styles',
                                    order: style.cm_order
                                });
                        }
                    });
                    return commands;
                }

                var resourceCommands = getCommands('resource');

                var embedCommands = getCommands('embed');

                editor.contextMenu.addListener(function (element, selection, path) {
                    function extracted(commands) {
                        var returnValue = [];
                        commands.forEach(function (command) {
                            if (command.style.checkActive(path, editor)) {
                                returnValue[command.style._.definition.name] = CKEDITOR.TRISTATE_ON;
                            } else {
                                returnValue[command.style._.definition.name] = CKEDITOR.TRISTATE_OFF;
                            }
                        });

                        return returnValue;
                    }

                    if (element.hasClass('cke_widget_resource')) {
                        return extracted(resourceCommands);
                    }

                    if (element.hasClass('cke_widget_embed')) {
                        return extracted(embedCommands);
                    }
                })
            });

        }
    })
})();