<?php
namespace Chamilo\Core\Repository\ContentObject\CalendarEvent\Common\Export\Cpo;

use Chamilo\Core\Repository\Common\Export\ContentObjectExport;
use Chamilo\Core\Repository\ContentObject\CalendarEvent\Common\Export\CpoExportImplementation;

class CpoDefaultExportImplementation extends CpoExportImplementation
{

    public function render()
    {
        ContentObjectExport::launch($this);
    }
}
