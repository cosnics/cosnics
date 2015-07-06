<?php
namespace Chamilo\Core\Repository\Common\Import\File;

use Chamilo\Core\Repository\Common\Import\ContentObjectImport;
use Chamilo\Core\Repository\Form\ContentObjectImportForm;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;

class FileContentObjectImportForm extends ContentObjectImportForm
{
    const PARAM_DOCUMENT_TYPE = 'document_type';
    const PROPERTY_LINK = 'url';
    const DOCUMENT_UPLOAD = 0;
    const DOCUMENT_LINK = 1;

    public function build_basic_form()
    {
        parent :: build_basic_form();

        $this->addElement(
            'radio',
            self :: PARAM_DOCUMENT_TYPE,
            Translation :: get('File'),
            Translation :: get('Upload'),
            self :: DOCUMENT_UPLOAD);

        $this->addElement('html', '<div style="padding-left: 25px; display: block;" id="document_upload">');
        $this->addElement('file', self :: IMPORT_FILE_NAME, null);
        $this->addElement('html', '</div>');

        $this->addElement('radio', self :: PARAM_DOCUMENT_TYPE, null, Translation :: get('Link'), self :: DOCUMENT_LINK);

        $this->addElement('html', '<div style="padding-left: 25px; display: block;" id="document_link">');
        $this->add_textfield(self :: PROPERTY_LINK, null, false);
        $this->addElement('html', '</div>');

        $this->addElement(
            'html',
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath('Chamilo\Core\Repository', true) . 'FileImportForm.js'));
    }

    public function setDefaults($defaults = array ())
    {
        parent :: setDefaults(
            array(
                self :: PARAM_DOCUMENT_TYPE => self :: DOCUMENT_UPLOAD,
                self :: PROPERTY_TYPE => ContentObjectImport :: FORMAT_FILE,
                self :: PROPERTY_LINK => 'http://'));
    }
}
