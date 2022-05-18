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
    public static function getDefaultPropertyNames($extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            array(self::PROPERTY_NAME, self::PROPERTY_DESCRIPTION, self::PROPERTY_USER_ID));
    }

    /**
     *
     * @return string
     */
    public function get_name()
    {
        return $this->getDefaultProperty(self::PROPERTY_NAME);
    }

    /**
     *
     * @return string
     */
    public function get_description()
    {
        return $this->getDefaultProperty(self::PROPERTY_DESCRIPTION);
    }

    /**
     *
     * @param string $name
     */
    public function set_name($name)
    {
        $this->setDefaultProperty(self::PROPERTY_NAME, $name);
    }

    /**
     *
     * @param string $description
     */
    public function set_description($description)
    {
        $this->setDefaultProperty(self::PROPERTY_DESCRIPTION, $description);
    }

    /**
     *
     * @return int
     */
    public function get_user_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    /**
     *
     * @param int $user_id
     */
    public function set_user_id($user_id)
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $user_id);
    }


    public function get_dependencies($dependencies = [])
    {
        return array(
            UserViewRelContentObject::class => new EqualityCondition(
                new PropertyConditionVariable(
                    UserViewRelContentObject::class,
                    UserViewRelContentObject::PROPERTY_USER_VIEW_ID), 
                new StaticConditionVariable($this->get_id())));
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'repository_user_view';
    }
}