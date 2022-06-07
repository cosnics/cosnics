<?php
namespace Chamilo\Core\Repository\Common\Import\Ical;

use Chamilo\Core\Repository\Common\Import\ContentObjectImport;
use Chamilo\Core\Repository\Form\ContentObjectImportForm;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class IcalContentObjectImportForm extends ContentObjectImportForm
{

    public function build_basic_form()
    {
        parent::build_basic_form();
        
        $this->addElement(
            'file', 
            self::IMPORT_FILE_NAME, 
            Translation::get('FileName', null, StringUtilities::LIBRARIES), 
            'accept=".ics"');
    }

    public function setDefaults($defaults = [], $filter = null)
    {
        parent::setDefaults(array(self::PROPERTY_TYPE => ContentObjectImport::FORMAT_ICAL));
    }
}
