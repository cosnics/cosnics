<?php
namespace Chamilo\Core\Repository\Common\Import\Ical;

use Chamilo\Core\Repository\Common\Import\ContentObjectImportParameters;

class IcalContentObjectImportParameters implements ContentObjectImportParameters
{

    private $calendar_component;

    public function __construct($calendar_component)
    {
        $this->calendar_component = $calendar_component;
    }

    public function get_calendar_component()
    {
        return $this->calendar_component;
    }
}
