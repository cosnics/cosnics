<?php
namespace Chamilo\Core\Repository\Table\ContentObject\Gallery;

use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Table\Extension\GalleryTable\Extension\DataClassGalleryTable\DataClassGalleryTable;

class GalleryTable extends DataClassGalleryTable
{

    private $type;

    public function __construct($component)
    {
        parent :: __construct($component);

        $template_id = FilterData :: get_instance($this->get_component()->get_repository_browser()->getWorkspace())->get_type();

        if (! $template_id || ! is_numeric($template_id))
        {
            $this->type = ContentObject :: class_name();
        }
        else
        {
            $template_registration = \Chamilo\Core\Repository\Configuration :: registration_by_id($template_id);

            $this->type = $template_registration->get_content_object_type() . '\\' . ClassnameUtilities :: getInstance()->getPackageNameFromNamespace(
                $template_registration->get_content_object_type(),
                true);
        }
    }

    public function get_type()
    {
        return $this->type;
    }
}
