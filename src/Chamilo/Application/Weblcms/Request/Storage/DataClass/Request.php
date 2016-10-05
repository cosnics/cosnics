<?php
namespace Chamilo\Application\Weblcms\Request\Storage\DataClass;

use Chamilo\Application\Weblcms\Request\Manager;
use Chamilo\Application\Weblcms\Request\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @author Hans De Bisschop
 */
class Request extends DataClass
{

    /**
     * Request properties
     */
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_NAME = 'name';
    const PROPERTY_COURSE_TYPE_ID = 'course_type_id';
    const PROPERTY_SUBJECT = 'subject';
    const PROPERTY_MOTIVATION = 'motivation';
    const PROPERTY_CREATION_DATE = 'creation_date';
    const PROPERTY_DECISION_DATE = 'decision_date';
    const PROPERTY_DECISION = 'decision';
    const PROPERTY_DECISION_MOTIVATION = 'decision_motivation';
    const PROPERTY_CATEGORY_ID = 'category_id';
    const DECISION_PENDING = 0;
    const DECISION_DENIED = 1;
    const DECISION_GRANTED = 2;

    /**
     * The user of the request
     *
     * @var \core\user\User
     */
    private $user;

    /**
     * Get the default properties
     *
     * @param $extended_property_names multitype:string
     * @return multitype:string The property names.
     */
    static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_USER_ID;
        $extended_property_names[] = self :: PROPERTY_NAME;
        $extended_property_names[] = self :: PROPERTY_COURSE_TYPE_ID;
        $extended_property_names[] = self :: PROPERTY_SUBJECT;
        $extended_property_names[] = self :: PROPERTY_MOTIVATION;
        $extended_property_names[] = self :: PROPERTY_CREATION_DATE;
        $extended_property_names[] = self :: PROPERTY_DECISION_DATE;
        $extended_property_names[] = self :: PROPERTY_DECISION;
        $extended_property_names[] = self :: PROPERTY_DECISION_MOTIVATION;
        $extended_property_names[] = self :: PROPERTY_CATEGORY_ID;

