<?php
namespace Chamilo\Configuration\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

class MailLog extends DataClass
{
    public const CONTEXT = 'Chamilo\Configuration';

    public const PROPERTY_DATE = 'date';
    public const PROPERTY_HOST = 'host';
    public const PROPERTY_MESSAGE = 'message';
    public const PROPERTY_RECIPIENT = 'recipient';
    public const PROPERTY_SENDER = 'sender';
    public const PROPERTY_STATE = 'state';
    public const PROPERTY_SUBJECT = 'subject';

    public const STATE_FAILED = 0;
    public const STATE_SUCCESSFUL = 1;

    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [
                self::PROPERTY_SENDER,
                self::PROPERTY_RECIPIENT,
                self::PROPERTY_DATE,
                self::PROPERTY_SUBJECT,
                self::PROPERTY_STATE,
                self::PROPERTY_MESSAGE,
                self::PROPERTY_HOST
            ]
        );
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'configuration_mail_log';
    }

    /**
     * @return int
     */
    public function get_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_DATE);
    }

    /**
     * @return string
     */
    public function get_host()
    {
        return $this->getDefaultProperty(self::PROPERTY_HOST);
    }

    /**
     * @return string
     */
    public function get_message()
    {
        return $this->getDefaultProperty(self::PROPERTY_MESSAGE);
    }

    /**
     * @return string
     */
    public function get_recipient()
    {
        return $this->getDefaultProperty(self::PROPERTY_RECIPIENT);
    }

    /**
     * @return string
     */
    public function get_sender()
    {
        return $this->getDefaultProperty(self::PROPERTY_SENDER);
    }

    /**
     * @return int
     */
    public function get_state()
    {
        return $this->getDefaultProperty(self::PROPERTY_STATE);
    }

    /**
     * @return string
     */
    public function get_subject()
    {
        return $this->getDefaultProperty(self::PROPERTY_SUBJECT);
    }

    /**
     * @param int $date
     */
    public function set_date($date)
    {
        $this->setDefaultProperty(self::PROPERTY_DATE, $date);
    }

    /**
     * @param string $host
     */
    public function set_host($host)
    {
        $this->setDefaultProperty(self::PROPERTY_HOST, $host);
    }

    /**
     * @param string $message
     */
    public function set_message($message)
    {
        $this->setDefaultProperty(self::PROPERTY_MESSAGE, $message);
    }

    /**
     * @param string $recipient
     */
    public function set_recipient($recipient)
    {
        $this->setDefaultProperty(self::PROPERTY_RECIPIENT, $recipient);
    }

    /**
     * @param string $sender
     */
    public function set_sender($sender)
    {
        $this->setDefaultProperty(self::PROPERTY_SENDER, $sender);
    }

    /**
     * @param int $state
     */
    public function set_state($state)
    {
        $this->setDefaultProperty(self::PROPERTY_STATE, $state);
    }

    /**
     * @param string $subject
     */
    public function set_subject($subject)
    {
        $this->setDefaultProperty(self::PROPERTY_SUBJECT, $subject);
    }
}
