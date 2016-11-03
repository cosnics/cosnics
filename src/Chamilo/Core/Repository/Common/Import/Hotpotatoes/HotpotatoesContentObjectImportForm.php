<?php
namespace Chamilo\Core\Repository\Common\Import\Hotpotatoes;

use Chamilo\Core\Repository\Common\Import\ContentObjectImport;
use Chamilo\Core\Repository\Form\ContentObjectImportForm;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class HotpotatoesContentObjectImportForm extends ContentObjectImportForm
{

    public function build_basic_form()
    {
        parent :: build_basic_form();
        
        $this->addElement(
            'file', 
            self :: IMPORT_FILE_NAME, 
            Translation :: get('FileName', null, Utilities :: COMMON_LIBRARIES));
    }

    public function setDefaults($defaults = array ())
    {
        parent :: setDefaults(array(self :: PROPERTY_TYPE => ContentObjectImport :: FORMAT_HOTPOTATOES));
    }
}
