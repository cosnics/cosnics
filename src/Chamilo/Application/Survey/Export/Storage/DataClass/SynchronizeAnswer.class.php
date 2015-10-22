<?php
namespace Chamilo\Application\Survey\Export\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

class SynchronizeAnswer extends DataClass
{
    const TABLE_NAME = 'synchronize_answer';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_SURVEY_PUBLICATION_ID = 'survey_publication_id';
    const PROPERTY_CREATED = 'created';
    const PROPERTY_STATUS = 'status';
    const STATUS_SYNCHRONIZED = 1;
    const STATUS_NOT_SYNCHRONIZED = 2;
    const STATUS_SYNCHRONISATION_IN_QUEUE = 3;
    const STATUS_SYNCHRONISATION_NOT_IN_QUEUE = 4;

    /**
     * Inherited
     */
    static function get_default_property_names()
    {
        return array(
            self :: PROPERTY_USER_ID,
            self :: PROPERTY_SURVEY_PUBLICATION_ID,
            self :: PROPERTY_CREATED,
            self :: PROPERTY_STATUS);
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

    function get_created()
    {
        return $this->get_default_property(self :: PROPERTY_CREATED);
    }

    function set_created($date)
    {
        $this->set_default_property(self :: PROPERTY_CREATED, $date);
    }

    function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_STATUS);
    }

    function set_status($status)
    {
        $this->set_default_property(self :: PROPERTY_STATUS, $status);
    }
}
?>