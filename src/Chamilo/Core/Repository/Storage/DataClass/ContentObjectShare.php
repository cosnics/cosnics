<?php
namespace Chamilo\Core\Repository\Storage\DataClass;

use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package repository.lib
 */
/**
 *
 * @author Sven Vanpoucke
 */
class ContentObjectShare extends DataClass
{
    const SEARCH_RIGHT = 1;
    const VIEW_RIGHT = 2;
    const USE_RIGHT = 3;
    const REUSE_RIGHT = 4;
    const CLASS_NAME = __CLASS__;
    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    const PROPERTY_RIGHT_ID = 'right_id';
    const PARAM_TYPE = 'share_type';

    public function get_content_object_id()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT_ID);
    }

    public function set_content_object_id($content_object_id)
    {
        $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
    }

    public function get_right_id()
    {
        return $this->get_default_property(self :: PROPERTY_RIGHT_ID);
    }

    public function set_right_id($right_id)
    {
        $this->set_default_property(self :: PROPERTY_RIGHT_ID, $right_id);
    }

    /**
     * Get the default properties of all groups.
     * 
     * @return array The property names.
     */
    public static function get_default_property_names($additional_property_names = array())
    {
        $additional_property_names[] = self :: PROPERTY_CONTENT_OBJECT_ID;
        $additional_property_names[] = self :: PROPERTY_RIGHT_ID;
        return $additional_property_names;
    }

    public static function get_rights()
    {
        $rights = array();
        
        if (! PlatformSetting :: get('all_objects_searchable', Manager :: context()))
        {
            $rights[self :: SEARCH_RIGHT] = Translation :: get('Search', null, Utilities :: COMMON_LIBRARIES);
        }
        
        $rights[self :: VIEW_RIGHT] = Translation :: get('View', null, Utilities :: COMMON_LIBRARIES);
        $rights[self :: USE_RIGHT] = Translation :: get('Use', null, Utilities :: COMMON_LIBRARIES);
        $rights[self :: REUSE_RIGHT] = Translation :: get('Reuse', null, Utilities :: COMMON_LIBRARIES);
        
        return $rights;
    }

    public function has_right($right_id)
    {
        return $this->get_right_id() >= $right_id;
    }
}
