<?php
namespace Chamilo\Application\Weblcms\Request\Storage\DataClass;

use Chamilo\Application\Weblcms\Request\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Translation\Translation;
use Exception;

/**
 * @package Chamilo\Application\Weblcms\Request\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Request extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const DECISION_DENIED = 1;
    public const DECISION_GRANTED = 2;
    public const DECISION_PENDING = 0;

    public const PROPERTY_CATEGORY_ID = 'category_id';
    public const PROPERTY_COURSE_TYPE_ID = 'course_type_id';
    public const PROPERTY_CREATION_DATE = 'creation_date';
    public const PROPERTY_DECISION = 'decision';
    public const PROPERTY_DECISION_DATE = 'decision_date';
    public const PROPERTY_DECISION_MOTIVATION = 'decision_motivation';
    public const PROPERTY_MOTIVATION = 'motivation';
    public const PROPERTY_NAME = 'name';
    public const PROPERTY_SUBJECT = 'subject';

    /**
     * Request properties
     */
    public const PROPERTY_USER_ID = 'user_id';

    /**
     * The user of the request
     *
     * @var \core\user\User
     */
    private $user;

    /**
     * @param int $decision
     *
     * @return string
     * @throws \Exception
     */
    public static function decision_icon($decision)
    {
        switch ($decision)
        {
            case self::DECISION_PENDING:
                $glyphName = 'hourglass-half';
                break;
            case self::DECISION_GRANTED:
                $glyphName = 'check-square';
                break;
            case self::DECISION_DENIED:
                $glyphName = 'times-circle';
                break;
            default:
                throw new Exception();
                break;
        }

        $glyph = new FontAwesomeGlyph($glyphName, [], Translation::get(self::decision_string($decision)), 'fas');

        return $glyph->render();
    }

    /**
     * @return string
     */
    public static function decision_string($decision)
    {
        switch ($decision)
        {
            case self::DECISION_PENDING :
                return 'DecisionPending';
                break;
            case self::DECISION_GRANTED :
                return 'DecisionGranted';
                break;
            case self::DECISION_DENIED :
                return 'DecisionDenied';
                break;
        }
    }

    /**
     * Get the default properties
     *
     * @param $extendedPropertyNames string[]
     *
     * @return string[] The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_USER_ID;
        $extendedPropertyNames[] = self::PROPERTY_NAME;
        $extendedPropertyNames[] = self::PROPERTY_COURSE_TYPE_ID;
        $extendedPropertyNames[] = self::PROPERTY_SUBJECT;
        $extendedPropertyNames[] = self::PROPERTY_MOTIVATION;
        $extendedPropertyNames[] = self::PROPERTY_CREATION_DATE;
        $extendedPropertyNames[] = self::PROPERTY_DECISION_DATE;
        $extendedPropertyNames[] = self::PROPERTY_DECISION;
        $extendedPropertyNames[] = self::PROPERTY_DECISION_MOTIVATION;
        $extendedPropertyNames[] = self::PROPERTY_CATEGORY_ID;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'weblcms_request_request';
    }

    public function get_category_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_CATEGORY_ID);
    }

    /**
     * Returns the course type identifier of this Request.
     *
     * @return int The course type identifier.
     */
    public function get_course_type_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_COURSE_TYPE_ID);
    }

    /**
     * Returns the creation_date of this Request.
     *
     * @return int The creation_date.
     */
    public function get_creation_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_CREATION_DATE);
    }

    /**
     * Returns the decision of this Request.
     *
     * @return int The decision.
     */
    public function get_decision()
    {
        return $this->getDefaultProperty(self::PROPERTY_DECISION);
    }

    /**
     * Returns the decision_date of this Request.
     *
     * @return int The decision_date.
     */
    public function get_decision_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_DECISION_DATE);
    }

    /**
     * @return string
     */
    public function get_decision_icon()
    {
        return self::decision_icon($this->get_decision());
    }

    /**
     * Returns the decision_motivation of this Request.
     *
     * @return string The decision_motivation.
     */
    public function get_decision_motivation()
    {
        return $this->getDefaultProperty(self::PROPERTY_DECISION_MOTIVATION);
    }

    /**
     * @return string
     */
    public function get_decision_string()
    {
        return self::decision_string($this->get_decision());
    }

    /**
     * @param $types_only bool
     *
     * @return int string[]
     */
    public static function get_decision_types($types_only = false)
    {
        $types = [];

        $types[self::DECISION_PENDING] = self::decision_string(self::DECISION_PENDING);
        $types[self::DECISION_GRANTED] = self::decision_string(self::DECISION_GRANTED);
        $types[self::DECISION_DENIED] = self::decision_string(self::DECISION_DENIED);

        return ($types_only ? array_keys($types) : $types);
    }

    /**
     * Returns the motivation of this Request.
     *
     * @return string The motivation.
     */
    public function get_motivation()
    {
        return $this->getDefaultProperty(self::PROPERTY_MOTIVATION);
    }

    /**
     * Returns the name of this Request.
     *
     * @return string The name.
     */
    public function get_name()
    {
        return $this->getDefaultProperty(self::PROPERTY_NAME);
    }

    /**
     * Returns the subject of this Request.
     *
     * @return string The subject.
     */
    public function get_subject()
    {
        return $this->getDefaultProperty(self::PROPERTY_SUBJECT);
    }

    /**
     * Get the user of this request
     *
     * @return \core\user\User
     */
    public function get_user()
    {
        if (!isset($this->user))
        {
            $this->user = DataManager::retrieve_by_id(
                User::class, (int) $this->get_user_id()
            );
        }

        return $this->user;
    }

    /**
     * Returns the user_id of this Request.
     *
     * @return int The user_id.
     */
    public function get_user_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    /**
     * Is the request pending ?
     *
     * @return bool
     */
    public function is_pending()
    {
        return $this->get_decision() == self::DECISION_PENDING;
    }

    public function set_category_id($category_id)
    {
        $this->setDefaultProperty(self::PROPERTY_CATEGORY_ID, $category_id);
    }

    /**
     * Sets the course type identifier of this Request.
     *
     * @param $course_type_id int
     */
    public function set_course_type_id($course_type_id)
    {
        $this->setDefaultProperty(self::PROPERTY_COURSE_TYPE_ID, $course_type_id);
    }

    /**
     * Sets the creation_date of this Request.
     *
     * @param $creation_date int
     */
    public function set_creation_date($creation_date)
    {
        $this->setDefaultProperty(self::PROPERTY_CREATION_DATE, $creation_date);
    }

    /**
     * Sets the decision of this Request.
     *
     * @param $decision int
     */
    public function set_decision($decision)
    {
        $this->setDefaultProperty(self::PROPERTY_DECISION, $decision);
    }

    /**
     * Sets the decision_date of this Request.
     *
     * @param $decision_date int
     */
    public function set_decision_date($decision_date)
    {
        $this->setDefaultProperty(self::PROPERTY_DECISION_DATE, $decision_date);
    }

    /**
     * Sets the decision_motivation of this Request.
     *
     * @param $decision_motivation string
     */
    public function set_decision_motivation($decision_motivation)
    {
        $this->setDefaultProperty(self::PROPERTY_DECISION_MOTIVATION, $decision_motivation);
    }

    /**
     * Sets the motivation of this Request.
     *
     * @param $motivation string
     */
    public function set_motivation($motivation)
    {
        $this->setDefaultProperty(self::PROPERTY_MOTIVATION, $motivation);
    }

    /**
     * Sets the name of this Request.
     *
     * @param $name string
     */
    public function set_name($name)
    {
        $this->setDefaultProperty(self::PROPERTY_NAME, $name);
    }

    /**
     * Sets the subject of this Request.
     *
     * @param $subject string
     */
    public function set_subject($subject)
    {
        $this->setDefaultProperty(self::PROPERTY_SUBJECT, $subject);
    }

    /**
     * Sets the user_id of this Request.
     *
     * @param $user_id int
     */
    public function set_user_id($user_id)
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $user_id);
    }

    /**
     * Was the request denied ?
     *
     * @return bool
     */
    public function was_denied()
    {
        return $this->get_decision() == self::DECISION_DENIED;
    }

    /**
     * Was the request granted ?
     *
     * @return bool
     */
    public function was_granted()
    {
        return $this->get_decision() == self::DECISION_GRANTED;
    }
}
