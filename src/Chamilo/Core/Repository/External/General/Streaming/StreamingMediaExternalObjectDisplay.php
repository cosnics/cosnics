<?php
namespace Chamilo\Core\Repository\External\General\Streaming;

use Chamilo\Core\Repository\External\ExternalObjectDisplay;

abstract class StreamingMediaExternalObjectDisplay extends ExternalObjectDisplay
{

    public function get_title()
    {
        $object = $this->get_object();
        return '<h3>' . $object->get_title() . ' (' . $object->get_duration() . ')</h3>';
    }
}
