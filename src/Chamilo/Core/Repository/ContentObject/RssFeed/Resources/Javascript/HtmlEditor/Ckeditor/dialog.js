/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/
CKEDITOR.dialog.add( 'rss_feedDialog', function( editor )
{
    function commitContent()
    {
        var args = arguments;
        
        this.foreach( function( widget )
        {
            if ( widget.commit )
                widget.commit.apply( widget, args );
        });
    }
	function concatObject(obj)
	{
	    str='';
	    for(prop in obj)
	    {
	      str+=prop + " value :"+ obj[prop] + "\n";
	    }
	    return(str);
	}
    function getRendition(objectId, parameters, securityCode)
    {
    	var ajaxUri = getPath('WEB_PATH') + 'index.php';
    	var rendition = '';
    	var parameters = {
    	    'application' : 'Chamilo\\Core\\Repository\\Ajax',
    	    'go' : 'rendition_implementation',
    	    'content_object_id' : objectId,
			'security_code': securityCode,
    	    'format' : 'html',
    	    'view' : 'inline', 
    	    'parameters' : parameters
    	};

    	var response = $.ajax({
    	    type : "POST",
    	    url : ajaxUri,
    	    data : parameters,
    	    async : false
    	}).success(function(json) {
    	    rendition = json.properties.rendition;
    	});
    	return rendition;
    };
    
    var numbering = function( id )
	{
		return CKEDITOR.tools.getNextId() + '_' + id;
	};
    
    var btnLockSizesId = numbering( 'btnLockSizes' ),
		btnResetSizeId = numbering( 'btnResetSize' );
    
    var previewImageId = numbering( 'previewImage' );
    
    var text = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis a felis in nulla luctus feugiat vitae sit amet justo. Phasellus elementum odio id neque dapibus sit amet pulvinar diam aliquet. Quisque et condimentum magna. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Phasellus faucibus dui vel nisi lobortis id dignissim neque interdum. Mauris vitae leo risus. Aliquam erat volutpat. Phasellus quam risus, sodales vitae rhoncus sed, aliquet in elit. Phasellus vulputate neque eu tortor aliquam eu dapibus diam gravida. Nam leo erat, vestibulum sit amet malesuada sit amet, tempor nec eros. Curabitur dignissim laoreet massa nec cursus. Aenean bibendum rutrum lorem sed mollis. Pellentesque imperdiet ligula sit amet diam blandit eget faucibus tellus consequat. Pellentesque tristique elit sagittis orci pretium faucibus. Nullam vestibulum imperdiet ante id hendrerit.';
	
    function getParameters(dialog)
    {
    	var parameters = {};
//    	parameters.width = dialog.getValueOf('info', 'txtWidth');
//    	parameters.height = dialog.getValueOf('info', 'txtHeight');
    	return parameters;
    }
    
    function updatePreview(dialog)
    {
    	var newObjectId = dialog.getValueOf('info', 'source');
		var securityCode = dialog.getValueOf('info', 'security_code');

		var rendition = getRendition(newObjectId, getParameters(dialog), securityCode);
    	dialog.preview.setHtml(rendition + text);
    }
    
    return {
        title : 'RssFeed Properties',
        minWidth : 400,
        minHeight : 200,
        onOk : function(evt)
        {
            resourceElement = editor.document.createElement( 'resource' );
            this.commitContent( resourceElement );

            var newFakeImage = editor.createChamiloFakeElement( resourceElement, 'cke_chamilo_rss_feed', 'chamilo' );
//			newFakeImage.setAttribute('alt', this.getValueOf('info', 'txtAlt'));
//			newFakeImage.setAttribute('title', this.getValueOf('info', 'txtAlt'));
            editor.insertElement( newFakeImage );  
        },
        onShow : function()
		{
        	this.preview = CKEDITOR.document.getById( previewImageId );
		},
        contents : [
    				{
    					id : 'info',
    					label : editor.lang.image.infoTab,
    					accessKey : 'I',
    					elements :
    					[
    						{
    							type : 'vbox',
    							padding : 0,
    							children :
    							[
    								{
										id : 'source',
										type : 'text',
										hidden:true,
										required: true,
										onChange : function()
										{
											updatePreview(this.getDialog());
										},
										commit : function( element )
										{
											element.setAttribute( 'source', this.getValue() );
										},
										validate : CKEDITOR.dialog.validate.notEmpty( editor.lang.image.urlMissing )
    								},
    								{
										id : 'type',
										type : 'text',
										hidden:true,
										required: true,
										commit : function( element )
										{
											element.setAttribute( 'type', this.getValue() );
										},
										validate : CKEDITOR.dialog.validate.notEmpty( editor.lang.image.urlMissing )
    								},
									{
										id : 'security_code',
										type : 'text',
										hidden:true,
										required: true,
										commit : function( element )
										{
											element.setAttribute( 'security_code', this.getValue() );
										},
										validate : CKEDITOR.dialog.validate.notEmpty( editor.lang.image.urlMissing )
									}
    							]
    						},				
    								{
    									type : 'vbox',
    									height : '250px',
    									children :
    									[
    										{
    											type : 'html',
    											id : 'htmlPreview',
    											style : 'width:95%;',
    											html : '<div style="white-space: normal;border: 2px ridge black;overflow: scroll;height: 160px;width: 230px;padding: 2px;background-color: white;" id="' + previewImageId + '"></div>'
    										}
    									]
    								}
    					]
    				}
        ]
    };
} );
