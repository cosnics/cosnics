<?php
namespace Chamilo\Application\Survey\Cron\Storage\DataClass;

use Chamilo\Application\Survey\Cron\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

class ExportJob extends DataClass
{
    const TABLE_NAME = 'export_job';
    const PROPERTY_PUBLICATION_ID = 'publication_id';
    const PROPERTY_EXPORT_TEMPLATE_ID = 'export_template_id';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_UUID = 'UUID';
    const PROPERTY_STATUS = 'status';
    const PROPERTY_EXPORT_TYPE = 'export_type';
    const STATUS_NEW = 1;
    const STATUS_DONE = 2;
    const STATUS_NOT_DONE = 3;
    const EXPORT_TYPE_TEMPLATE_EXPORT = 1;
    const EXPORT_TYPE_SYNCHRONIZE_ANSWERS = 2;

    static function get_default_property_names()
    {
        return parent :: get_default_property_names(
            array(
                self :: PROPERTY_PUBLICATION_ID,
                self :: PROPERTY_EXPORT_TEMPLATE_ID,
                self :: PROPERTY_USER_ID,
                self :: PROPERTY_UUID,
                self :: PROPERTY_STATUS,
                self :: PROPERTY_EXPORT_TYPE));
    }

    function get_data_manager()
    {
        return DataManager :: get_instance();
    }

    /**
     * Returns the publication_id of this ExportJob.
     *
     * @return the publication_id.
     */
    function get_publication_id()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLICATION_ID);
    }

    /**
     * Sets the publication_id of this ExportJob.
     *
     * @param publication_id
     */
    function set_publication_id($publication_id)
    {
        $this->set_default_property(self :: PROPERTY_PUBLICATION_ID, $publication_id);
    }

    /**
     * Returns the export_template_id of this ExportJob.
     *
     * @return the export_template_id.
     */
    function get_export_template_id()
    {
        return $this->get_default_property(self :: PROPERTY_EXPORT_TEMPLATE_ID);
    }

    /**
     * Sets the export_template_id of this ExportJob.
     *
     * @param export_template_id
     */
    function set_export_template_id($export_template_id)
    {
        $this->set_default_property(self :: PROPERTY_EXPORT_TEMPLATE_ID, $export_template_id);
    }

    /**
     * Returns the user_id of this ExportJob.
     *
     * @return the user_id.
     */
    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     * Sets the user_id of this ExportJob.
     *
     * @param user_id
     */
    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    /**
     * Returns the UUID of this ExportJob.
     *
     * @return the UUID.
     */
    function get_UUID()
    {
        return $this->get_default_property(self :: PROPERTY_UUID);
    }

    /**
     * Sets the UUID of this ExportJob.
     *
     * @param UUID
     */
    function set_UUID($UUID)
    {
        $this->set_default_property(self :: PROPERTY_UUID, $UUID);
    }

    /**
     * Returns the status of this ExportJob.
     *
     * @return the status.
     */
    function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_STATUS);
    }

    /**
     * Sets the status of this ExportJob.
     *
     * @param status
     */
    function set_status($status)
    {
        $this->set_default_property(self :: PROPERTY_STATUS, $status);
    }

    /**
     * Returns the export_type of this ExportJob.
     *
     * @return the export_type.
     */
    function get_export_type()
    {
        return $this->get_default_property(self :: PROPERTY_EXPORT_TYPE);
    }

    /**
     * Sets the export_type of this ExportJob.
     *
     * @param export_type
     */
    function set_export_type($export_type)
    {
        $this->set_default_property(self :: PROPERTY_EXPORT_TYPE, $export_type);
    }
}

?>