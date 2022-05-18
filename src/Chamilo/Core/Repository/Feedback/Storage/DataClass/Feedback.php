<?php
namespace Chamilo\Core\Repository\Feedback\Storage\DataClass;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

abstract class Feedback extends DataClass
{
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_COMMENT = 'comment';
    const PROPERTY_CREATION_DATE = 'creation_date';
    const PROPERTY_MODIFICATION_DATE = 'modification_date';

    private $user;

    /**
     * Get the default properties of all feedback
     * 
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_USER_ID;
        $extendedPropertyNames[] = self::PROPERTY_COMMENT;
        $extendedPropertyNames[] = self::PROPERTY_CREATION_DATE;
        $extendedPropertyNames[] = self::PROPERTY_MODIFICATION_DATE;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    public function get_user_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

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
     * Returns the feedback comment
     * 
     * @return string
     */
    public function get_comment()
    {
        return $this->getDefaultProperty(self::PROPERTY_COMMENT);
    }

    public function set_user_id($user_id)
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $user_id);
    }

    /**
     * Sets the comment
     * 
     * @param string $comment
     */
    public function set_comment($comment)
    {
        $this->setDefaultProperty(self::PROPERTY_COMMENT, $comment);
    }

    /**
     * Returns creation_date
     * 
     * @return integer the creation_date
     */
    public function get_creation_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_CREATION_DATE);
    }

    /**
     * Sets the creation_date of this feedback.
     * 
     * @param $creation_date integer the creation_date.
     */
    public function set_creation_date($creation_date)
    {
        $this->setDefaultProperty(self::PROPERTY_CREATION_DATE, $creation_date);
    }

    /**
     * Returns modification_date
     * 
     * @return integer the modification_date
     */
    public function get_modification_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_MODIFICATION_DATE);
    }

    /**
     * Sets the modification_date of this feedback.
     * 
     * @param $modification_date integer the modification_date.
     */
    public function set_modification_date($modification_date)
    {
        $this->setDefaultProperty(self::PROPERTY_MODIFICATION_DATE, $modification_date);
    }
}