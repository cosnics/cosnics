<?php
namespace Chamilo\Core\Repository\Common\Import\Webpage;

use Chamilo\Core\Repository\Common\Import\ContentObjectImport;
use Chamilo\Core\Repository\Form\ContentObjectImportForm;
use Chamilo\Libraries\Translation\Translation;

class WebpageContentObjectImportForm extends ContentObjectImportForm
{
    public const DOCUMENT_LINK = 1;

    public const DOCUMENT_UPLOAD = 0;

    public const PARAM_WEBPAGE_TYPE = 'webpage_type';

    public const PROPERTY_LINK = 'url';

    public function build_basic_form()
    {
        parent::build_basic_form();

        $this->addElement(
            'radio', self::PARAM_WEBPAGE_TYPE, Translation::get('Document'), Translation::get('Upload'),
            self::DOCUMENT_UPLOAD
        );

        $this->addElement('html', '<div style="padding-left: 25px; display: block;" id="document_upload">');

        $this->addElement('file', self::IMPORT_FILE_NAME, null, 'accept=".htm,.html"');

        $this->addElement('html', '</div>');

        $this->addElement('radio', self::PARAM_WEBPAGE_TYPE, null, Translation::get('Link'), self::DOCUMENT_LINK);

        $this->addElement('html', '<div style="padding-left: 25px; display: block;" id="document_link">');
        $this->add_textfield(self::PROPERTY_LINK, null, false);
        $this->addElement('html', '</div>');

        $this->addElement(
            'html', $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath('Chamilo\Core\Repository') . 'WebpageImportForm.js'
        )
        );
    }

    public function setDefaults($defaults = [], $filter = null)
    {
        parent::setDefaults(
            [
                self::PARAM_WEBPAGE_TYPE => self::DOCUMENT_UPLOAD,
                self::PROPERTY_TYPE => ContentObjectImport::FORMAT_WEBPAGE,
                self::PROPERTY_LINK => 'http://'
            ]
        );
    }
}
