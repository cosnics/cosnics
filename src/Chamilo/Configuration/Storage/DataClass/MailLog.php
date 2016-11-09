<?php
namespace Chamilo\Configuration\Storage\DataClass;

use Chamilo\Configuration\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

class MailLog extends DataClass
{
    const PROPERTY_SENDER = 'sender';
    const PROPERTY_RECIPIENT = 'recipient';
    const PROPERTY_DATE = 'date';
    const PROPERTY_SUBJECT = 'subject';
    const PROPERTY_STATE = 'state';
    const PROPERTY_MESSAGE = 'message';
    const PROPERTY_HOST = 'host';
    const STATE_FAILED = 0;
    const STATE_SUCCESSFUL = 1;

    /**
     *
     * @return string
     */
    public function get_sender()
    {
        return $this->get_default_property(self :: PROPERTY_SENDER);
    }

    /**
     *
     * @param string $sender
     */
    public function set_sender($sender)
    {
        $this->set_default_property(self :: PROPERTY_SENDER, $sender);
    }

    /**
     *
     * @return string
     */
    public function get_recipient()
    {
        return $this->get_default_property(self :: PROPERTY_RECIPIENT);
    }

    /**
     *
     * @param string $recipient
     */
    public function set_recipient($recipient)
    {
        $this->set_default_property(self :: PROPERTY_RECIPIENT, $recipient);
    }

    /**
     *
     * @return int
     */
    public function get_date()
    {
        return $this->get_default_property(self :: PROPERTY_DATE);
    }

    /**
     *
     * @param int $date
     */
    public function set_date($date)
    {
        $this->set_default_property(self :: PROPERTY_DATE, $date);
    }

    /**
     *
     * @return string
     */
    public function get_subject()
    {
        return $this->get_default_property(self :: PROPERTY_SUBJECT);
    }

    /**
     *
     * @param string $subject
     */
    public function set_subject($subject)
    {
        $this->set_default_property(self :: PROPERTY_SUBJECT, $subject);
    }

    /**
     *
     * @return int
     */
    public function get_state()
    {
        return $this->get_default_property(self :: PROPERTY_STATE);
    }

    /**
     *
     * @param int $state
     */
    public function set_state($state)
    {
        $this->set_default_property(self :: PROPERTY_STATE, $state);
    }

    /**
     *
     * @return string
     */
    public function get_message()
    {
        return $this->get_default_property(self :: PROPERTY_MESSAGE);
    }

    /**
     *
     * @param string $message
     */
    public function set_message($message)
    {
        $this->set_default_property(self :: PROPERTY_MESSAGE, $message);
    }

    /**
     *
     * @return string
     */
    public function get_host()
    {
        return $this->get_default_property(self :: PROPERTY_HOST);
    }

    /**
     *
     * @param string $host
     */
    public function set_host($host)
    {
        $this->set_default_property(self :: PROPERTY_HOST, $host);
    }

    public static function get_default_property_names($extended_property_names = array())
    {
        return parent :: get_default_property_names(
            array(
                self :: PROPERTY_SENDER,
                self :: PROPERTY_RECIPIENT,
                self :: PROPERTY_DATE,
                self :: PROPERTY_SUBJECT,
                self :: PROPERTY_STATE,
                self :: PROPERTY_MESSAGE,
                self :: PROPERTY_HOST));
    }

    /**
     *
     * @return \configuration\storage\DataManager
     */
    public function get_data_manager()
    {
        return DataManager :: getInstance();
    }
}
