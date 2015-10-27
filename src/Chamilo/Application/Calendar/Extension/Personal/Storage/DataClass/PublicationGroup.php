<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass;

use Chamilo\Application\Calendar\Extension\Personal\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package application\calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationGroup extends DataClass
{
    // Properties
    const PROPERTY_PUBLICATION = 'publication_id';
    const PROPERTY_GROUP_ID = 'group_id';

    /**
     * Get the default properties
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_PUBLICATION, self :: PROPERTY_GROUP_ID));
    }

    /**
     *
     * @return string[]
     */
    public static function get_cacheable_property_names()
    {
        return array();
    }

    /**
     *
     * @return int
     */
    public function get_publication()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLICATION);
    }

    /**
     *
     * @param int
     */
    public function set_publication($publication)
    {
        $this->set_default_property(self :: PROPERTY_PUBLICATION, $publication);
    }

    /**
     *
     * @return int
     */
    public function get_group_id()
    {
        return $this->get_default_property(self :: PROPERTY_GROUP_ID);
    }

    /**
     *
     * @param int $group_id
     */
    public function set_group_id($group_id)
    {
        $this->set_default_property(self :: PROPERTY_GROUP_ID, $group_id);
    }

    /**
     *
     * @return \libraries\storage\data_manager\DataManager
     */
    public function get_data_manager()
    {
        return DataManager :: get_instance();
    }
}
