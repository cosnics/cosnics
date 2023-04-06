<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package application.lib.weblcms.course
 */
class CommonRequest extends DataClass
{
    const ALLOWED_DECISION = 2;
    const DENIED_DECISION = 1;
    const NO_DECISION = 0;
    const SUBSCRIPTION_REQUEST = 'subscription_request';
    CONST CREATE_REQUEST = 'create_request';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_SUBJECT = 'subject';
    const PROPERTY_MOTIVATION = 'motivation';
    const PROPERTY_CREATION_DATE = 'creation_date';
    const PROPERTY_DECISION_DATE = 'decision_date';
    const PROPERTY_DECISION = 'decision';

    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array_merge(
                $extended_property_names,
                array(
                    self::PROPERTY_USER_ID,
                    self::PROPERTY_SUBJECT,
                    self::PROPERTY_MOTIVATION,
                    self::PROPERTY_CREATION_DATE,
                    self::PROPERTY_DECISION_DATE,
                    self::PROPERTY_DECISION)));
    }

    public function get_user_id()
    {
        return $this->get_default_property(self::PROPERTY_USER_ID);
    }

    public function get_subject()
    {
        return $this->get_default_property(self::PROPERTY_SUBJECT);
    }

    public function get_motivation()
    {
        return $this->get_default_property(self::PROPERTY_MOTIVATION);
    }

    public function get_creation_date()
    {
        return $this->get_default_property(self::PROPERTY_CREATION_DATE);
    }

    public function get_decision_date()
    {
        return $this->get_default_property(self::PROPERTY_DECISION_DATE);
    }

    public function get_decision()
    {
        return $this->get_default_property(self::PROPERTY_DECISION);
    }

    public function set_user_id($user_id)
    {
        return $this->set_default_property(self::PROPERTY_USER_ID, $user_id);
    }

    public function set_subject($subject)
    {
        $this->set_default_property(self::PROPERTY_SUBJECT, $subject);
    }

    public function set_motivation($motivation)
    {
        $this->set_default_property(self::PROPERTY_MOTIVATION, $motivation);
    }

    public function set_creation_date($creation_date)
    {
        $this->set_default_property(self::PROPERTY_CREATION_DATE, $creation_date);
    }

    public function set_decision_date($decision_date)
    {
        $this->set_default_property(self::PROPERTY_DECISION_DATE, $decision_date);
    }

    public function set_decision($decision)
    {
        $this->set_default_property(self::PROPERTY_DECISION, $decision);
    }
}
