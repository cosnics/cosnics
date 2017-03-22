(function()
{
    var cssStyle = CKEDITOR.htmlParser.cssStyle, cssLength = CKEDITOR.tools.cssLength;

    var cssLengthRegex = /^((?:\d*(?:\.\d+))|(?:\d+))(.*)?$/i;

    /*
     * Replacing the former CSS length value with the later one, with adjustment
     * to the length unit.
     */
    function replaceCssLength(length1, length2)
    {
        var parts1 = cssLengthRegex.exec(length1), parts2 = cssLengthRegex.exec(length2);

        // Omit pixel length unit when necessary,
        // e.g. replaceCssLength( 10, '20px' ) -> 20
        if (parts1)
        {
            if (!parts1[2] && parts2[2] == 'px') return parts2[1];
            if (parts1[2] == 'px' && !parts2[2]) return parts2[1] + 'px';
        }

        return length2;
    }

    function getFakeElementImageUrl(objectId, securityCode)
    {
        var ajaxUri = getPath('WEB_PATH') + 'index.php';
        var rendition = '';
        var parameters = {
            'application' : 'Chamilo\\Core\\Repository\\Ajax',
            'go' : 'rendition_implementation',
            'content_object_id' : objectId,
            'security_code': securityCode,
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
            if(json['result_code'] == 200) {
                rendition = json.properties.rendition.url;
            }
        });

        return rendition;
    }

    var htmlFilterRules = {
        elements : {
            $ : function(element)
            {
                var attributes = element.attributes, realHtml = attributes && attributes['data-cke-realelement'], realFragment = realHtml && new CKEDITOR.htmlParser.fragment.fromHtml(decodeURIComponent(realHtml)), realElement = realFragment && realFragment.children[0];

                // Width/height in the fake object are subjected to clone into
                // the real element.
                if (realElement && element.attributes['data-cke-resizable'])
                {
                    var styles = new cssStyle(element).rules, realAttrs = realElement.attributes, width = styles.width, height = styles.height;

                    width && (realAttrs.width = replaceCssLength(realAttrs.width, width));
                    height && (realAttrs.height = replaceCssLength(realAttrs.height, height));
                }

                return realElement;
            }
        }
    };

    CKEDITOR.plugins.add('chamilofakeobjects', {
        requires : [ 'htmlwriter' ],

        afterInit : function(editor)
        {
            var dataProcessor = editor.dataProcessor, htmlFilter = dataProcessor && dataProcessor.htmlFilter;

            if (htmlFilter) htmlFilter.addRules(htmlFilterRules);
        }
    });

    CKEDITOR.editor.prototype.createChamiloFakeElement = function(realElement, className, realElementType, isResizable)
    {
        if (realElement.getAttribute('width'))
        {
            var width = cssLengthRegex.exec(realElement.getAttribute('width'));
        }

        if (realElement.getAttribute('height'))
        {
            var height = cssLengthRegex.exec(realElement.getAttribute('height'));
        }

        var fakeElementImageUrl = getFakeElementImageUrl(realElement.getAttribute('source'), realElement.getAttribute('security_code'));

        if (!fakeElementImageUrl)
        {
            imageUrl = CKEDITOR.tools.transparentImageData;
        }
        else
        {
            imageUrl = fakeElementImageUrl;
        }

        var attributes = {
            'class' : className,
            src : imageUrl,
            'data-cke-realelement' : encodeURIComponent(realElement.getOuterHtml()),
            'data-cke-real-node-type' : realElement.type,
            align : realElement.getAttribute('align') || '',
            width : width ? width[1] : '100%',
            height : height ? height[1] : '50',
            style : width ? '' : 'clear:both'
        };

        if (realElementType) attributes['data-cke-real-element-type'] = realElementType;

        if (isResizable)
        {
            attributes['data-cke-resizable'] = isResizable;

            var fakeStyle = new cssStyle();

            var width = realElement.getAttribute('width'), height = realElement.getAttribute('height');

            width && (fakeStyle.rules.width = cssLength(width));
            height && (fakeStyle.rules.height = cssLength(height));
            fakeStyle.populate(attributes);
        }

        if (fakeElementImageUrl)
        {
            attributes['style'] = attributes['style'] + ' border: 0px;'
        }

        //set margins
        var marginVertical = 0;
        var marginHorizontal = 0;
        if (realElement.getAttribute('margin-vertical'))
        {
            marginVertical = cssLengthRegex.exec(realElement.getAttribute('margin-vertical'));
        }

        if (realElement.getAttribute('margin-horizontal'))
        {
            marginHorizontal = cssLengthRegex.exec(realElement.getAttribute('margin-horizontal'));
        }

        attributes['style'] = attributes['style'] + ' margin: ' + marginVertical[0] + 'px ' +marginHorizontal[0] + 'px;';


        return this.document.createElement('img', {
            attributes : attributes
        });
    };

    CKEDITOR.editor.prototype.createChamiloFakeParserElement = function(realElement, className, realElementType, isResizable)
    {
        var writer = new CKEDITOR.htmlParser.basicWriter();
        realElement.writeHtml(writer);
        var html = writer.getHtml();

        if (realElement.attributes.width)
        {
            var width = cssLengthRegex.exec(realElement.attributes.width);
        }
        if (realElement.attributes.height)
        {
            var height = cssLengthRegex.exec(realElement.attributes.height);
        }

        var fakeElementImageUrl = getFakeElementImageUrl(realElement.attributes.source, realElement.attributes['security_code']);

        if (!fakeElementImageUrl)
        {
            imageUrl = CKEDITOR.tools.transparentImageData;
        }
        else
        {
            imageUrl = fakeElementImageUrl;
        }

        var attributes = {
            'class' : className,
            src : imageUrl,
            'data-cke-realelement' : encodeURIComponent(html),
            'data-cke-real-node-type' : realElement.type,
            align : realElement.attributes.align || '',
            width : width ? width[1] : '100%',
            height : height ? height[1] : '50',
            style : width ? '' : 'clear:both'
        };

        if (realElementType) attributes['data-cke-real-element-type'] = realElementType;

        if (isResizable)
        {
            attributes['data-cke-resizable'] = isResizable;
            var realAttrs = realElement.attributes, fakeStyle = new cssStyle();

            var width = realAttrs.width, height = realAttrs.height;

            width != undefined && (fakeStyle.rules.width = cssLength(width));
            height != undefined && (fakeStyle.rules.height = cssLength(height));
            fakeStyle.populate(attributes);
        }

        if (fakeElementImageUrl)
        {
            attributes['style'] = attributes['style'] + ' border: 0px;'
        }

        //set margins
        var marginVertical = 0;
        var marginHorizontal = 0;
        if (realElement.attributes['margin-vertical'])
        {
            marginVertical = cssLengthRegex.exec(realElement.attributes['margin-vertical']);
        }

        if (realElement.attributes['margin-horizontal'])
        {
            marginHorizontal = cssLengthRegex.exec(realElement.attributes['margin-horizontal']);
        }

        attributes['style'] = attributes['style'] + ' margin: ' + marginVertical[0] + 'px ' +marginHorizontal[0] + 'px;';

        return new CKEDITOR.htmlParser.element('img', attributes);
    };

    CKEDITOR.editor.prototype.restoreChamiloRealElement = function(fakeElement)
    {
        if (fakeElement.data('cke-real-node-type') != CKEDITOR.NODE_ELEMENT) return null;

        var element = CKEDITOR.dom.element.createFromHtml(decodeURIComponent(fakeElement.data('cke-realelement')), this.document);

        if (fakeElement.data('cke-resizable'))
        {
            var width = fakeElement.getStyle('width'), height = fakeElement.getStyle('height');

            width && element.setAttribute('width', replaceCssLength(element.getAttribute('width'), width));
            height && element.setAttribute('height', replaceCssLength(element.getAttribute('height'), height));
        }

        return element;
    };

})();