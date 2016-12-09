<?php
namespace Chamilo\Core\Repository\ContentObject\Hotpotatoes\Common\Export\Cpo;

use Chamilo\Core\Repository\Common\Export\ContentObjectExport;
use Chamilo\Core\Repository\ContentObject\Hotpotatoes\Common\Export\CpoExportImplementation;

class CpoDefaultExportImplementation extends CpoExportImplementation
{

    public function render()
    {
        ContentObjectExport::launch($this);
        $this->get_context()->add_files(
            dirname($this->get_content_object()->get_full_path()), 
            'hotpotatoes/' . basename(rtrim(dirname($this->get_content_object()->get_full_path()), '/')));
    }
}
