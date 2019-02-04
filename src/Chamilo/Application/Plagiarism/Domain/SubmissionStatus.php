<?php

namespace Chamilo\Application\Plagiarism\Domain;

/**
 * @package Chamilo\Application\Plagiarism\Domain\Turnitin
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class SubmissionStatus
{
    const STATUS_UPLOAD_IN_PROGRESS = 1;
    const STATUS_UPLOAD_COMPLETE = 2;
    const STATUS_CREATE_REPORT_IN_PROGRESS = 3;
    const STATUS_REPORT_GENERATED = 4;
    const STATUS_FAILED = 5;

    const ERROR_UNKNOWN = 1;
    const ERROR_INVALID_FILE = 2;
    const ERROR_FILE_TOO_SMALL = 3;
    const ERROR_FILE_TOO_LARGE = 4;
    const ERROR_TOO_MANY_PAGES = 5;
    const ERROR_FILE_CORRUPT = 6;
    const ERROR_FILE_LOCKED = 7;

    /**
     * @var string
     */
    protected $submissionId;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var int
     */
    protected $error;

    /**
     * @var int
     */
    protected $result;

    /**
     * SubmissionStatus constructor.
     *
     * @param string $submissionId
     * @param int $status
     * @param int $error
     * @param int $result
     */
    public function __construct(
        string $submissionId, int $status = self::STATUS_UPLOAD_IN_PROGRESS, int $result = null, int $error = null
    )
    {
        $this->setSubmissionId($submissionId);
        $this->setStatus($status);
        $this->setError($error);
        $this->setResult($result);
    }

    /**
     * @return string
     */
    public function getSubmissionId(): string
    {
        return $this->submissionId;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getError()
    {
        return $this->error;
    }



    /**
     * @return int
     */
    public function getResult()
    {
        return $this->result;
    }

    /*
     * ALLOWED VALUES
     */

    /**
     * @return int[]
     */
    public static function getAllowedStatuses()
    {
        return [
            SubmissionStatus::STATUS_UPLOAD_IN_PROGRESS, SubmissionStatus::STATUS_CREATE_REPORT_IN_PROGRESS,
            SubmissionStatus::STATUS_UPLOAD_COMPLETE, self::STATUS_REPORT_GENERATED, SubmissionStatus::STATUS_FAILED
        ];
    }

    /**
     * @return int[]
     */
    public static function getAllowedErrorCodes()
    {
        return [
            self::ERROR_UNKNOWN, self::ERROR_INVALID_FILE, self::ERROR_FILE_TOO_SMALL, self::ERROR_FILE_TOO_LARGE,
            self::ERROR_TOO_MANY_PAGES, self::ERROR_FILE_CORRUPT
        ];
    }

    /*
     * HELPER METHODS
     */

    /**
     * @return bool
     */
    public function isInProgress()
    {
        return $this->getStatus() == SubmissionStatus::STATUS_UPLOAD_IN_PROGRESS ||
            $this->getStatus() == SubmissionStatus::STATUS_CREATE_REPORT_IN_PROGRESS;
    }

    /**
     * @return bool
     */
    public function isUploadInProgress()
    {
        return $this->getStatus() == SubmissionStatus::STATUS_UPLOAD_IN_PROGRESS;
    }

    /**
     * @return bool
     */
    public function isUploadComplete()
    {
        return $this->getStatus() == SubmissionStatus::STATUS_UPLOAD_COMPLETE;
    }

    /**
     * @return bool
     */
    public function isReportGenerationInProgress()
    {
        return $this->getStatus() == SubmissionStatus::STATUS_CREATE_REPORT_IN_PROGRESS;
    }

    /**
     * @return bool
     */
    public function isReportGenerated()
    {
        return $this->getStatus() == SubmissionStatus::STATUS_REPORT_GENERATED;
    }

    /**
     * @return bool
     */
    public function isFailed()
    {
        return $this->getStatus() == SubmissionStatus::STATUS_FAILED;
    }

    /**
     * @return bool
     */
    public function canRetry()
    {
        return $this->getError() == SubmissionStatus::ERROR_UNKNOWN;
    }


    /*
     * PRIVATE SETTERS
     */

    /**
     * @param string $submissionId
     */
    protected function setSubmissionId(string $submissionId)
    {
        $this->submissionId = $submissionId;
    }

    /**
     * @param int $status
     */
    protected function setStatus(int $status)
    {
        if (!in_array($status, self::getAllowedStatuses()))
        {
            throw new \InvalidArgumentException(sprintf('The given status %s is not supported', $status));
        }

        $this->status = $status;
    }

    /**
     * @param int $error
     */
    protected function setError(int $error = null)
    {
        if (!empty($error) && !in_array($error, self::getAllowedErrorCodes()))
        {
            throw new \InvalidArgumentException(sprintf('The given error code %s is not supported', $error));
        }

        $this->error = $error;
    }

    /**
     * @param int $result
     */
    protected function setResult(int $result = null)
    {
        if(empty($result))
        {
            return;
        }

        if (!is_integer($result) || $result < 0 || $result > 100)
        {
            throw new \InvalidArgumentException(
                sprintf(
                    'The given $result (%s) is empty or is not within the valid range between 0 and 100',
                    $result
                )
            );
        }

        $this->result = $result;
    }
}