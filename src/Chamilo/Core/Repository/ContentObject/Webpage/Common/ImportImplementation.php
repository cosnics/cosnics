<?php
namespace Chamilo\Core\Repository\ContentObject\Webpage\Common;

use Chamilo\Core\Repository\Common\Import\ContentObjectImport;
use Chamilo\Core\Repository\Common\Import\ContentObjectImportImplementation;

class ImportImplementation extends ContentObjectImportImplementation
{

    public static function get_types(array $types = [])
    {
        return parent::get_types(array(ContentObjectImport::FORMAT_WEBPAGE));
    }
}
