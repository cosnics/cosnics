<?php
namespace Chamilo\Core\Repository\ContentObject\Task\Implementation\Export\Cpo;

use Chamilo\Core\Repository\Common\Export\ContentObjectExport;
use Chamilo\Core\Repository\ContentObject\Task\Implementation\Export\CpoExportImplementation;

class CpoDefaultExportImplementation extends CpoExportImplementation
{

    public function render()
    {
        ContentObjectExport :: launch($this);
    }
}
