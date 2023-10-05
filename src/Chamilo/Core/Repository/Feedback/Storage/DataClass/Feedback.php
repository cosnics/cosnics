<?php
namespace Chamilo\Core\Repository\Feedback\Storage\DataClass;

use Chamilo\Core\Repository\Feedback\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

abstract class Feedback extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_COMMENT = 'comment';
    public const PROPERTY_CREATION_DATE = 'creation_date';
    public const PROPERTY_MODIFICATION_DATE = 'modification_date';
    public const PROPERTY_USER_ID = 'user_id';

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

    /**
     * Returns the feedback comment
     *
     * @return string
     */
    public function get_comment()
    {
        return $this->getDefaultProperty(self::PROPERTY_COMMENT);
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

    public function get_user_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
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

    public function set_user_id($user_id)
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $user_id);
    }
}