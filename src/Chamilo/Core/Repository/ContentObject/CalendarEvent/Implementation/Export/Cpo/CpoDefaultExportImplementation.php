<?php
namespace Chamilo\Core\Repository\ContentObject\CalendarEvent\Implementation\Export\Cpo;

use Chamilo\Core\Repository\Common\Export\ContentObjectExport;
use Chamilo\Core\Repository\ContentObject\CalendarEvent\Implementation\Export\CpoExportImplementation;

class CpoDefaultExportImplementation extends CpoExportImplementation
{

    public function render()
    {
        ContentObjectExport :: launch($this);
    }
}
