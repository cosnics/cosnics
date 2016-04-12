<?php
namespace Chamilo\Core\Repository\Implementation\Youtube\Table\ExternalObjectGallery;

use Chamilo\Core\Repository\External\Table\ExternalObjectGallery\DefaultExternalObjectGalleryTableCellRenderer;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;

class ExternalObjectGalleryTableCellRenderer extends DefaultExternalObjectGalleryTableCellRenderer
{

    public function render_cell($object)
    {
        $html = array();
        $html[] = '<h4>' . StringUtilities :: getInstance()->truncate($object->get_title(), 25) . ' (' . DatetimeUtilities :: format_seconds_to_minutes(
            $object->get_duration()) . ')</h4>';
        $html[] = '<a href="' . $this->get_component()->get_external_repository_object_viewing_url($object) .
             '"><img class="thumbnail" src="' . $object->get_thumbnail() . '"/></a> <br/>';
        $html[] = '<i>' . StringUtilities :: getInstance()->truncate($object->get_description(), 100) . '</i><br/>';
        $html[] = '<div class="thumbnail_action">';
        $html[] = $this->get_actions($object);
        $html[] = '</div>';
        return implode(PHP_EOL, $html);
    }
}
