<?php

namespace Chamilo\Application\Plagiarism\Service\Turnitin;

use Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException;
use Chamilo\Application\Plagiarism\Domain\SubmissionStatus;

/**
 * @package Chamilo\Application\Plagiarism\Service\Turnitin
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class SubmissionStatusParser
{
    const SUBMISSION_STATUS_UPLOAD = 1;
    const SUBMISSION_STATUS_REPORT_GENERATION = 2;

    /**
     * @param int $submissionStatus
     * @param array $statusData
     *
     * @return \Chamilo\Application\Plagiarism\Domain\SubmissionStatus
     *
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     */
    public function parse(int $submissionStatus, array $statusData = array())
    {
        if (
            $submissionStatus != self::SUBMISSION_STATUS_UPLOAD &&
            $submissionStatus != self::SUBMISSION_STATUS_REPORT_GENERATION
        )
        {
            throw new PlagiarismException(sprintf('The given submission status %s is invalid', $submissionStatus));
        }

        switch($submissionStatus)
        {
            case self::SUBMISSION_STATUS_UPLOAD:
                return $this->parseUploadStatus($statusData);
            case self::SUBMISSION_STATUS_REPORT_GENERATION:
                return $this->parseReportGenerationStatus($statusData);
        }

        return null;
    }

    /**
     * @param array $statusData
     *
     * @return \Chamilo\Application\Plagiarism\Domain\SubmissionStatus
     *
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     */
    protected function parseUploadStatus(array $statusData = array())
    {
        if (empty($statusData))
        {
            throw new PlagiarismException(sprintf('Could not parse the upload status due to empty data'));
        }

        $submissionId = $statusData['id'];
        $status = $statusData['status'];
        if ($status == 'COMPLETE' || $status == 'COMPLETED')
        {
            return new SubmissionStatus($submissionId, SubmissionStatus::STATUS_UPLOAD_COMPLETE);
        }

        if ($status == 'CREATED' || $status == 'PROCESSING')
        {
            return new SubmissionStatus($submissionId, SubmissionStatus::STATUS_UPLOAD_IN_PROGRESS);
        }

        $error = SubmissionStatus::ERROR_UNKNOWN;

        $errorMapping = [
            'UNSUPPORTED_FILETYPE' => SubmissionStatus::ERROR_INVALID_FILE,
            'PROCESSING_ERROR' => SubmissionStatus::ERROR_UNKNOWN,
            'TOO_LITTLE_TEXT' => SubmissionStatus::ERROR_FILE_TOO_SMALL,
            'TOO_MUCH_TEXT' => SubmissionStatus::ERROR_FILE_TOO_LARGE,
            'TOO_MANY_PAGES' => SubmissionStatus::ERROR_TOO_MANY_PAGES,
            'FILE_LOCKED' => SubmissionStatus::ERROR_FILE_LOCKED,
            'CORRUPT_FILE' => SubmissionStatus::ERROR_FILE_CORRUPT,
        ];

        $errorCode = $statusData['error_code'];
        if (array_key_exists($errorCode, $errorMapping))
        {
            $error = $errorMapping[$errorCode];
        }

        return new SubmissionStatus($submissionId, SubmissionStatus::STATUS_FAILED, 0, $error);
    }

    /**
     * @param array $statusData
     *
     * @return \Chamilo\Application\Plagiarism\Domain\SubmissionStatus
     *
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     */
    protected function parseReportGenerationStatus(array $statusData = array())
    {
        if (empty($statusData))
        {
            throw new PlagiarismException(sprintf('Could not parse the report generation status due to empty data'));
        }

        $submissionId = $statusData['submission_id'];
        $status = $statusData['status'];
        if ($status == 'COMPLETE')
        {
            return new SubmissionStatus(
                $submissionId, SubmissionStatus::STATUS_REPORT_GENERATED,
                $statusData['overall_match_percentage']
            );
        }

        return new SubmissionStatus($submissionId, SubmissionStatus::STATUS_CREATE_REPORT_IN_PROGRESS);
    }
}