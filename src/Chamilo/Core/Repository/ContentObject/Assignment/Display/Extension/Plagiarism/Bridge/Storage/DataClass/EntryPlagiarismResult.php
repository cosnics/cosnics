<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Extension\Plagiarism\Bridge\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Extension\Plagiarism\Storage\DataClass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class EntryPlagiarismResult extends DataClass
{
    const PROPERTY_ENTRY_ID = 'entry_id';
    const PROPERTY_EXTERNAL_ID = 'external_id';
    const PROPERTY_STATUS = 'status';
    const PROPERTY_RESULT = 'result';
    const PROPERTY_ERROR_CODE = 'error_code';

    const STATUS_IN_PROGRESS = 1;
    const STATUS_FAILED = 2;
    const STATUS_SUCCESS = 3;

    /**
     * Get the default properties of all feedback
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self::PROPERTY_ENTRY_ID;
        $extended_property_names[] = self::PROPERTY_EXTERNAL_ID;
        $extended_property_names[] = self::PROPERTY_STATUS;
        $extended_property_names[] = self::PROPERTY_RESULT;
        $extended_property_names[] = self::PROPERTY_ERROR_CODE;

        return parent::get_default_property_names($extended_property_names);
    }

    /**
     * @return int
     */
    public function getEntryId()
    {
        return $this->get_default_property(self::PROPERTY_ENTRY_ID);
    }

    /**
     * @param int $entryId
     */
    public function setEntryId($entryId)
    {
        $this->set_default_property(self::PROPERTY_ENTRY_ID, $entryId);
    }

    /**
     * @return string
     */
    public function getExternalId()
    {
        return $this->get_default_property(self::PROPERTY_EXTERNAL_ID);
    }

    /**
     * @param string $externalId
     */
    public function setExternalId(string $externalId)
    {
        $this->set_default_property(self::PROPERTY_EXTERNAL_ID, $externalId);
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->get_default_property(self::PROPERTY_STATUS);
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status)
    {
        $allowedStatuses = $this->getAllowedStatuses();

        if (!in_array($status, $allowedStatuses))
        {
            throw new \InvalidArgumentException(
                sprintf(
                    'The given status %s is not allowed. Status must be either one of the following constants (STATUS_IN_PROGRESS, STATUS_SUCCESS, STATUS_FAILED)'
                )
            );
        }

        $this->set_default_property(self::PROPERTY_STATUS, $status);
    }

    /**
     * @return string
     */
    public function getResult()
    {
        return $this->get_default_property(self::PROPERTY_RESULT);
    }

    /**
     * @param string $status
     */
    public function setResult(string $status)
    {
        $this->set_default_property(self::PROPERTY_RESULT, $status);
    }

    /**
     * @return string
     */
    public function getErrorCode()
    {
        return $this->get_default_property(self::PROPERTY_ERROR_CODE);
    }

    /**
     * @param string $errorCode
     */
    public function setErrorCode(string $errorCode)
    {
        $this->set_default_property(self::PROPERTY_ERROR_CODE, $errorCode);
    }

    /**
     * @return array
     */
    public function getAllowedStatuses()
    {
        return [self::STATUS_IN_PROGRESS, self::STATUS_SUCCESS, self::STATUS_FAILED];
    }

}