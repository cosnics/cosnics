<?php
namespace Chamilo\Application\Survey\Mail\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

class UserMail extends DataClass
{
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_PUBLICATION_ID = 'publication_id';
    const PROPERTY_SEND_DATE = 'send_date';
    const PROPERTY_STATUS = 'status';
    const PROPERTY_MAIL_ID = 'mail_id';
    const STATUS_MAIL_SEND = 1;
    const STATUS_MAIL_NOT_SEND = 2;
    const STATUS_MAIL_IN_QUEUE = 3;

    /**
     * Inherited
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(
            array(
                self :: PROPERTY_USER_ID,
                self :: PROPERTY_PUBLICATION_ID,
                self :: PROPERTY_SEND_DATE,
                self :: PROPERTY_STATUS,
                self :: PROPERTY_MAIL_ID));
    }

    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    function get_publication_id()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLICATION_ID);
    }

    function set_publication_id($publication_id)
    {
        $this->set_default_property(self :: PROPERTY_PUBLICATION_ID, $publication_id);
    }

    function get_mail_id()
    {
        return $this->get_default_property(self :: PROPERTY_MAIL_ID);
    }

    function set_mail_id($mail_id)
    {
        $this->set_default_property(self :: PROPERTY_MAIL_ID, $mail_id);
    }

    function get_send_date()
    {
        return $this->get_default_property(self :: PROPERTY_SEND_DATE);
    }

    function set_send_date($date)
    {
        $this->set_default_property(self :: PROPERTY_SEND_DATE, $date);
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