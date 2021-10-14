<?php
namespace Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class PresenceResultEntry extends DataClass
{
    const PROPERTY_PRESENCE_PERIOD_ID = 'presence_period_id';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_CHOICE_ID = 'choice_id';
    const PROPERTY_PRESENCE_STATUS_ID = 'presence_status_id';
    const PROPERTY_CHECKED_IN_DATE = 'checked_in_date';
    const PROPERTY_CHECKED_OUT_DATE = 'checked_out_date';

    /**
     * Get the default properties
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array()): array
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_PRESENCE_PERIOD_ID,
                self::PROPERTY_USER_ID,
                self::PROPERTY_CHOICE_ID,
                self::PROPERTY_PRESENCE_STATUS_ID,
                self::PROPERTY_CHECKED_IN_DATE,
                self::PROPERTY_CHECKED_OUT_DATE
            )
        );
    }

    /**
     * @return int
     */
    public function getPresencePeriodId(): int
    {
        return $this->get_default_property(self::PROPERTY_PRESENCE_PERIOD_ID);
    }

    /**
     * @param int $presencePeriodId
     */
    public function setPresencePeriodId(int $presencePeriodId)
    {
        $this->set_default_property(self::PROPERTY_PRESENCE_PERIOD_ID, $presencePeriodId);
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->get_default_property(self::PROPERTY_USER_ID);
    }

    /**
     *
     * @param int $userId
     */
    public function setUserId(int $userId)
    {
        $this->set_default_property(self::PROPERTY_USER_ID, $userId);
    }

    /**
     * @return int
     */
    public function getChoiceId(): int
    {
        return $this->get_default_property(self::PROPERTY_CHOICE_ID);
    }

    /**
     *
     * @param int $choiceId
     */
    public function setChoiceId(int $choiceId)
    {
        $this->set_default_property(self::PROPERTY_CHOICE_ID, $choiceId);
    }

    /**
     * @return int
     */
    public function getPresenceStatusId(): int
    {
        return $this->get_default_property(self::PROPERTY_PRESENCE_STATUS_ID);
    }

    /**
     *
     * @param int $presenceStatusId
     */
    public function setPresenceStatusId(int $presenceStatusId)
    {
        $this->set_default_property(self::PROPERTY_PRESENCE_STATUS_ID, $presenceStatusId);
    }

    /**
     * @return int
     */
    public function getCheckedInDate(): int
    {
        return $this->get_default_property(self::PROPERTY_CHECKED_IN_DATE);
    }

    /**
     * @param int $checkedInDate
     */
    public function setCheckedInDate(int $checkedInDate)
    {
        $this->set_default_property(self::PROPERTY_CHECKED_IN_DATE, $checkedInDate);
    }

    /**
     * @return int
     */
    public function getCheckedOutDate(): ?int
    {
        return $this->get_default_property(self::PROPERTY_CHECKED_OUT_DATE);
    }

    /**
     * @param int $checkedOutDate
     */
    public function setCheckedOutDate(int $checkedOutDate)
    {
        $this->set_default_property(self::PROPERTY_CHECKED_OUT_DATE, $checkedOutDate);
    }

    public static function get_table_name(): string
    {
        return 'repository_presence_result_entry';
    }
}

