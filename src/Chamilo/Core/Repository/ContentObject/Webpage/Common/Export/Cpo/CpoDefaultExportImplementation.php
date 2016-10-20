<?php
namespace Chamilo\Core\Repository\ContentObject\Webpage\Common\Export\Cpo;

use Chamilo\Core\Repository\Common\Export\ContentObjectExport;
use Chamilo\Core\Repository\ContentObject\Webpage\Common\Export\CpoExportImplementation;

class CpoDefaultExportImplementation extends CpoExportImplementation
{

    public function render()
    {
        ContentObjectExport :: launch($this);
        $this->get_context()->add_files(
            $this->get_content_object()->get_full_path(), 
            'data/' . $this->get_content_object()->get_hash());
    }
}
