CKEDITOR.plugins.add('recording', {
    requires: 'widget',
    icons: 'recording',
    lang: ['en', 'nl'],

    init: function (editor) {

        var pluginDirectory = this.path;

        editor.addContentsCss( pluginDirectory + 'plugin.css' );

        editor.widgets.add('recording', {
            button: editor.lang.recording.insertRecording,
            template: //@todo translation
            '<div class="quick-question">' +
            '<div class="a-question"><h4>' + editor.lang.recording.askQuestion + '</h4></div>' +
            '<button type="button" class="btn btn-primary" style="margin-bottom:5px; display: none" onclick="$(\'.an-answer\', $(this).parent()).toggle()">' + editor.lang.recording.showAnswer + '</button>' +
            '<div class="an-answer">' + editor.lang.recording.addAnswer + '</div>' +
            '</div>',


            editables: {
                question: {
                    selector: '.a-question'
                },
                answer: {
                    selector: '.an-answer'
                }
            },

            upcast: function (element) {
                if (element.name == 'div' && element.hasClass('quick-question')) {
                    element.children[1].attributes["style"] = "margin-bottom:5px; display: none"; //hide button
                    element.children[2].attributes["style"] = ""; //show answer

                    return true;
                }

                return false;
            },

            downcast: function (element) {
                element.children[1].attributes["style"] = "margin-bottom:5px;"; //show button
                element.children[2].attributes["style"] = "display: none;"; //hide answer

                return element;
            }
        });
    }
});