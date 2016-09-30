<?php
namespace Chamilo\Core\Repository\ContentObject\Webpage\Common\Import;

use Chamilo\Core\Repository\Common\Import\ContentObjectImport;
use Chamilo\Core\Repository\ContentObject\Webpage\Common\ImportImplementation;

class CpoImportImplementation extends ImportImplementation
{

    public function import()
    {
        $content_object = ContentObjectImport :: launch($this);
        $data_path = $this->get_controller()->get_data_path();
        $content_object->set_temporary_file_path($data_path . $content_object->get_hash());
        return $content_object;
    }

    public function post_import($content_object)
    {
    }
}
