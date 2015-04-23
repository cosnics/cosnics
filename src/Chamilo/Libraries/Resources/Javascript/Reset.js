function resetCKEditorHtmlEditor()
{
    // If the API is not detected, there shouldn't be any editors
    if (typeof CKEDITOR === "undefined")
    {
    	return;
    }

    // Loop through all the editor's instances
    for (var sEditorName in CKEDITOR.instances)
    {
        // Get the initial value
        var sInitialValue = CKEDITOR.instances[sEditorName].element.getValue();

        // Overwrite the editor's current value
        CKEDITOR.instances[sEditorName].setData(sInitialValue);
    }
}

function resetAdvancedMultiSelect()
{
	
}

function resetElements()
{
	resetCKEditorHtmlEditor()
	resetAdvancedMultiSelect();
}