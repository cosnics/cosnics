<?php
namespace Chamilo\Core\Repository\Quota\Storage\DataClass;

use Chamilo\Core\Repository\Quota\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Translation\Translation;
use Exception;

/**
 *
 * @author Hans De Bisschop
 */
class Request extends DataClass
{

    const DECISION_DENIED = 1;

    const DECISION_GRANTED = 2;

    const DECISION_PENDING = 0;

    const PROPERTY_CREATION_DATE = 'creation_date';

    const PROPERTY_DECISION = 'decision';

    const PROPERTY_DECISION_DATE = 'decision_date';

    const PROPERTY_DECISION_MOTIVATION = 'decision_motivation';

    const PROPERTY_MOTIVATION = 'motivation';

    const PROPERTY_QUOTA = 'quota';

    /**
     * Request properties
     */
    const PROPERTY_USER_ID = 'user_id';

    /**
     * The user of the request
     *
     * @var \core\user\User
     */
    private $user;

    /**
     * @param $decision
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
        }

        $glyph = new FontAwesomeGlyph($glyphName, array(), Translation::get(self::decision_string($decision)), 'fas');

        return $glyph->render();
    }

    /**
     *
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
     * Returns the creation_date of this Request.
     *
     * @return integer The creation_date.
     */
    public function get_creation_date()
    {
        return $this->get_default_property(self::PROPERTY_CREATION_DATE);
    }

    /**
     * Get the data class data manager
     *
     * @return \libraries\storage\data_manager\DataManager
     */
    public function get_data_manager()
    {
        return DataManager::getInstance();
    }

    /**
     * Returns the decision of this Request.
     *
     * @return integer The decision.
     */
    public function get_decision()
    {
        return $this->get_default_property(self::PROPERTY_DECISION);
    }

    /**
     * Returns the decision_date of this Request.
     *
     * @return integer The decision_date.
     */
    public function get_decision_date()
    {
        return $this->get_default_property(self::PROPERTY_DECISION_DATE);
    }

    /**
     *
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
        return $this->get_default_property(self::PROPERTY_DECISION_MOTIVATION);
    }

    /**
     *
     * @return string
     */
    public function get_decision_string()
    {
        return self::decision_string($this->get_decision());
    }

    /**
     *
     * @param $types_only boolean
     *
     * @return multitype:integer string[]
     */
    public static function get_decision_types($types_only = false)
    {
        $types = array();

        $types[self::DECISION_PENDING] = self::decision_string(self::DECISION_PENDING);
        $types[self::DECISION_GRANTED] = self::decision_string(self::DECISION_GRANTED);
        $types[self::DECISION_DENIED] = self::decision_string(self::DECISION_DENIED);

        return ($types_only ? array_keys($types) : $types);
    }

    /**
     * Get the default properties
     *
     * @param $extended_property_names string[]
     *
     * @return string[] The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self::PROPERTY_USER_ID;
        $extended_property_names[] = self::PROPERTY_QUOTA;
        $extended_property_names[] = self::PROPERTY_MOTIVATION;
        $extended_property_names[] = self::PROPERTY_CREATION_DATE;
        $extended_property_names[] = self::PROPERTY_DECISION_DATE;
        $extended_property_names[] = self::PROPERTY_DECISION;
        $extended_property_names[] = self::PROPERTY_DECISION_MOTIVATION;

        return parent::get_default_property_names($extended_property_names);
    }

    /**
     * Returns the motivation of this Request.
     *
     * @return string The motivation.
     */
    public function get_motivation()
    {
        return $this->get_default_property(self::PROPERTY_MOTIVATION);
    }

    /**
     * Returns the quota of this Request.
     *
     * @return integer The quota.
     */
    public function get_quota()
    {
        return $this->get_default_property(self::PROPERTY_QUOTA);
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
            $this->user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                User::class, (int) $this->get_user_id()
            );
        }

        return $this->user;
    }

    /**
     * Returns the user_id of this Request.
     *
     * @return integer The user_id.
     */
    public function get_user_id()
    {
        return $this->get_default_property(self::PROPERTY_USER_ID);
    }

    /**
     * Is the request pending ?
     *
     * @return boolean
     */
    public function is_pending()
    {
        return $this->get_decision() == self::DECISION_PENDING;
    }

    /**
     * Sets the creation_date of this Request.
     *
     * @param $creation_date integer
     */
    public function set_creation_date($creation_date)
    {
        $this->set_default_property(self::PROPERTY_CREATION_DATE, $creation_date);
    }

    /**
     * Sets the decision of this Request.
     *
     * @param $decision integer
     */
    public function set_decision($decision)
    {
        $this->set_default_property(self::PROPERTY_DECISION, $decision);
    }

    /**
     * Sets the decision_date of this Request.
     *
     * @param $decision_date integer
     */
    public function set_decision_date($decision_date)
    {
        $this->set_default_property(self::PROPERTY_DECISION_DATE, $decision_date);
    }

    /**
     * Sets the decision_motivation of this Request.
     *
     * @param $decision_motivation string
     */
    public function set_decision_motivation($decision_motivation)
    {
        $this->set_default_property(self::PROPERTY_DECISION_MOTIVATION, $decision_motivation);
    }

    /**
     * Sets the motivation of this Request.
     *
     * @param $motivation string
     */
    public function set_motivation($motivation)
    {
        $this->set_default_property(self::PROPERTY_MOTIVATION, $motivation);
    }

    /**
     * Sets the quota of this Request.
     *
     * @param $quota integer
     */
    public function set_quota($quota)
    {
        $this->set_default_property(self::PROPERTY_QUOTA, $quota);
    }

    /**
     * Sets the user_id of this Request.
     *
     * @param $user_id integer
     */
    public function set_user_id($user_id)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $user_id);
    }

    /**
     * Was the request denied ?
     *
     * @return boolean
     */
    public function was_denied()
    {
        return $this->get_decision() == self::DECISION_DENIED;
    }

    /**
     * Was the request granted ?
     *
     * @return boolean
     */
    public function was_granted()
    {
        return $this->get_decision() == self::DECISION_GRANTED;
    }

    /**
     * @return string
     */
    public static function get_table_name()
    {
        return 'repository_quota_request';
    }
}
