<?php
namespace Chamilo\Core\Repository\Feedback\Storage\DataClass;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Portfolio feedback object
 * 
 * @package repository\content_object\portfolio\feedback
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Notification extends DataClass
{
    
    // Properties
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_CREATION_DATE = 'creation_date';
    const PROPERTY_MODIFICATION_DATE = 'modification_date';

    /**
     *
     * @var \core\user\User
     */
    private $user;

    /**
     * Get the default properties of all feedback
     * 
     * @return array The property names.
     */
    public static function getDefaultPropertyNames($extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            array(self::PROPERTY_USER_ID, self::PROPERTY_CREATION_DATE, self::PROPERTY_MODIFICATION_DATE));
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
     * @return User
     */
    public function get_user()
    {
        if (! isset($this->user))
        {
            $this->user = DataManager::retrieve_by_id(
                User::class,
                $this->get_user_id());
        }
        
        return $this->user;
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
     * Returns creation_date
     * 
     * @return integer the creation_date
     */
    public function get_creation_date()
    {
        return $this->get_default_property(self::PROPERTY_CREATION_DATE);
    }

    /**
     * Sets the creation_date of this feedback.
     * 
     * @param $creation_date integer the creation_date.
     */
    public function set_creation_date($creation_date)
    {
        $this->set_default_property(self::PROPERTY_CREATION_DATE, $creation_date);
    }

    /**
     * Returns modification_date
     * 
     * @return integer the modification_date
     */
    public function get_modification_date()
    {
        return $this->get_default_property(self::PROPERTY_MODIFICATION_DATE);
    }

    /**
     * Sets the modification_date of this feedback.
     * 
     * @param $modification_date integer the modification_date.
     */
    public function set_modification_date($modification_date)
    {
        $this->set_default_property(self::PROPERTY_MODIFICATION_DATE, $modification_date);
    }
}