<?php

namespace Chamilo\Application\Plagiarism\Service\Turnitin;

use Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException;
use Chamilo\Application\Plagiarism\Domain\SubmissionStatus;
use Chamilo\Application\Plagiarism\Domain\Turnitin\SimilarityReportSettings;

/**
 * @package Chamilo\Application\Plagiarism\Domain\Turnitin
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class WebhookHandler
{
    const STATUS_COMPLETE = 'COMPLETE';
    const STATUS_ERROR = 'ERROR';

    /**
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    protected $configurationConsulter;

    /**
     * @var \Chamilo\Application\Plagiarism\Service\Events\PlagiarismEventNotifier
     */
    protected $plagiarismEventNotifier;

    /**
     * @var \Chamilo\Application\Plagiarism\Service\Turnitin\SubmissionStatusParser
     */
    protected $submissionStatusParser;

    /**
     * @var \Chamilo\Application\Plagiarism\Service\Turnitin\SubmissionService
     */
    protected $submissionService;

    /**
     * WebhookHandler constructor.
     *
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     * @param \Chamilo\Application\Plagiarism\Service\Events\PlagiarismEventNotifier $plagiarismEventNotifier
     * @param \Chamilo\Application\Plagiarism\Service\Turnitin\SubmissionStatusParser $submissionStatusParser
     * @param \Chamilo\Application\Plagiarism\Service\Turnitin\SubmissionService $submissionService
     */
    public function __construct(
        \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter,
        \Chamilo\Application\Plagiarism\Service\Events\PlagiarismEventNotifier $plagiarismEventNotifier,
        \Chamilo\Application\Plagiarism\Service\Turnitin\SubmissionStatusParser $submissionStatusParser,
        SubmissionService $submissionService
    )
    {
        $this->configurationConsulter = $configurationConsulter;
        $this->plagiarismEventNotifier = $plagiarismEventNotifier;
        $this->submissionStatusParser = $submissionStatusParser;
        $this->submissionService = $submissionService;
    }

    /**
     * @param string $eventType
     * @param string $authorizationKey
     * @param string $requestBody
     *
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     */
    public function handleWebhookRequest(
        string $eventType = null, string $authorizationKey = null, string $requestBody = null
    )
    {
        $this->validateAuthorizationKey($authorizationKey, $requestBody);
        $this->validateEventType($eventType);

        $data = $this->getDataFromRequestBody($requestBody);

        $webhookToStatusMapping = [
            WebhookManager::WEBHOOK_SUBMISSION_COMPLETE => SubmissionStatusParser::SUBMISSION_STATUS_UPLOAD,
            WebhookManager::WEBHOOK_SIMILARITY_COMPLETE => SubmissionStatusParser::SUBMISSION_STATUS_REPORT_GENERATION
        ];

        if (array_key_exists($eventType, $webhookToStatusMapping))
        {
            $submissionStatus = $this->submissionStatusParser->parse($webhookToStatusMapping[$eventType], $data);

            if ($submissionStatus->isUploadComplete())
            {
                $submissionStatus = $this->handleUploadComplete($submissionStatus);
            }

            $this->plagiarismEventNotifier->submissionStatusChanged($submissionStatus);
        }
    }

    /**
     * @param \Chamilo\Application\Plagiarism\Domain\SubmissionStatus $currentSubmissionStatus
     *
     * @return \Chamilo\Application\Plagiarism\Domain\SubmissionStatus
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     */
    protected function handleUploadComplete(SubmissionStatus $currentSubmissionStatus)
    {
        $settings = new SimilarityReportSettings();
        $settings->setSearchRepositories(
            [
                SimilarityReportSettings::SEARCH_REPOSITORY_INTERNET,
                SimilarityReportSettings::SEARCH_REPOSITORY_PUBLICATION,
                SimilarityReportSettings::SEARCH_REPOSITORY_SUBMITTED_WORK
            ]
        );

        $settings->setAutoExcludeMatchingScope(SimilarityReportSettings::AUTO_EXCLUDE_ALL);

        $this->submissionService->generateSimilarityReport($currentSubmissionStatus->getSubmissionId(), $settings);

        return new SubmissionStatus(
            $currentSubmissionStatus->getSubmissionId(), SubmissionStatus::STATUS_CREATE_REPORT_IN_PROGRESS
        );
    }

    /**
     * Validates the secret key from the webhook
     *
     * @param string $authorizationKey
     * @param string $requestBody
     *
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     */
    protected function validateAuthorizationKey(string $authorizationKey = null, string $requestBody = null)
    {
        if(empty($authorizationKey))
        {
            throw new PlagiarismException(
                'The given authorization key is empty and therefor it is insecure to call this service, request aborted'
            );
        }

        $webhookSecret = $this->configurationConsulter->getSetting(
            ['Chamilo\Application\Plagiarism', 'turnitin_webhook_secret']
        );

        if (empty($storedAuthorizationKey))
        {
            throw new PlagiarismException(
                'The stored authorization key is empty and therefor it is insecure to call this service, request aborted'
            );
        }

        $expectedKey = hash_hmac('sha256', $requestBody, $webhookSecret);
        if ($expectedKey != $authorizationKey)
        {
            throw new PlagiarismException('The given authorization key is not correct');
        }
    }

    /**
     * @param string $eventType
     *
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     */
    protected function validateEventType(string $eventType = null)
    {
        $availableEventTypes =
            [WebhookManager::WEBHOOK_SIMILARITY_COMPLETE, WebhookManager::WEBHOOK_SUBMISSION_COMPLETE];

        if (!in_array($eventType, $availableEventTypes))
        {
            throw new PlagiarismException(sprintf('The given event type %s is invalid', $eventType));
        }
    }

    /**
     * @param string $requestBody
     *
     * @return array
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     */
    protected function getDataFromRequestBody(string $requestBody)
    {
        $data = \json_decode($requestBody, true);
        if (empty($data))
        {
            throw new PlagiarismException(
                sprintf('Could not decode the data from the request body, not a valid json string (%s)', $requestBody)
            );
        }

        return $data;
    }
}