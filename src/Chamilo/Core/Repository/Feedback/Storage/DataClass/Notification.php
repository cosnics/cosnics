<?php
namespace Chamilo\Core\Repository\Feedback\Storage\DataClass;

use Chamilo\Core\Repository\Feedback\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Portfolio feedback object
 *
 * @package repository\content_object\portfolio\feedback
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Notification extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_CREATION_DATE = 'creation_date';
    public const PROPERTY_MODIFICATION_DATE = 'modification_date';
    public const PROPERTY_USER_ID = 'user_id';

    /**
     * @var User
     */
    private $user;

    /**
     * Get the default properties of all feedback
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [self::PROPERTY_USER_ID, self::PROPERTY_CREATION_DATE, self::PROPERTY_MODIFICATION_DATE]
        );
    }

    /**
     * Returns creation_date
     *
     * @return int the creation_date
     */
    public function get_creation_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_CREATION_DATE);
    }

    /**
     * Returns modification_date
     *
     * @return int the modification_date
     */
    public function get_modification_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_MODIFICATION_DATE);
    }

    /**
     * @return User
     */
    public function get_user()
    {
        if (!isset($this->user))
        {
            $this->user = DataManager::retrieve_by_id(
                User::class, $this->get_user_id()
            );
        }

        return $this->user;
    }

    /**
     * @return int
     */
    public function get_user_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    /**
     * Sets the creation_date of this feedback.
     *
     * @param $creation_date int the creation_date.
     */
    public function set_creation_date($creation_date)
    {
        $this->setDefaultProperty(self::PROPERTY_CREATION_DATE, $creation_date);
    }

    /**
     * Sets the modification_date of this feedback.
     *
     * @param $modification_date int the modification_date.
     */
    public function set_modification_date($modification_date)
    {
        $this->setDefaultProperty(self::PROPERTY_MODIFICATION_DATE, $modification_date);
    }

    /**
     * @param int $user_id
     */
    public function set_user_id($user_id)
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $user_id);
    }
}