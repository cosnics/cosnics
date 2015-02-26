<?php
namespace Chamilo\Core\Repository\External\General\Streaming;

use Chamilo\Core\Repository\External\Table\ExternalObjectGallery\DefaultExternalObjectGalleryTableCellRenderer;
use Chamilo\Libraries\Utilities\Utilities;

class StreamingMediaExternalObjectGalleryTableCellRenderer extends DefaultExternalObjectGalleryTableCellRenderer
{

    public function get_cell_content($object)
    {
        $html = array();
        $html[] = '<h4>' . Utilities :: truncate_string($object->get_title(), 25) . ' (' . Utilities :: format_seconds_to_minutes(
            $object->get_duration()) . ')</h4>';
        $html[] = '<a href="' . $this->browser->get_external_repository_object_viewing_url($object) .
             '"><img class="thumbnail" src="' . $object->get_thumbnail() . '"/></a> <br/>';
        $html[] = '<i>' . Utilities :: truncate_string($object->get_description(), 100) . '</i><br/>';
        return implode("\n", $html);
    }
}
