CKEDITOR.plugins.add('quickquestion', {
    requires: 'widget',
    icons: 'quickquestion',
    lang: ['en', 'nl'],

    init: function (editor) {

        var pluginDirectory = this.path;
        editor.addContentsCss( pluginDirectory + 'plugin.css' );

        editor.widgets.add('quickquestion', {
            button: editor.lang.quickquestion.insertQuickQuestion,

            template: //@todo translation
            '<div class="quick-question">' +
            '<div class="a-question"><h4>' + editor.lang.quickquestion.askQuestion + '</h4></div>' +
            '<button class="btn btn-primary" style="margin-bottom:5px; display: none" onclick="$(&quot;.an-answer&quot;).toggle()">' + editor.lang.quickquestion.showAnswer + '</button>' +
            '<div class="an-answer">' + editor.lang.quickquestion.addAnswer + '</div>' +
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