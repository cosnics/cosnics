<?php

namespace Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\Tracking\Storage\DataClass\SimpleTracker;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * This class tracks the visits to pages
 *
 * @package users.lib.trackers
 *
 * http://branch.vanpouckesven.be/index.php?go=ChangeUser&application=Chamilo%5CCore%5CUser&user_id=22
 */
class AdminUserVisit extends SimpleTracker
{
    const PROPERTY_ADMIN_USER_ID = 'admin_user_id';
    const PROPERTY_USER_VISIT_ID = 'user_visit_id';
    const PROPERTY_VISIT_DATE = 'visit_date';

    public function validate_parameters(array $parameters = array())
    {
    }

    public function run(array $parameters = array())
    {
        $adminUserId = $_SESSION['_as_admin'];
        if(empty($adminUserId))
        {
            return;
        }

        $this->setAdminUserId($adminUserId);
        $this->setUserVisitId($parameters['user_visit_id']);
        $this->setVisitDate(time());
        $this->create();
    }

    /**
     * Inherited
     *
     * @see MainTracker :: empty_tracker
     */
    public function empty_tracker()
    {
        return $this->remove();
    }

    /**
     * @return int
     */
    public function getAdminUserId()
    {
        return $this->get_default_property(self::PROPERTY_ADMIN_USER_ID);
    }

    /**
     * @param int $adminUserId
     *
     * @return $this
     */
    public function setAdminUserId(int $adminUserId)
    {
        $this->set_default_property(self::PROPERTY_ADMIN_USER_ID, $adminUserId);
        return $this;
    }

    /**
     * @return int
     */
    public function getUserVisitId()
    {
        return $this->get_default_property(self::PROPERTY_USER_VISIT_ID);
    }

    /**
     * @param int $userVisitId
     *
     * @return $this
     */
    public function setUserVisitId(int $userVisitId)
    {
        $this->set_default_property(self::PROPERTY_USER_VISIT_ID, $userVisitId);
        return $this;
    }

    /**
     * @return int
     */
    public function getVisitDate()
    {
        return $this->get_default_property(self::PROPERTY_VISIT_DATE);
    }

    /**
     * @param int $visitDateTimeStamp
     *
     * @return $this
     */
    public function setVisitDate(int $visitDateTimeStamp)
    {
        $this->set_default_property(self::PROPERTY_VISIT_DATE, $visitDateTimeStamp);
        return $this;
    }

    /**
     * Inherited
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_ADMIN_USER_ID,
                self::PROPERTY_USER_VISIT_ID,
                self::PROPERTY_VISIT_DATE
            )
        );
    }
}
