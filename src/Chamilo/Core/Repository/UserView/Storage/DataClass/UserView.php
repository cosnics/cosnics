<?php
namespace Chamilo\Core\Repository\UserView\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package core\repository\user_view
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class UserView extends DataClass
{
    
    // Properties
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_NAME = 'name';
    const PROPERTY_DESCRIPTION = 'description';

    /**
     *
     * @return string[]
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(self::PROPERTY_NAME, self::PROPERTY_DESCRIPTION, self::PROPERTY_USER_ID));
    }

    /**
     *
     * @return string
     */
    public function get_name()
    {
        return $this->get_default_property(self::PROPERTY_NAME);
    }

    /**
     *
     * @return string
     */
    public function get_description()
    {
        return $this->get_default_property(self::PROPERTY_DESCRIPTION);
    }

    /**
     *
     * @param string $name
     */
    public function set_name($name)
    {
        $this->set_default_property(self::PROPERTY_NAME, $name);
    }

    /**
     *
     * @param string $description
     */
    public function set_description($description)
    {
        $this->set_default_property(self::PROPERTY_DESCRIPTION, $description);
    }

    /**
     *
     * @return int
     */
    public function get_user_id()
    {
        return $this->get_default_property(self::PROPERTY_USER_ID);
    }

    /**
     *
     * @param int $user_id
     */
    public function set_user_id($user_id)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $user_id);
    }

    /**
     *
     * @see \libraries\storage\DataClass::get_dependencies()
     */
    public function get_dependencies()
    {
        return array(
            UserViewRelContentObject::class_name() => new EqualityCondition(
                new PropertyConditionVariable(
                    UserViewRelContentObject::class_name(), 
                    UserViewRelContentObject::PROPERTY_USER_VIEW_ID), 
                new StaticConditionVariable($this->get_id())));
    }
}