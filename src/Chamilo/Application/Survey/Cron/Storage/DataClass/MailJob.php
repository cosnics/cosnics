<?php
namespace Chamilo\Application\Survey\Cron\Storage\DataClass;

use Chamilo\Application\Survey\Cron\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

class MailJob extends DataClass
{
    const TABLE_NAME = 'mail_job';
    const PROPERTY_USER_MAIL_ID = 'user_mail_id';
    const PROPERTY_UUID = 'UUID';
    const PROPERTY_STATUS = 'status';
    const STATUS_NEW = 1;
    const STATUS_DONE = 2;

    static function get_default_property_names()
    {
        return parent :: get_default_property_names(
            array(self :: PROPERTY_USER_MAIL_ID, self :: PROPERTY_UUID, self :: PROPERTY_STATUS));
    }

    function get_data_manager()
    {
        return DataManager :: getInstance();
    }

    /**
     * Returns the publication_mail_tracker_id of this MailJob.
     *
     * @return the publication_mail_tracker_id.
     */
    function get_user_mail_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_MAIL_ID);
    }

    /**
     * Sets the publication_mail_tracker_id of this MailJob.
     *
     * @param publication_mail_tracker_id
     */
    function set_user_mail_id($user_mail_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_MAIL_ID, $user_mail_id);
    }

    /**
     * Returns the UUID of this MailJob.
     *
     * @return the UUID.
     */
    function get_UUID()
    {
        return $this->get_default_property(self :: PROPERTY_UUID);
    }

    /**
     * Sets the UUID of this MailJob.
     *
     * @param UUID
     */
    function set_UUID($UUID)
    {
        $this->set_default_property(self :: PROPERTY_UUID, $UUID);
    }

    /**
     * Returns the status of this MailJob.
     *
     * @return the status.
     */
    function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_STATUS);
    }

    /**
     * Sets the status of this MailJob.
     *
     * @param status
     */
    function set_status($status)
    {
        $this->set_default_property(self :: PROPERTY_STATUS, $status);
    }
}

?>