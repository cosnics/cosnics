CKEDITOR.plugins.add('quickquestion', {
    requires: 'widget',
    icons: 'quickquestion',

    init: function (editor) {

        var pluginDirectory = this.path;
        editor.addContentsCss( pluginDirectory + 'plugin.css' );

        editor.widgets.add('quickquestion', {
            button: 'Ask a quick question',

            template: //@todo translation
            '<div class="quick-question">' +
            '<div class="a-question"><h4>Stel hier uw vraag.</h4></div>' +
            '<button class="btn btn-primary" style="margin-bottom:5px; display: none" onclick="$(&quot;.an-answer&quot;).toggle()">Toon antwoord</button>' +
            '<div class="an-answer">Geef hief uw antwoord</div>' +
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