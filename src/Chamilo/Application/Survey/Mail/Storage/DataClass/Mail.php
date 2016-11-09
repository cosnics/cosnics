<?php
namespace Chamilo\Application\Survey\Mail\Storage\DataClass;

use Chamilo\Application\Survey\Mail\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

class Mail extends DataClass
{

    /**
     * Mail properties
     */
    const PROPERTY_MAIL_HEADER = 'mail_header';
    const PROPERTY_MAIL_CONTENT = 'mail_content';
    const PROPERTY_FROM_ADDRESS = 'from_address';
    const PROPERTY_FROM_ADDRESS_NAME = 'from_address_name';
    const PROPERTY_REPLY_ADDRESS = 'reply_address';
    const PROPERTY_REPLY_ADDRESS_NAME = 'reply_address_name';
    const PROPERTY_SENDER_USER_ID = 'sender_user_id';
    const PROPERTY_PUBLICATION_ID = 'publication_id';
    const PROPERTY_TYPE = 'type';
    const PROPERTY_SEND_DATE = "send_date";
    const PARTICIPANT_TYPE = 1;
    const REPORTING_TYPE = 2;
    const EXPORT_TYPE = 3;

    /**
     * Get the default properties
     *
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(
            array(
                self :: PROPERTY_SEND_DATE,
                self :: PROPERTY_TYPE,
                self :: PROPERTY_PUBLICATION_ID,
                self :: PROPERTY_FROM_ADDRESS,
                self :: PROPERTY_FROM_ADDRESS_NAME,
                self :: PROPERTY_REPLY_ADDRESS,
                self :: PROPERTY_REPLY_ADDRESS_NAME,
                self :: PROPERTY_MAIL_CONTENT,
                self :: PROPERTY_MAIL_HEADER,
                self :: PROPERTY_SENDER_USER_ID));
    }

    // public function delete()
    // {
    // $condition = new EqualityCondition(MailTracker :: PROPERTY_SURVEY_PUBLICATION_MAIL_ID, $this->get_id());
    // $trackers = Tracker :: get_data(MailTracker :: class_name(), Manager :: APPLICATION_NAME, $condition);
    // while ($tracker = $trackers->next_result())
    // {
    // $tracker->delete();
    // }
    // $succes = parent :: delete();
    // return $succes;
    // }
    function get_data_manager()
    {
        return DataManager :: getInstance();
    }

    /**
     * Returns the mail_header of this Mail.
     *
     * @return the mail_header.
     */
    function get_mail_header()
    {
        return $this->get_default_property(self :: PROPERTY_MAIL_HEADER);
    }

    /**
     * Sets the mail_header of this Mail.
     *
     * @param mail_header
     */
    function set_mail_header($mail_header)
    {
        $this->set_default_property(self :: PROPERTY_MAIL_HEADER, $mail_header);
    }

    /**
     * Sets the mail_content of this Mail.
     *
     * @param mail_content
     */
    function set_mail_content($mail_content)
    {
        $this->set_default_property(self :: PROPERTY_MAIL_CONTENT, $mail_content);
    }

    /**
     * Returns the mail_content of this Mail.
     *
     * @return the mail_content.
     */
    function get_mail_content()
    {
        return $this->get_default_property(self :: PROPERTY_MAIL_CONTENT);
    }

    /**
     * Returns the from_address of this Mail.
     *
     * @return the from_address.
     */
    function get_from_address()
    {
        return $this->get_default_property(self :: PROPERTY_FROM_ADDRESS);
    }

    /**
     * Sets the from_address of this Mail.
     *
     * @param from_address_name
     */
    function set_from_address($from_address)
    {
        $this->set_default_property(self :: PROPERTY_FROM_ADDRESS, $from_address);
    }

    function get_from_address_name()
    {
        return $this->get_default_property(self :: PROPERTY_FROM_ADDRESS_NAME);
    }

    /**
     * Sets the from_address of this Mail.
     *
     * @param from_address_name
     */
    function set_from_address_name($from_address_name)
    {
        $this->set_default_property(self :: PROPERTY_FROM_ADDRESS_NAME, $from_address_name);
    }

    /**
     * Returns the reply_address of this Mail.
     *
     * @return the reply_address.
     */
    function get_reply_address()
    {
        return $this->get_default_property(self :: PROPERTY_REPLY_ADDRESS);
    }

    /**
     * Sets the reply_address of this Mail.
     *
     * @param reply_address
     */
    function set_reply_address($reply_address)
    {
        $this->set_default_property(self :: PROPERTY_REPLY_ADDRESS, $reply_address);
    }

    /**
     * Returns the reply_address of this Mail.
     *
     * @return the reply_address_name.
     */
    function get_reply_address_name()
    {
        return $this->get_default_property(self :: PROPERTY_REPLY_ADDRESS_NAME);
    }

    /**
     * Sets the reply_address of this Mail.
     *
     * @param reply_address_name
     */
    function set_reply_address_name($reply_address_name)
    {
        $this->set_default_property(self :: PROPERTY_REPLY_ADDRESS_NAME, $reply_address_name);
    }

    /**
     * Returns the sender_user_id of this Mail.
     *
     * @return the sender_user_id.
     */
    function get_sender_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_SENDER_USER_ID);
    }

    /**
     * Sets the sender_user_id of this Mail.
     *
     * @param sender_user_id
     */
    function set_sender_user_id($sender_user_id)
    {
        $this->set_default_property(self :: PROPERTY_SENDER_USER_ID, $sender_user_id);
    }

    /**
     * Returns the publication_id of this Mail.
     *
     * @return the publication_id.
     */
    function get_publication_id()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLICATION_ID);
    }

    /**
     * Sets the publication_id of this Mail.
     *
     * @param publication_id
     */
    function set_publication_id($publication_id)
    {
        $this->set_default_property(self :: PROPERTY_PUBLICATION_ID, $publication_id);
    }

    /**
     * Returns the type of this Mail.
     *
     * @return the type.
     */
    function get_type()
    {
        return $this->get_default_property(self :: PROPERTY_TYPE);
    }

    /**
     * Sets the type of this Mail.
     *
     * @param type
     */
    function set_type($type)
    {
        $this->set_default_property(self :: PROPERTY_TYPE, $type);
    }

    /**
     * Returns the send_date of this Mail.
     *
     * @return the send_date.
     */
    function get_send_date()
    {
        return $this->get_default_property(self :: PROPERTY_SEND_DATE);
    }

    /**
     * Sets the send_date of this Mail.
     *
     * @param send_date
     */
    function set_send_date($send_date)
    {
        $this->set_default_property(self :: PROPERTY_SEND_DATE, $send_date);
    }
}
?>