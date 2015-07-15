<?php
namespace Chamilo\Application\Calendar\Extension\Google\Storage\DataClass;

use Chamilo\Application\Calendar\Extension\Personal\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * A content object publication in the personal calendar application
 *
 * @package application\calendar$Publication
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Dieter De Neef
 */
class Visibility extends DataClass
{
    const CLASS_NAME = __CLASS__;

    // Properties
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_CALENDAR_ID = 'calendar_id';
    const PROPERTY_VISIBILITY = 'visibility';

    /**
     *
     * @return string[]
     */
    public static function get_default_property_names()
    {
        return parent :: get_default_property_names(
            array(self :: PROPERTY_USER_ID, self :: PROPERTY_CALENDAR_ID, self :: PROPERTY_VISIBILITY));
    }

    public function get_data_manager()
    {
        return DataManager :: get_instance();
    }

    /**
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     *
     * @param integer $userId
     */
    public function setUserId($userId)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $userId);
    }

    /**
     *
     * @return string
     */
    public function getCalendarId()
    {
        return $this->get_default_property(self :: PROPERTY_CALENDAR_ID);
    }

    /**
     *
     * @param string $calendarId
     */
    public function setCalendarId($calendarId)
    {
        $this->set_default_property(self :: PROPERTY_CALENDAR_ID, $calendarId);
    }

    /**
     *
     * @return integer
     */
    public function getVisibility()
    {
        return $this->get_default_property(self :: PROPERTY_VISIBILITY);
    }

    /**
     *
     * @param integer $visibility
     */
    public function setVisibility($visibility)
    {
        $this->set_default_property(self :: PROPERTY_VISIBILITY, $visibility);
    }
}
