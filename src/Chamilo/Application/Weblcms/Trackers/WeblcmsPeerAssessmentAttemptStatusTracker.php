<?php
namespace Chamilo\Application\Weblcms\Trackers;

use Chamilo\Core\Tracking\Storage\DataClass\SimpleTracker;

/**
 *
 * @package application.lib.weblcms.trackers
 */
class WeblcmsPeerAssessmentAttemptStatusTracker extends SimpleTracker
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
    function validate_parameters(array $parameters = array())
    {
        $this->set_attempt_id($parameters[self :: PROPERTY_ATTEMPT_ID]);
        $this->set_user_id($parameters[self :: PROPERTY_USER_ID]);
        $this->set_factor($parameters[self :: PROPERTY_FACTOR]);
        $this->set_closed($parameters[self :: PROPERTY_CLOSED]);
        $this->set_closed_by($parameters[self :: PROPERTY_CLOSED_BY]);
        $this->set_progress($parameters[self :: PROPERTY_PROGRESS]);
        $this->set_created($parameters[self :: PROPERTY_CREATED]);
        $this->set_modified($parameters[self :: PROPERTY_MODIFIED]);
    }

    /**
     * Inherited
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(
            array(
                self :: PROPERTY_ATTEMPT_ID,
                self :: PROPERTY_USER_ID,
                self :: PROPERTY_FACTOR,
                self :: PROPERTY_PROGRESS,
                self :: PROPERTY_CLOSED,
                self :: PROPERTY_CLOSED_BY,
                self :: PROPERTY_CREATED,
                self :: PROPERTY_MODIFIED));
    }

    /*
     * function get_attempt_definition_id() { return $this->get_default_property(self :: PROPERTY_ATTEMPT_ID); }
     * function set_attempt_definition_id($attempt_definition_id) { $this->set_default_property(self ::
     * PROPERTY_ATTEMPT_ID, $attempt_definition_id); } function get_user_id() { return $this->get_default_property(self
     * :: PROPERTY_USER_ID); } function set_user_id($user_id) { $this->set_default_property(self :: PROPERTY_USER_ID,
     * $user_id); } function get_group_id() { return $this->get_default_property(self :: PROPERTY_GROUP_ID); } function
     * set_group_id($group_id) { $this->set_default_property(self :: PROPERTY_GROUP_ID, $group_id); } function
     * get_factor() { return $this->get_default_property(self :: PROPERTY_FACTOR); } function set_factor($factor) {
     * $this->set_default_property(self :: PROPERTY_FACTOR, $factor); } function get_created() { return
     * $this->get_default_property(self :: PROPERTY_CREATED); } function set_created($created) {
     * $this->set_default_property(self :: PROPERTY_CREATED, $created); } function get_modified() { return
     * $this->get_default_property(self :: PROPERTY_MODIFIED); } function set_modified($modified) {
     * $this->set_default_property(self :: PROPERTY_MODIFIED, $modified); } function get_closed() { return
     * $this->get_default_property(self :: PROPERTY_CLOSED); } function set_closed($closed) {
     * $this->set_default_property(self :: PROPERTY_CLOSED, $closed); }
     */
    function __call($name, array $arguments)
    {
        // generate error if no getter or setter is called and return
        if (! preg_match('/^(get|set)_(.+)$/', $name, $matches))
        {
            trigger_error('method not found', E_USER_ERROR);
            return;
        }
        // determine the method and property to be called
        $method = $matches[1] . '_default_property';
        $prop = constant($this :: class_name() . '::PROPERTY_' . strtoupper($matches[2]));
        // prepend the property to the argument list
        array_unshift($arguments, $prop);
        // call get_default_property or set_default_property with the arguments
        return call_user_func_array(array($this, $method), $arguments);
    }
}