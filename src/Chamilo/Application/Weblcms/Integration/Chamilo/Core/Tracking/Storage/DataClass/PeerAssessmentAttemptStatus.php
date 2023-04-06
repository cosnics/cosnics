<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass;

/**
 *
 * @package application.lib.weblcms.trackers
 */
class PeerAssessmentAttemptStatus extends \Chamilo\Core\Tracking\Storage\DataClass\SimpleTracker
{
    const PROPERTY_ATTEMPT_ID = 'attempt_id';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_FACTOR = 'factor';
    const PROPERTY_PROGRESS = 'progress';
    const PROPERTY_CLOSED = 'closed';
    const PROPERTY_CLOSED_BY = 'closed_by';
    const PROPERTY_CREATED = 'created';
    const PROPERTY_MODIFIED = 'modified';

    /**
     * Inherited
     * 
     * @see MainTracker :: track()
     */
    public function validate_parameters(array $parameters = array())
    {
        $this->set_attempt_id($parameters[self::PROPERTY_ATTEMPT_ID]);
        $this->set_user_id($parameters[self::PROPERTY_USER_ID]);
        $this->set_factor($parameters[self::PROPERTY_FACTOR]);
        $this->set_closed($parameters[self::PROPERTY_CLOSED]);
        $this->set_closed_by($parameters[self::PROPERTY_CLOSED_BY]);
        $this->set_progress($parameters[self::PROPERTY_PROGRESS]);
        $this->set_created($parameters[self::PROPERTY_CREATED]);
        $this->set_modified($parameters[self::PROPERTY_MODIFIED]);
    }

    /**
     * Inherited
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_ATTEMPT_ID, 
                self::PROPERTY_USER_ID, 
                self::PROPERTY_FACTOR, 
                self::PROPERTY_PROGRESS, 
                self::PROPERTY_CLOSED, 
                self::PROPERTY_CLOSED_BY, 
                self::PROPERTY_CREATED, 
                self::PROPERTY_MODIFIED));
    }

    public function get_attempt_id()
    {
        return $this->get_default_property(self::PROPERTY_ATTEMPT_ID);
    }

    public function set_attempt_id($attempt_definition_id)
    {
        $this->set_default_property(self::PROPERTY_ATTEMPT_ID, $attempt_definition_id);
    }

    public function get_user_id()
    {
        return $this->get_default_property(self::PROPERTY_USER_ID);
    }

    public function set_user_id($user_id)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $user_id);
    }

    public function get_factor()
    {
        return $this->get_default_property(self::PROPERTY_FACTOR);
    }

    public function set_factor($factor)
    {
        $this->set_default_property(self::PROPERTY_FACTOR, $factor);
    }

    public function get_created()
    {
        return $this->get_default_property(self::PROPERTY_CREATED);
    }

    public function set_created($created)
    {
        $this->set_default_property(self::PROPERTY_CREATED, $created);
    }

    public function get_modified()
    {
        return $this->get_default_property(self::PROPERTY_MODIFIED);
    }

    function set_modified($modified)
    {
        $this->set_default_property(self::PROPERTY_MODIFIED, $modified);
    }

    public function get_closed()
    {
        return $this->get_default_property(self::PROPERTY_CLOSED);
    }

    public function set_closed($closed)
    {
        $this->set_default_property(self::PROPERTY_CLOSED, $closed);
    }

    public function get_closed_by()
    {
        return $this->get_default_property(self::PROPERTY_CLOSED_BY);
    }

    public function set_closed_by($closed_by)
    {
        $this->set_default_property(self::PROPERTY_CLOSED_BY, $closed_by);
    }

    public function get_progress()
    {
        return $this->get_default_property(self::PROPERTY_PROGRESS);
    }

    public function set_progress($progress)
    {
        $this->set_default_property(self::PROPERTY_PROGRESS, $progress);
    }
}