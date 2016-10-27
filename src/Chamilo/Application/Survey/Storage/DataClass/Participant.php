<?php
namespace Chamilo\Application\Survey\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

class Participant extends DataClass
{
    const TABLE_NAME = 'participant';
    const CREATE_PARTICIPANT_EVENT = 'create_survey_participant';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_SURVEY_PUBLICATION_ID = 'survey_publication_id';
    const PROPERTY_DATE = 'date';
    const PROPERTY_PROGRESS = 'progress';
    const PROPERTY_STATUS = 'status';
    const PROPERTY_START_TIME = 'start_time';
    const PROPERTY_TOTAL_TIME = 'total_time';
    const PROPERTY_CONTEXT_ID = 'context_id';
    const PROPERTY_PARENT_ID = 'parent_id';
    const PROPERTY_CONTEXT_TEMPLATE_ID = 'context_template_id';
    const STATUS_STARTED = 'started';
    const STATUS_NOTSTARTED = 'notstarted';
    const STATUS_FINISHED = 'finished';

    static function get_default_property_names()
    {
        return parent :: get_default_property_names(
            array(
                self :: PROPERTY_USER_ID,
                self :: PROPERTY_SURVEY_PUBLICATION_ID,
                self :: PROPERTY_DATE,
                self :: PROPERTY_PROGRESS,
                self :: PROPERTY_STATUS,
                self :: PROPERTY_PARENT_ID,
                self :: PROPERTY_START_TIME,
                self :: PROPERTY_TOTAL_TIME,
                self :: PROPERTY_CONTEXT_ID,
                self :: PROPERTY_CONTEXT_TEMPLATE_ID));
    }

    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    function get_survey_publication_id()
    {
        return $this->get_default_property(self :: PROPERTY_SURVEY_PUBLICATION_ID);
    }

    function set_survey_publication_id($survey__publication_id)
    {
        $this->set_default_property(self :: PROPERTY_SURVEY_PUBLICATION_ID, $survey__publication_id);
    }

    function get_date()
    {
        return $this->get_default_property(self :: PROPERTY_DATE);
    }

    function set_date($date)
    {
        $this->set_default_property(self :: PROPERTY_DATE, $date);
    }

    function get_progress()
    {
        return $this->get_default_property(self :: PROPERTY_PROGRESS);
    }

    function set_progress($progress)
    {
        $this->set_default_property(self :: PROPERTY_PROGRESS, $progress);
    }

    function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_STATUS);
    }

    function set_context_id($context_id)
    {
        $this->set_default_property(self :: PROPERTY_CONTEXT_ID, $context_id);
    }

    function get_context_id()
    {
        return $this->get_default_property(self :: PROPERTY_CONTEXT_ID);
    }

    function set_context_template_id($context_template_id)
    {
        $this->set_default_property(self :: PROPERTY_CONTEXT_TEMPLATE_ID, $context_template_id);
    }

    function get_context_template_id()
    {
        return $this->get_default_property(self :: PROPERTY_CONTEXT_TEMPLATE_ID);
    }

    function set_parent_id($parent_id)
    {
        $this->set_default_property(self :: PROPERTY_PARENT_ID, $parent_id);
    }

    function get_parent_id()
    {
        return $this->get_default_property(self :: PROPERTY_PARENT_ID);
    }

    function set_status($status)
    {
        $this->set_default_property(self :: PROPERTY_STATUS, $status);
    }

    function get_start_time()
    {
        return $this->get_default_property(self :: PROPERTY_START_TIME);
    }

    function set_start_time($start_time)
    {
        $this->set_default_property(self :: PROPERTY_START_TIME, $start_time);
    }

    function get_total_time()
    {
        return $this->get_default_property(self :: PROPERTY_TOTAL_TIME);
    }

    function set_total_time($total_time)
    {
        $this->set_default_property(self :: PROPERTY_TOTAL_TIME, $total_time);
    }
}
?>