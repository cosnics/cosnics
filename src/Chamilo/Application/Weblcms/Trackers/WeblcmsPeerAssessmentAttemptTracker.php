<?php
namespace Chamilo\Application\Weblcms\Trackers;

use Chamilo\Core\Tracking\Storage\DataClass\SimpleTracker;

/**
 *
 * @package application.lib.weblcms.trackers
 */
class WeblcmsPeerAssessmentAttemptTracker extends SimpleTracker
{
    const PROPERTY_DESCRIPTION = 'description';

    const PROPERTY_END_DATE = 'end_date';

    const PROPERTY_HIDDEN = 'hidden';

    const PROPERTY_PUBLICATION_ID = 'publication_id';

    const PROPERTY_START_DATE = 'start_date';

    const PROPERTY_TITLE = 'title';

    const PROPERTY_WEIGHT = 'weight';

    function __call($name, array $arguments)
    {
        // generate error if no getter or setter is called and return
        if (!preg_match('/^(get|set)_(.+)$/', $name, $matches))
        {
            trigger_error('method not found', E_USER_ERROR);

            return;
        }
        // determine the method and property to be called
        $method = $matches[1] . '_default_property';
        $prop = constant(static::class . '::PROPERTY_' . strtoupper($matches[2]));
        // prepend the property to the argument list
        array_unshift($arguments, $prop);

        // call get_default_property or set_default_property with the arguments
        return call_user_func_array(array($this, $method), $arguments);
    }

    /**
     * Inherited
     */
    static function get_default_property_names()
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_PUBLICATION_ID,
                self::PROPERTY_TITLE,
                self::PROPERTY_DESCRIPTION,
                self::PROPERTY_START_DATE,
                self::PROPERTY_END_DATE,
                self::PROPERTY_HIDDEN,
                self::PROPERTY_WEIGHT
            )
        );
    }

    /*
     * function get_publication_id() { return $this->get_default_property(self :: PROPERTY_PUBLICATION_ID); } function
     * set_publication_id($publication_id) { $this->set_default_property(self :: PROPERTY_PUBLICATION_ID,
     * $publication_id); } function get_start_date() { return $this->get_default_property(self :: PROPERTY_START_DATE);
     * } function set_start_date($start_date) { $this->set_default_property(self :: PROPERTY_START_DATE, $start_date); }
     * function get_end_date() { return $this->get_default_property(self :: PROPERTY_END_DATE); } function
     * set_end_date($end_date) { $this->set_default_property(self :: PROPERTY_END_DATE, $end_date); } function
     * get_closed() { return $this->get_default_property(self :: PROPERTY_CLOSED); } function set_closed($closed) {
     * $this->set_default_property(self :: PROPERTY_CLOSED, $closed); }
     */

    /**
     * Inherited
     *
     * @see MainTracker :: track()
     */
    function validate_parameters(array $parameters = array())
    {
        $this->set_publication_id($parameters[self::PROPERTY_PUBLICATION_ID]);
        $this->set_title($parameters[self::PROPERTY_TITLE]);
        $this->set_description($parameters[self::PROPERTY_DESCRIPTION]);
        $this->set_start_date(strtotime($parameters[self::PROPERTY_START_DATE]));
        $this->set_end_date(strtotime($parameters[self::PROPERTY_END_DATE]));
        $this->set_hidden($parameters[self::PROPERTY_HIDDEN]);
        $this->set_weight($parameters[self::PROPERTY_WEIGHT]);
    }
}