        return parent :: get_default_property_names($extended_property_names);
    }

    /**
     * Get the data class data manager
     *
     * @return \libraries\Datamanager
     */
    function get_data_manager()
    {
        return DataManager :: get_instance();
    }

    /**
     * Returns the user_id of this Request.
     *
     * @return integer The user_id.
     */
    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     * Get the user of this request
     *
     * @return \core\user\User
     */
    function get_user()
    {
        if (! isset($this->user))
        {
            $this->user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
                (int) $this->get_user_id());
        }
        return $this->user;
    }

    /**
     * Sets the user_id of this Request.
     *
     * @param $user_id integer
     */
    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    /**
     * Returns the name of this Request.
     *
     * @return string The name.
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Sets the name of this Request.
     *
     * @param $name string
     */
    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    /**
     * Returns the course type identifier of this Request.
     *
     * @return integer The course type identifier.
     */
    function get_course_type_id()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_TYPE_ID);
    }

    /**
     * Sets the course type identifier of this Request.
     *
     * @param $course_type_id integer
     */
    function set_course_type_id($course_type_id)
    {
        $this->set_default_property(self :: PROPERTY_COURSE_TYPE_ID, $course_type_id);
    }

    /**
     * Returns the subject of this Request.
     *
     * @return string The subject.
     */
    function get_subject()
    {
        return $this->get_default_property(self :: PROPERTY_SUBJECT);
    }

    /**
     * Sets the subject of this Request.
     *
     * @param $subject string
     */
    function set_subject($subject)
    {
        $this->set_default_property(self :: PROPERTY_SUBJECT, $subject);
    }

    /**
     * Returns the motivation of this Request.
     *
     * @return string The motivation.
     */
    function get_motivation()
    {
        return $this->get_default_property(self :: PROPERTY_MOTIVATION);
    }

    /**
     * Sets the motivation of this Request.
     *
     * @param $motivation string
     */
    function set_motivation($motivation)
    {
        $this->set_default_property(self :: PROPERTY_MOTIVATION, $motivation);
    }

    /**
     * Returns the creation_date of this Request.
     *
     * @return integer The creation_date.
     */
    function get_creation_date()
    {
        return $this->get_default_property(self :: PROPERTY_CREATION_DATE);
    }

    /**
     * Sets the creation_date of this Request.
     *
     * @param $creation_date integer
     */
    function set_creation_date($creation_date)
    {
        $this->set_default_property(self :: PROPERTY_CREATION_DATE, $creation_date);
    }

    /**
     * Returns the decision_date of this Request.
     *
     * @return integer The decision_date.
     */
    function get_decision_date()
    {
        return $this->get_default_property(self :: PROPERTY_DECISION_DATE);
    }

    /**
     * Sets the decision_date of this Request.
     *
     * @param $decision_date integer
     */
    function set_decision_date($decision_date)
    {
        $this->set_default_property(self :: PROPERTY_DECISION_DATE, $decision_date);
    }

    /**
     * Returns the decision of this Request.
     *
     * @return integer The decision.
     */
    function get_decision()
    {
        return $this->get_default_property(self :: PROPERTY_DECISION);
    }

    /**
     * Sets the decision of this Request.
     *
     * @param $decision integer
     */
    function set_decision($decision)
    {
        $this->set_default_property(self :: PROPERTY_DECISION, $decision);
    }

    /**
     * Returns the decision_motivation of this Request.
     *
     * @return string The decision_motivation.
     */
    function get_decision_motivation()
    {
        return $this->get_default_property(self :: PROPERTY_DECISION_MOTIVATION);
    }

    /**
     * Sets the decision_motivation of this Request.
     *
     * @param $decision_motivation string
     */
    function set_decision_motivation($decision_motivation)
    {
        $this->set_default_property(self :: PROPERTY_DECISION_MOTIVATION, $decision_motivation);
    }

    /**
     *
     * @return string
     */
    function get_decision_string()
    {
        return self :: decision_string($this->get_decision());
    }

    /**
     *
     * @return string
     */
    static function decision_string($decision)
    {
        switch ($decision)
        {
            case self :: DECISION_PENDING :
                return 'DecisionPending';
                break;
            case self :: DECISION_GRANTED :
                return 'DecisionGranted';
                break;
            case self :: DECISION_DENIED :
                return 'DecisionDenied';
                break;
        }
    }

    /**
     *
     * @return string
     */
    function get_decision_icon()
    {
        return self :: decision_icon($this->get_decision());
    }

    /**
     *
     * @return string
     */
    static function decision_icon($decision)
    {
        return Theme :: getInstance()->getImage(
            'Decision/16/' . $decision,
            'png',
            Translation :: get(self :: decision_string($decision)),
            null,
            ToolbarItem :: DISPLAY_ICON, false, Manager::context());
    }

    /**
     *
     * @param $types_only boolean
     * @return multitype:integer multitype:string
     */
    static function get_decision_types($types_only = false)
    {
        $types = array();

        $types[self :: DECISION_PENDING] = self :: decision_string(self :: DECISION_PENDING);
        $types[self :: DECISION_GRANTED] = self :: decision_string(self :: DECISION_GRANTED);
        $types[self :: DECISION_DENIED] = self :: decision_string(self :: DECISION_DENIED);

        return ($types_only ? array_keys($types) : $types);
    }

    /**
     * Was the request granted ?
     *
     * @return boolean
     */
    function was_granted()
    {
        return $this->get_decision() == self :: DECISION_GRANTED;
    }

    /**
     * Was the request denied ?
     *
     * @return boolean
     */
    function was_denied()
    {
        return $this->get_decision() == self :: DECISION_DENIED;
    }

    /**
     * Is the request pending ?
     *
     * @return boolean
     */
    function is_pending()
    {
        return $this->get_decision() == self :: DECISION_PENDING;
    }

    public function get_category_id()
    {
        return $this->get_default_property(self :: PROPERTY_CATEGORY_ID);
    }

    public function set_category_id($category_id)
    {
        $this->set_default_property(self :: PROPERTY_CATEGORY_ID, $category_id);
    }
}
