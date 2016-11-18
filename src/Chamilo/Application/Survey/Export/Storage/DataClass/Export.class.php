<?php
namespace Chamilo\Application\Survey\Export\Storage\DataClass;

use Chamilo\libraries\platform\Translation;
use Chamilo\Libraries\Storage\DataClass\DataClass;

class Export extends DataClass
{
    const TABLE_NAME = 'export';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_PUBLICATION_ID = 'publication_id';
    const PROPERTY_CREATED = 'created';
    const PROPERTY_FINISHED = 'finished';
    const PROPERTY_STATUS = 'status';
    const PROPERTY_EXPORT_REGISTRATION_ID = 'export_registration_id';
    const PROPERTY_EXPORT_JOB_ID = 'export_job_id';
    const PROPERTY_TEMPLATE_NAME = 'template_name';
    const PROPERTY_TEMPLATE_DESCRIPTION = 'template_description';
    const STATUS_EXPORT_CREATED = 1;
    const STATUS_EXPORT_CREATED_NAME = 'ExportCreated';
    const STATUS_EXPORT_NOT_CREATED = 2;
    const STATUS_EXPORT_NOT_CREATED_NAME = 'ExportNotCreated';
    const STATUS_EXPORT_IN_QUEUE = 3;
    const STATUS_EXPORT_IN_QUEUE_NAME = 'ExportInQueue';

    /**
     * Inherited
     */
    static function get_default_property_names()
    {
        return array(
            self::PROPERTY_USER_ID, 
            self::PROPERTY_PUBLICATION_ID, 
            self::PROPERTY_CREATED, 
            self::PROPERTY_STATUS, 
            self::PROPERTY_FINISHED, 
            self::PROPERTY_EXPORT_REGISTRATION_ID, 
            self::PROPERTY_TEMPLATE_NAME, 
            self::PROPERTY_TEMPLATE_DESCRIPTION, 
            self::PROPERTY_EXPORT_JOB_ID);
    }

    function get_user_id()
    {
        return $this->get_default_property(self::PROPERTY_USER_ID);
    }

    function set_user_id($user_id)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $user_id);
    }

    function get_survey_publication_id()
    {
        return $this->get_default_property(self::PROPERTY_PUBLICATION_ID);
    }

    function set_survey_publication_id($survey__publication_id)
    {
        $this->set_default_property(self::PROPERTY_PUBLICATION_ID, $survey__publication_id);
    }

    function get_export_registration_id()
    {
        return $this->get_default_property(self::PROPERTY_EXPORT_REGISTRATION_ID);
    }

    function set_export_registration_id($export_registration_id)
    {
        $this->set_default_property(self::PROPERTY_EXPORT_REGISTRATION_ID, $export_registration_id);
    }

    function get_export_job_id()
    {
        return $this->get_default_property(self::PROPERTY_EXPORT_JOB_ID);
    }

    function set_export_job_id($export_job_id)
    {
        $this->set_default_property(self::PROPERTY_EXPORT_JOB_ID, $export_job_id);
    }

    function get_created()
    {
        return $this->get_default_property(self::PROPERTY_CREATED);
    }

    function set_created($date)
    {
        $this->set_default_property(self::PROPERTY_CREATED, $date);
    }

    function get_finished()
    {
        return $this->get_default_property(self::PROPERTY_FINISHED);
    }

    function set_finished($date)
    {
        $this->set_default_property(self::PROPERTY_FINISHED, $date);
    }

    function get_status()
    {
        return $this->get_default_property(self::PROPERTY_STATUS);
    }

    function get_status_name()
    {
        $names = $this->get_status_names();
        return Translation::get($names[$this->get_status()]);
    }

    function get_status_names()
    {
        return array(
            self::STATUS_EXPORT_CREATED => self::STATUS_EXPORT_CREATED_NAME, 
            self::STATUS_EXPORT_IN_QUEUE => self::STATUS_EXPORT_IN_QUEUE_NAME, 
            self::STATUS_EXPORT_NOT_CREATED => self::STATUS_EXPORT_NOT_CREATED_NAME);
    }

    function set_status($status)
    {
        $this->set_default_property(self::PROPERTY_STATUS, $status);
    }

    function get_template_name()
    {
        return $this->get_default_property(self::PROPERTY_TEMPLATE_NAME);
    }

    function set_template_name($template_name)
    {
        $this->set_default_property(self::PROPERTY_TEMPLATE_NAME, $template_name);
    }

    function get_template_description()
    {
        return $this->get_default_property(self::PROPERTY_TEMPLATE_DESCRIPTION);
    }

    function set_template_description($template_description)
    {
        $this->set_default_property(self::PROPERTY_TEMPLATE_DESCRIPTION, $template_description);
    }
}
?>