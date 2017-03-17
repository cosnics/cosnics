/*
 * Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */
CKEDITOR.dialog
        .add(
                'webpageDialog',
                function(editor)
                {
                    function commitContent()
                    {
                        var args = arguments;

                        this.foreach(function(widget)
                        {
                            if (widget.commit) widget.commit.apply(widget, args);
                        });
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

                    function getRendition(objectId, objectParameters)
                    {
                        var ajaxUri = getPath('WEB_PATH') + 'index.php';
                        var rendition = '';
                        var parameters = {
                            'application' : 'Chamilo\\Core\\Repository\\Ajax',
                            'go' : 'rendition_implementation',
                            'content_object_id' : objectId,
                            'format' : 'html',
                            'view' : 'inline',
                            'parameters' : objectParameters
                        };

                        $.ajax({
                            type : "POST",
                            url : ajaxUri,
                            data : parameters,
                            async : false
                        }).success(function(json)
                        {
                            return rendition = json.properties.rendition;
                        });

                        return rendition;
                    }

                    var numbering = function(id)
                    {
                        return CKEDITOR.tools.getNextId() + '_' + id;
                    };

                    var currentRendition;
                    var blockUpdatePreview;
                    var timer;

                    var btnLockSizesId = numbering('btnLockSizes'), btnResetSizeId = numbering('btnResetSize'), previewImageId = numbering('previewImage'), attributesMap = {}, text = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis a felis in nulla luctus feugiat vitae sit amet justo. Phasellus elementum odio id neque dapibus sit amet pulvinar diam aliquet. Quisque et condimentum magna. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Phasellus faucibus dui vel nisi lobortis id dignissim neque interdum. Mauris vitae leo risus. Aliquam erat volutpat. Phasellus quam risus, sodales vitae rhoncus sed, aliquet in elit. Phasellus vulputate neque eu tortor aliquam eu dapibus diam gravida. Nam leo erat, vestibulum sit amet malesuada sit amet, tempor nec eros. Curabitur dignissim laoreet massa nec cursus. Aenean bibendum rutrum lorem sed mollis. Pellentesque imperdiet ligula sit amet diam blandit eget faucibus tellus consequat. Pellentesque tristique elit sagittis orci pretium faucibus. Nullam vestibulum imperdiet ante id hendrerit.';

                    function getCurrentParameters(dialog)
                    {
                        var parameters = {
                            'margin-horizontal' : dialog.getValueOf('info', 'margin-horizontal'),
                            'margin-vertical' : dialog.getValueOf('info', 'margin-vertical')
                        };
                        parameters.width = dialog.getValueOf('info', 'width');
                        parameters.height = dialog.getValueOf('info', 'height');
                        parameters.border = dialog.getValueOf('info', 'border');
                        parameters.align = dialog.getValueOf('info', 'align');
                        parameters.alt = dialog.getValueOf('info', 'alt');

                        return parameters;
                    }

                    function getOriginalParameters(dialog)
                    {
                        var parameters = {
                            'margin-horizontal' : dialog.objectNode.getAttribute('margin-horizontal'),
                            'margin-vertical' : dialog.objectNode.getAttribute('margin-vertical')
                        };
                        parameters.width = dialog.objectNode.getAttribute('width');
                        parameters.height = dialog.objectNode.getAttribute('height');
                        parameters.border = dialog.objectNode.getAttribute('border');
                        parameters.align = dialog.objectNode.getAttribute('align');
                        parameters.alt = dialog.objectNode.getAttribute('alt');

                        return parameters;
                    }

                    // Load image preview.
                    var regexGetSize = /^\s*(\d+)((px)|\%)?\s*$/i, regexGetSizeOrEmpty = /(^\s*(\d+)((px)|\%)?\s*$)|^$/i, pxLengthRegex = /^\d+px$/;

                    var onSizeChange = function(element)
                    {

                        console.log(element);
                        var value = element.getValue(), // This = input element.
                        dialog = element.getDialog(), aMatch = value.match(regexGetSize); // Check

                        blockUpdatePreview = true;

                        // value
                        if (aMatch)
                        {
                            if (aMatch[2] == '%') // % is allowed - > unlock
                            // ratio.
                            switchLockRatio(dialog, false); // Unlock.
                            value = aMatch[1];
                        }

                        // Only if ratio is locked
                        if (dialog.lockRatio)
                        {
                            var oImageOriginal = dialog.originalElement;
                            if (oImageOriginal.getCustomData('isReady') == 'true')
                            {
                                if (element.id == 'height')
                                {
                                    if (value && value != '0') value = Math.round(oImageOriginal.$.width * (value / oImageOriginal.$.height));
                                    if (!isNaN(value)) dialog.setValueOf('info', 'width', value);
                                }
                                else
                                // this.id = txtWidth.
                                {
                                    if (value && value != '0') value = Math.round(oImageOriginal.$.height * (value / oImageOriginal.$.width));
                                    if (!isNaN(value)) dialog.setValueOf('info', 'height', value);
                                }
                            }
                        }

                        blockUpdatePreview = false;

                        updatePreview(dialog, true);
                    };

                    var switchLockRatio = function(dialog, value)
                    {

                        if (!dialog.getContentElement('info', 'ratioLock')) return null;

                        var oImageOriginal = dialog.originalElement;

                        // Dialog may already closed. (#5505)
                        if (!oImageOriginal) return null;

                        // Check image ratio and original image ratio, but
                        // respecting user's preference.
                        if (value == 'check')
                        {
                            if (!dialog.userlockRatio && oImageOriginal.getCustomData('isReady') == 'true')
                            {
                                var width = dialog.getValueOf('info', 'width'), height = dialog.getValueOf('info', 'height'), originalRatio = oImageOriginal.$.width * 1000 / oImageOriginal.$.height, thisRatio = width * 1000 / height;
                                dialog.lockRatio = false; // Default: unlock
                                // ratio

                                if (!width && !height)
                                    dialog.lockRatio = true;
                                else
                                    if (!isNaN(originalRatio) && !isNaN(thisRatio))
                                    {
                                        if (Math.round(originalRatio) == Math.round(thisRatio)) dialog.lockRatio = true;
                                    }
                            }
                        }
                        else
                            if (value != undefined)
                                dialog.lockRatio = value;
                            else
                            {
                                dialog.userlockRatio = 1;
                                dialog.lockRatio = !dialog.lockRatio;
                            }

                        var ratioButton = CKEDITOR.document.getById(btnLockSizesId);
                        if (dialog.lockRatio)
                            ratioButton.removeClass('cke_btn_unlocked');
                        else
                            ratioButton.addClass('cke_btn_unlocked');

                        ratioButton.setAttribute('aria-checked', dialog.lockRatio);

                        // Ratio button hc presentation - WHITE SQUARE / BLACK
                        // SQUARE
                        if (CKEDITOR.env.hc)
                        {
                            var icon = ratioButton.getChild(0);
                            icon.setHtml(dialog.lockRatio ? CKEDITOR.env.ie ? '\u25A0' : '\u25A3' : CKEDITOR.env.ie ? '\u25A1' : '\u25A2');
                        }

                        return dialog.lockRatio;
                    };

                    var resetSize = function(dialog)
                    {
                        var oImageOriginal = dialog.originalElement;
                        blockUpdatePreview = true;

                        if (oImageOriginal.getCustomData('isReady') == 'true')
                        {
                            var widthField = dialog.getContentElement('info', 'width'), heightField = dialog.getContentElement('info', 'height');
                            widthField && widthField.setValue(oImageOriginal.$.width);
                            heightField && heightField.setValue(oImageOriginal.$.height);
                        }

                        blockUpdatePreview = false;

                        updatePreview(dialog, true);
                    };

                    var setupDimension = function(element)
                    {

                        function checkDimension(size, defaultValue)
                        {
                            var aMatch = size.match(regexGetSize);
                            if (aMatch)
                            {
                                if (aMatch[2] == '%') // % is allowed.
                                {
                                    aMatch[1] += '%';
                                    switchLockRatio(dialog, false); // Unlock
                                    // ratio
                                }
                                return aMatch[1];
                            }
                            return defaultValue;
                        }

                        var dialog = this.getDialog(), value = '', dimension = this.id == 'width' ? 'width' : 'height', size = element.getAttribute(dimension);

                        if (size) value = checkDimension(size, value);
                        value = checkDimension(element.getStyle(dimension), value);

                        blockUpdatePreview = true;
                        this.setValue(value);
                        blockUpdatePreview = false;
                    };

                    var onImgLoadEvent = function()
                    {
                        // Image is ready.
                        var original = this.originalElement;
                        original.setCustomData('isReady', 'true');
                        original.removeListener('load', onImgLoadEvent);
                        original.removeListener('error', onImgLoadErrorEvent);
                        original.removeListener('abort', onImgLoadErrorEvent);

                        // New image -> new domensions
                        if (!this.dontResetSize) resetSize(this);

                        if (this.firstLoad) CKEDITOR.tools.setTimeout(function()
                        {
                            switchLockRatio(this, 'check');
                        }, 0, this);

                        this.firstLoad = false;
                        this.dontResetSize = false;
                    };

                    var onImgLoadErrorEvent = function(msg, url, line)
                    {
                        // Error. Image is not loaded.
                        var original = this.originalElement;
                        original.removeListener('load', onImgLoadEvent);
                        original.removeListener('error', onImgLoadErrorEvent);
                        original.removeListener('abort', onImgLoadErrorEvent);

                        switchLockRatio(this, false); // Unlock.
                    };

                    function updatePreview(dialog, isUpdate)
                    {
                        var objectParameters = dialog.objectNode && !isUpdate ? getOriginalParameters(dialog) : getCurrentParameters(dialog);

                        var ajaxUri = getPath('WEB_PATH') + 'index.php';
                        var parameters = {
                            'application' : 'Chamilo\\Core\\Repository\\Ajax',
                            'go' : 'rendition_implementation',
                            'content_object_id' : dialog.getValueOf('info', 'source'),
                            'security_code' : dialog.getValueOf('info', 'security_code'),
                            'format' : 'html',
                            'view' : 'inline',
                            'parameters' : objectParameters
                        };

                        var response = $.ajax({
                            type : "POST",
                            url : ajaxUri,
                            data : parameters,
                            async : false
                        }).responseText

                        var json = jQuery.parseJSON(response);
                        currentRendition = json.properties.rendition;
                        dialog.preview.setHtml(currentRendition + text);
                    }

                    return {
                        title : 'Document Properties',
                        minWidth : 420,
                        minHeight : 300,
                        onOk : function(evt)
                        {
                            resourceElement = editor.document.createElement('resource');
                            this.commitContent(resourceElement);

                            var newFakeImage = editor.createChamiloFakeElement(resourceElement, 'cke_chamilo_webpage', 'chamilo');
                            newFakeImage.setAttribute('alt', this.getValueOf('info', 'alt'));
                            newFakeImage.setAttribute('title', this.getValueOf('info', 'alt'));
                            editor.insertElement(newFakeImage);
                        },
                        onShow : function()
                        {

                            this.lockRatio = true;
                            this.userlockRatio = 0;
                            this.dontResetSize = false;
                            this.firstLoad = true;

                            // Copy of the image
                            this.originalElement = editor.document.createElement('img');
                            this.originalElement.setAttribute('alt', '');
                            this.originalElement.setCustomData('isReady', 'false');

                            this.preview = CKEDITOR.document.getById(previewImageId);
                            this.fakeImage = this.objectNode = null;

                            var fakeElement = this.getParentEditor().getSelection().getSelectedElement();
                            if (fakeElement && fakeElement.data('cke-real-element-type') && fakeElement.data('cke-real-element-type') == 'chamilo')
                            {
                                this.fakeElement = fakeElement;
                                this.objectNode = editor.restoreChamiloRealElement(fakeElement);
                                this.isInit = true;
                                this.setupContent(this.objectNode);
                                this.isInit = false;
                                // updatePreview(this, false);
                            }
                        },
                        onLoad : function()
                        {
                            var doc = this._.element.getDocument();

                            if (this.getContentElement('info', 'ratioLock'))
                            {
                                this.addFocusable(doc.getById(btnResetSizeId), 5);
                                this.addFocusable(doc.getById(btnLockSizesId), 5);
                            }
                        },
                        onHide : function()
                        {
                            if (this.preview)
                            {
                                this.preview.setHtml('');
                            }

                            if (this.originalElement)
                            {
                                this.originalElement.removeListener('load', onImgLoadEvent);
                                this.originalElement.removeListener('error', onImgLoadErrorEvent);
                                this.originalElement.removeListener('abort', onImgLoadErrorEvent);
                                this.originalElement.remove();
                                this.originalElement = false; // Dialog is
                                // closed.
                            }

                            delete currentRendition;
                            delete blockUpdatePreview;
                            delete this.fakeImage;
                            delete this.objectNode;
                        },
                        contents : [ {
                            id : 'info',
                            label : editor.lang.image.infoTab,
                            accessKey : 'I',
                            elements : [
                                    {
                                        type : 'vbox',
                                        padding : 0,
                                        children : [ {
                                            id : 'source',
                                            type : 'text',
                                            hidden : true,
                                            required : true,
                                            onChange : function()
                                            {
                                                var sourceDialog = this.getDialog();

                                                updatePreview(sourceDialog, false);

                                                var original = sourceDialog.originalElement;
                                                original.setCustomData('isReady', 'false');
                                                original.on('load', onImgLoadEvent, sourceDialog);
                                                original.on('error', onImgLoadErrorEvent, sourceDialog);
                                                original.on('abort', onImgLoadErrorEvent, sourceDialog);
                                                original.setAttribute('src', $(currentRendition).attr("src"));

                                                var width, height;

                                                if (sourceDialog.objectNode)
                                                {
                                                    var width = sourceDialog.objectNode.getAttribute('width');
                                                    var height = sourceDialog.objectNode.getAttribute('height');
                                                }

                                                if (!width && !height)
                                                {
                                                    blockUpdatePreview = true;
                                                    var img = $(currentRendition).attr("src");

                                                    if (img)
                                                    {
                                                        var originalImage = $("<img/>").attr("src", img);
                                                        originalImage.load(function()
                                                        {
                                                            sourceDialog.setValueOf('info', 'width', this.width);
                                                            sourceDialog.setValueOf('info', 'height', this.height);
                                                            blockUpdatePreview = false;
                                                        });
                                                    }
                                                    else
                                                    {
                                                        sourceDialog.setValueOf('info', 'width', '320');
                                                        sourceDialog.setValueOf('info', 'height', '240');
                                                        blockUpdatePreview = false;
                                                    }
                                                }

                                            },
                                            setup : function(element)
                                            {
                                                this.getDialog().dontResetSize = true;
                                                this.setValue(element.getAttribute('source'));
                                                this.setInitValue();
                                            },
                                            commit : function(element)
                                            {
                                                element.setAttribute('source', this.getValue());
                                            },
                                            validate : CKEDITOR.dialog.validate.notEmpty(editor.lang.image.urlMissing)
                                        }, {
                                            id : 'type',
                                            type : 'text',
                                            hidden : true,
                                            required : true,
                                            setup : function(element)
                                            {
                                                this.setValue(element.getAttribute('type'));
                                            },
                                            commit : function(element)
                                            {
                                                element.setAttribute('type', this.getValue());
                                            },
                                            validate : CKEDITOR.dialog.validate.notEmpty(editor.lang.image.urlMissing)
                                        }, {
                                            id : 'security_code',
                                            type : 'text',
                                            hidden : true,
                                            required : true,
                                            setup : function(element)
                                            {
                                                this.setValue(element.getAttribute('security_code'));
                                            },
                                            commit : function(element)
                                            {
                                                element.setAttribute('security_code', this.getValue());
                                            },
                                            validate : CKEDITOR.dialog.validate.notEmpty(editor.lang.image.urlMissing)
                                        } ]
                                    },
                                    {
                                        id : 'alt',
                                        type : 'text',
                                        label : editor.lang.image.alt,
                                        accessKey : 'T',
                                        'default' : '',
                                        setup : function(element)
                                        {
                                            this.setValue(element.getAttribute('alt'));
                                        },
                                        commit : function(element)
                                        {
                                            element.setAttribute('alt', this.getValue());
                                        }
                                    },
                                    {
                                        type : 'hbox',
                                        children : [
                                                {
                                                    id : 'basic',
                                                    type : 'vbox',
                                                    children : [
                                                            {
                                                                type : 'hbox',
                                                                widths : [ '50%', '50%' ],
                                                                children : [
                                                                        {
                                                                            type : 'vbox',
                                                                            padding : 1,
                                                                            children : [ {
                                                                                type : 'text',
                                                                                width : '40px',
                                                                                id : 'width',
                                                                                label : editor.lang.common.width,
                                                                                onKeyUp : function(event)
                                                                                {
                                                                                    var currentElement = this;

                                                                                    if (event.keyCode == 13)
                                                                                    {
                                                                                        event.preventDefault();
                                                                                        onSizeChange(currentElement);
                                                                                    }
                                                                                    else
                                                                                    {
                                                                                        clearTimeout(timer);
                                                                                        timer = setTimeout(function()
                                                                                        {
                                                                                            onSizeChange(currentElement);
                                                                                        }, 750);
                                                                                    }
                                                                                },
                                                                                setup : setupDimension,
                                                                                commit : function(element)
                                                                                {
                                                                                    element.setAttribute('width', this.getValue());
                                                                                }
                                                                            }, {
                                                                                type : 'text',
                                                                                id : 'height',
                                                                                width : '40px',
                                                                                label : editor.lang.common.height,
                                                                                onKeyUp : function(event)
                                                                                {
                                                                                    var currentElement = this;

                                                                                    if (event.keyCode == 13)
                                                                                    {
                                                                                        event.preventDefault();
                                                                                        onSizeChange(currentElement);
                                                                                    }
                                                                                    else
                                                                                    {
                                                                                        clearTimeout(timer);
                                                                                        timer = setTimeout(function()
                                                                                        {
                                                                                            onSizeChange(currentElement);
                                                                                        }, 750);
                                                                                    }
                                                                                },
                                                                                setup : setupDimension,
                                                                                commit : function(element)
                                                                                {
                                                                                    element.setAttribute('height', this.getValue());
                                                                                }
                                                                            } ]
                                                                        },
                                                                        {
                                                                            id : 'ratioLock',
                                                                            type : 'html',
                                                                            style : 'margin-top:30px;width:40px;height:40px;',
                                                                            onLoad : function()
                                                                            {
                                                                                // Activate
                                                                                // Reset
                                                                                // button
                                                                                var resetButton = CKEDITOR.document.getById(btnResetSizeId), ratioButton = CKEDITOR.document.getById(btnLockSizesId);
                                                                                if (resetButton)
                                                                                {
                                                                                    resetButton.on('click', function(evt)
                                                                                    {
                                                                                        resetSize(this);
                                                                                        evt.data && evt.data.preventDefault();
                                                                                    }, this.getDialog());
                                                                                    resetButton.on('mouseover', function()
                                                                                    {
                                                                                        this.addClass('cke_btn_over');
                                                                                    }, resetButton);
                                                                                    resetButton.on('mouseout', function()
                                                                                    {
                                                                                        this.removeClass('cke_btn_over');
                                                                                    }, resetButton);
                                                                                }
                                                                                // Activate
                                                                                // (Un)LockRatio
                                                                                // button
                                                                                if (ratioButton)
                                                                                {
                                                                                    ratioButton.on('click', function(evt)
                                                                                    {
                                                                                        var locked = switchLockRatio(this), oImageOriginal = this.originalElement, width = this.getValueOf('info', 'width');

                                                                                        if (oImageOriginal.getCustomData('isReady') == 'true' && width)
                                                                                        {
                                                                                            var height = oImageOriginal.$.height / oImageOriginal.$.width * width;
                                                                                            if (!isNaN(height))
                                                                                            {
                                                                                                this.setValueOf('info', 'height', Math.round(height));
                                                                                                updatePreview(this.getDialog());
                                                                                            }
                                                                                        }
                                                                                        evt.data && evt.data.preventDefault();
                                                                                    }, this.getDialog());
                                                                                    ratioButton.on('mouseover', function()
                                                                                    {
                                                                                        this.addClass('cke_btn_over');
                                                                                    }, ratioButton);
                                                                                    ratioButton.on('mouseout', function()
                                                                                    {
                                                                                        this.removeClass('cke_btn_over');
                                                                                    }, ratioButton);
                                                                                }
                                                                            },
                                                                            html : '<div>' + '<a href="javascript:void(0)" tabindex="-1" title="' + editor.lang.image.lockRatio + '" class="cke_btn_locked" id="' + btnLockSizesId + '" role="checkbox"><span class="cke_icon"></span><span class="cke_label">' + editor.lang.image.lockRatio + '</span></a>'
                                                                                    + '<a href="javascript:void(0)" tabindex="-1" title="' + editor.lang.image.resetSize + '" class="cke_btn_reset" id="' + btnResetSizeId + '" role="button"><span class="cke_label">' + editor.lang.image.resetSize + '</span></a>' + '</div>'
                                                                        } ]
                                                            }, {
                                                                type : 'vbox',
                                                                padding : 1,
                                                                children : [ {
                                                                    type : 'text',
                                                                    id : 'border',
                                                                    width : '60px',
                                                                    label : editor.lang.image.border,
                                                                    'default' : '',
                                                                    onChange : function()
                                                                    {
                                                                        if (!blockUpdatePreview)
                                                                        {
                                                                            updatePreview(this.getDialog(), true);
                                                                        }
                                                                    },
                                                                    setup : function(element)
                                                                    {
                                                                        blockUpdatePreview = true;
                                                                        this.setValue(element.getAttribute('border'));
                                                                        blockUpdatePreview = false;
                                                                    },
                                                                    commit : function(element)
                                                                    {
                                                                        element.setAttribute('border', this.getValue());
                                                                    }
                                                                }, {
                                                                    type : 'text',
                                                                    id : 'margin-horizontal',
                                                                    width : '60px',
                                                                    label : editor.lang.image.hSpace,
                                                                    'default' : '',
                                                                    onChange : function()
                                                                    {
                                                                        if (!blockUpdatePreview)
                                                                        {
                                                                            updatePreview(this.getDialog(), true);
                                                                        }
                                                                    },
                                                                    setup : function(element)
                                                                    {
                                                                        blockUpdatePreview = true;
                                                                        this.setValue(element.getAttribute('margin-horizontal'));
                                                                        blockUpdatePreview = false;
                                                                    },
                                                                    commit : function(element)
                                                                    {
                                                                        element.setAttribute('margin-horizontal', this.getValue());
                                                                    }
                                                                }, {
                                                                    type : 'text',
                                                                    id : 'margin-vertical',
                                                                    width : '60px',
                                                                    label : editor.lang.image.vSpace,
                                                                    'default' : '',
                                                                    onChange : function()
                                                                    {
                                                                        if (!blockUpdatePreview)
                                                                        {
                                                                            updatePreview(this.getDialog(), true);
                                                                        }
                                                                    },
                                                                    setup : function(element)
                                                                    {
                                                                        blockUpdatePreview = true;
                                                                        this.setValue(element.getAttribute('margin-vertical'));
                                                                        blockUpdatePreview = false;
                                                                    },
                                                                    commit : function(element)
                                                                    {
                                                                        element.setAttribute('margin-vertical', this.getValue());
                                                                    }
                                                                }, {
                                                                    id : 'align',
                                                                    type : 'select',
                                                                    widths : [ '35%', '65%' ],
                                                                    style : 'width:90px',
                                                                    label : editor.lang.common.align,
                                                                    'default' : '',
                                                                    items : [ [ editor.lang.common.notSet, '' ], [ editor.lang.common.alignLeft, 'left' ], [ editor.lang.common.alignRight, 'right' ] ],
                                                                    onChange : function()
                                                                    {
                                                                        if (!blockUpdatePreview)
                                                                        {
                                                                            updatePreview(this.getDialog(), true);
                                                                        }

                                                                    },
                                                                    setup : function(element)
                                                                    {
                                                                        blockUpdatePreview = true;
                                                                        this.setValue(element.getAttribute('align'));
                                                                        blockUpdatePreview = false;
                                                                    },
                                                                    commit : function(element)
                                                                    {
                                                                        element.setAttribute('align', this.getValue());
                                                                    }
                                                                } ]
                                                            } ]
                                                }, {
                                                    type : 'vbox',
                                                    height : '250px',
                                                    children : [ {
                                                        type : 'html',
                                                        id : 'htmlPreview',
                                                        style : 'width:95%;',
                                                        html : '<div>' + CKEDITOR.tools.htmlEncode(editor.lang.common.preview) + '<br>' + '<div style="white-space: normal;border: 2px ridge black;overflow: scroll;height: 160px;width: 230px;padding: 2px;background-color: white;" id="' + previewImageId + '"></div></div>'
                                                    } ]
                                                } ]
                                    } ]
                        } ]
                    };
                });