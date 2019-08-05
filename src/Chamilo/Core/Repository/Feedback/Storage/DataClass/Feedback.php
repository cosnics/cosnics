<?php
namespace Chamilo\Core\Repository\Feedback\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

class Feedback extends DataClass
{
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_COMMENT = 'comment';
    const PROPERTY_CREATION_DATE = 'creation_date';
    const PROPERTY_MODIFICATION_DATE = 'modification_date';
    const PROPERTY_FEEDBACK_CONTENT_OBJECT_ID = 'feedback_content_object_id';

    private $user;

    /**
     * Get the default properties of all feedback
     * 
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self::PROPERTY_USER_ID;
        $extended_property_names[] = self::PROPERTY_COMMENT;
        $extended_property_names[] = self::PROPERTY_CREATION_DATE;
        $extended_property_names[] = self::PROPERTY_MODIFICATION_DATE;
        $extended_property_names[] = self::PROPERTY_FEEDBACK_CONTENT_OBJECT_ID;

        return parent::get_default_property_names($extended_property_names);
    }

    public function get_user_id()
    {
        return $this->get_default_property(self::PROPERTY_USER_ID);
    }

    public function get_user()
    {
        if (! isset($this->user))
        {
            $this->user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                \Chamilo\Core\User\Storage\DataClass\User::class_name(), 
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
        return $this->get_default_property(self::PROPERTY_COMMENT);
    }

    public function set_user_id($user_id)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $user_id);
    }

    /**
     * Sets the comment
     * 
     * @param string $comment
     */
    public function set_comment($comment)
    {
        $this->set_default_property(self::PROPERTY_COMMENT, $comment);
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

    /**
     * @return int
     */
    public function getFeedbackContentObjectId()
    {
        return $this->get_default_property(self::PROPERTY_FEEDBACK_CONTENT_OBJECT_ID);
    }

    /**
     * @param int $feedbackContentObjectId
     *
     * @return $this
     */
    public function setFeedbackContentObjectId(int $feedbackContentObjectId)
    {
        $this->set_default_property(self::PROPERTY_FEEDBACK_CONTENT_OBJECT_ID, $feedbackContentObjectId);
        return $this;
    }
}