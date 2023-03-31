<?php
namespace Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class PresenceResultPeriod extends DataClass
{
    const PROPERTY_PRESENCE_ID = 'presence_id';
    const PROPERTY_LABEL = 'label';
    const PROPERTY_DATE = 'date';
    const PROPERTY_CONTEXT_CLASS = 'context_class';
    const PROPERTY_CONTEXT_ID = 'context_id';
    const PROPERTY_PERIOD_SELF_REGISTRATION_DISABLED = 'period_self_registration_disabled';

    /**
     * Get the default properties
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array()): array
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_PRESENCE_ID,
                self::PROPERTY_LABEL,
                self::PROPERTY_DATE,
                self::PROPERTY_CONTEXT_CLASS,
                self::PROPERTY_CONTEXT_ID,
                self::PROPERTY_PERIOD_SELF_REGISTRATION_DISABLED
            )
        );
    }

    /**
     * @return int
     */
    public function getPresenceId(): int
    {
        return $this->get_default_property(self::PROPERTY_PRESENCE_ID);
    }

    /**
     * @param int $presenceId
     */
    public function setPresenceId(int $presenceId)
    {
        $this->set_default_property(self::PROPERTY_PRESENCE_ID, $presenceId);
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->get_default_property(self::PROPERTY_LABEL);
    }

    /**
     *
     * @param string $label
     */
    public function setLabel(string $label)
    {
        $this->set_default_property(self::PROPERTY_LABEL, $label);
    }

    /**
     * @return int
     */
    public function getDate(): int
    {
        return $this->get_default_property(self::PROPERTY_DATE);
    }

    /**
     * @param int $date
     */
    public function setDate(int $date)
    {
        $this->set_default_property(self::PROPERTY_DATE, $date);
    }

    /**
     * @return string
     */
    public function getContextClass(): string
    {
        return $this->get_default_property(self::PROPERTY_CONTEXT_CLASS);
    }

    /**
     * @param string $context_class
     */
    public function setContextClass(string $context_class)
    {
        $this->set_default_property(self::PROPERTY_CONTEXT_CLASS, $context_class);
    }

    /**
     * @return int
     */
    public function getContextId(): int
    {
        return $this->get_default_property(self::PROPERTY_CONTEXT_ID);
    }

    /**
     * @param int $context_id
     */
    public function setContextId(int $context_id)
    {
        $this->set_default_property(self::PROPERTY_CONTEXT_ID, $context_id);
    }

    /**
     * @return bool
     */
    public function isPeriodSelfRegistrationDisabled(): bool
    {
        return $this->get_default_property(self::PROPERTY_PERIOD_SELF_REGISTRATION_DISABLED);
    }

    /**
     * @param bool $selfRegistrationDisabled
     */
    public function setPeriodSelfRegistrationDisabled(bool $selfRegistrationDisabled)
    {
        $this->set_default_property(self::PROPERTY_PERIOD_SELF_REGISTRATION_DISABLED, $selfRegistrationDisabled);
    }

    public static function get_table_name(): string
    {
        return 'repository_presence_result_period';
    }
}

