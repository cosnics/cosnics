<?php
namespace Chamilo\Libraries\Calendar\Event;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package libraries\calendar\event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Visibility extends DataClass
{
    const CLASS_NAME = __CLASS__;

    // Properties
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_SOURCE = 'source';

    /**
     *
     * @var \core\user\User
     */
    private $user;

    /**
     * Get the default properties of a Visibility DataClass
     *
     * @return array The property names.
     */
    public static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_USER_ID, self :: PROPERTY_SOURCE));
    }

    /**
     *
     * @return \libraries\storage\data_manager\DataManager
     */
    public function get_data_manager()
    {
    }

    /**
     *
     * @return int
     */
    public function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     *
     * @return string
     */
    public function get_source()
    {
        return $this->get_default_property(self :: PROPERTY_SOURCE);
    }

    /**
     *
     * @param int $id
     */
    public function set_user_id($id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $id);
    }

    /**
     *
     * @param string $source
     */
    public function set_source($source)
    {
        $this->set_default_property(self :: PROPERTY_SOURCE, $source);
    }

    /**
     *
     * @return \core\user\User
     */
    public function get_user()
    {
        if (isset($this->user))
        {
            $this->user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
                (int) $this->get_user_id());
        }

        return $this->user;
    }
}
