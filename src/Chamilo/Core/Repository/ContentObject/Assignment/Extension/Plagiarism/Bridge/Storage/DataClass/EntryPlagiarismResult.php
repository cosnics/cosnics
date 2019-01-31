<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass;

use Chamilo\Application\Plagiarism\Domain\Turnitin\SubmissionStatus;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Storage\DataClass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class EntryPlagiarismResult extends DataClass
{
    const PROPERTY_ENTRY_ID = 'entry_id';
    const PROPERTY_EXTERNAL_ID = 'external_id';
    const PROPERTY_STATUS = 'status';
    const PROPERTY_RESULT = 'result';
    const PROPERTY_ERROR = 'error_code';

    /**
     * @var SubmissionStatus
     */
    protected $submissionStatus;

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
        $extended_property_names[] = self::PROPERTY_ERROR;

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
        if (!in_array($status, SubmissionStatus::getAllowedStatuses()))
        {
            throw new \InvalidArgumentException(sprintf('The given status %s is not allowed', $status));
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
    public function setResult(string $status = null)
    {
        $this->set_default_property(self::PROPERTY_RESULT, $status);
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->get_default_property(self::PROPERTY_ERROR);
    }

    /**
     * @param string $error
     */
    public function setError(string $error = null)
    {
        if (!empty($error) && !in_array($error, SubmissionStatus::getAllowedErrorCodes()))
        {
            throw new \InvalidArgumentException(sprintf('The given error code %s is not allowed', $error));
        }

        $this->set_default_property(self::PROPERTY_ERROR, $error);
    }

    /**
     * @return SubmissionStatus
     */
    public function getSubmissionStatus()
    {
        if(!$this->submissionStatus)
        {
            $this->submissionStatus = new SubmissionStatus();
            $this->submissionStatus->setStatus($this->getStatus());
            $this->submissionStatus->setResult($this->getResult());
            $this->submissionStatus->setError($this->getError());

        }

        return $this->submissionStatus;
    }

    /**
     * @param \Chamilo\Application\Plagiarism\Domain\Turnitin\SubmissionStatus $submissionStatus
     */
    public function copyFromSubmissionStatus(SubmissionStatus $submissionStatus)
    {
        $this->setStatus($submissionStatus->getStatus());
        $this->setResult($submissionStatus->getResult());
        $this->setError($submissionStatus->getError());
    }

}