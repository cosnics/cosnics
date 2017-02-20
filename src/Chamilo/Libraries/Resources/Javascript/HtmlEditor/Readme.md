##Update version of ckeditor

The Ckeditor subfolder contains the optimized release.
The CkeditorExtra subfolder contains custom cosnics plugins and the build configuration

To update to a new version:
* upload the build-config.js to http://ckeditor.com/builder
* Download the optimized build
* Copy the new release in the CKeditor folder.
* Delete the sample folder
* Change CKEDITOR.timestamp='v1' for cache busting. src/Chamilo/Libraries/Resources/Javascript/HtmlEditor/Ckeditor.js

Change skin:
* Change default skin in Chamilo/Libraries/Format/Form/FormValidatorCkeditorHtmlEditorOptions.php
