<?php
namespace Chamilo\Core\Repository\Template;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;

abstract class Manager extends Application
{
    const PARAM_ACTION = 'template_action';
    const PARAM_CONTENT_OBJECT_ID = 'id';
    const PARAM_DELETE = 'delete';
    const PARAM_COPY_FROM_TEMPLATES = 'copy_template';
    const PARAM_TEMPLATE_ID = 'template_id';
    const ACTION_IMPORT = 'Importer';
    const ACTION_DELETE = 'Deleter';
    const ACTION_BROWSE = 'Browser';
    const ACTION_CREATE = 'Creator';
    const ACTION_COPY = 'Copier';
    const DEFAULT_ACTION = self :: ACTION_BROWSE;

    public function get_delete_template_url($template_id)
    {
        return $this->get_url(
            array(self :: PARAM_ACTION => self :: ACTION_DELETE, self :: PARAM_CONTENT_OBJECT_ID => $template_id));
    }

    public function get_import_template_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_IMPORT));
    }

    public function get_allowed_content_object_types()
    {
        $types = $this->get_content_object_types(true);

        foreach ($types as $index => $type)
        {
            $registration = \Chamilo\Configuration\Storage\DataManager :: get_registration(
                ClassnameUtilities :: getInstance()->getNamespaceFromClassname($type));

            if (! $registration || ! $registration->is_active())
            {
                unset($types[$index]);
            }
        }

        return $types;
    }

    public function get_content_object_types($check_view_right = true)
    {
        return \Chamilo\Core\Repository\Storage\DataManager :: get_registered_types($check_view_right);
    }

    /**
     * Gets the url for browsing objects of a given type
     *
     * @param int $template_registration_id
     * @return string The url
     */
    public function get_type_filter_url($template_registration_id)
    {
        return $this->get_application()->get_type_filter_url($template_registration_id);
    }

    public function get_content_object_viewing_url($content_object)
    {
        return $this->get_application()->get_content_object_viewing_url($content_object);
    }

    public function get_copy_content_object_url($content_object_id)
    {
        return $this->get_application()->get_copy_content_object_url($content_object_id);
    }
}
