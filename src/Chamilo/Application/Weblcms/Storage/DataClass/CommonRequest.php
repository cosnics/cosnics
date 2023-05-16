<?php
namespace Chamilo\Application\Weblcms\Storage\DataClass;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package application.lib.weblcms.course
 */
abstract class CommonRequest extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const ALLOWED_DECISION = 2;
    public const DENIED_DECISION = 1;
    public const NO_DECISION = 0;

    public const PROPERTY_CREATION_DATE = 'creation_date';
    public const PROPERTY_DECISION = 'decision';
    public const PROPERTY_DECISION_DATE = 'decision_date';
    public const PROPERTY_MOTIVATION = 'motivation';
    public const PROPERTY_SUBJECT = 'subject';
    public const PROPERTY_USER_ID = 'user_id';

    public const SUBSCRIPTION_REQUEST = 'subscription_request';

    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            array_merge(
                $extendedPropertyNames, [
                    self::PROPERTY_USER_ID,
                    self::PROPERTY_SUBJECT,
                    self::PROPERTY_MOTIVATION,
                    self::PROPERTY_CREATION_DATE,
                    self::PROPERTY_DECISION_DATE,
                    self::PROPERTY_DECISION
                ]
            )
        );
    }

    public function get_creation_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_CREATION_DATE);
    }

    public function get_decision()
    {
        return $this->getDefaultProperty(self::PROPERTY_DECISION);
    }

    public function get_decision_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_DECISION_DATE);
    }

    public function get_motivation()
    {
        return $this->getDefaultProperty(self::PROPERTY_MOTIVATION);
    }

    public function get_subject()
    {
        return $this->getDefaultProperty(self::PROPERTY_SUBJECT);
    }

    public function get_user_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    public function set_creation_date($creation_date)
    {
        $this->setDefaultProperty(self::PROPERTY_CREATION_DATE, $creation_date);
    }

    public function set_decision($decision)
    {
        $this->setDefaultProperty(self::PROPERTY_DECISION, $decision);
    }

    public function set_decision_date($decision_date)
    {
        $this->setDefaultProperty(self::PROPERTY_DECISION_DATE, $decision_date);
    }

    public function set_motivation($motivation)
    {
        $this->setDefaultProperty(self::PROPERTY_MOTIVATION, $motivation);
    }

    public function set_subject($subject)
    {
        $this->setDefaultProperty(self::PROPERTY_SUBJECT, $subject);
    }

    public function set_user_id($user_id)
    {
        return $this->setDefaultProperty(self::PROPERTY_USER_ID, $user_id);
    }
}
