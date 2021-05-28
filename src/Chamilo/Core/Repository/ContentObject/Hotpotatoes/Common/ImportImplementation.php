<?php
namespace Chamilo\Core\Repository\ContentObject\Hotpotatoes\Common;

use Chamilo\Core\Repository\Common\Import\ContentObjectImport;
use Chamilo\Core\Repository\Common\Import\ContentObjectImportImplementation;

class ImportImplementation extends ContentObjectImportImplementation
{

    public static function get_types(array $types = [])
    {
        return parent::get_types(array(ContentObjectImport::FORMAT_HOTPOTATOES));
    }
}
