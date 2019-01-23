<?php

namespace Chamilo\Application\Plagiarism\Service\Turnitin;

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
     * @var \Chamilo\Application\Plagiarism\Service\Turnitin\Events\TurnitinEventNotifier
     */
    protected $turnitinEventNotifier;

    /**
     * @param string $eventType
     * @param string $authorizationKey
     * @param string $requestBody
     */
    public function handleWebhookRequest(string $eventType, string $authorizationKey, string $requestBody)
    {
        $this->validateAuthorizationKey($authorizationKey, $requestBody);
        $this->validateEventType($eventType);

        $data = $this->getDataFromRequestBody($requestBody);

        switch ($eventType)
        {
            case WebhookManager::WEBHOOK_SUBMISSION_COMPLETE:
                $this->handleSubmissionComplete($data);
                break;
            case WebhookManager::WEBHOOK_SIMILARITY_COMPLETE:
                $this->handleSimilarityComplete($data);
                break;
        }
    }

    /**
     * @param array $data
     */
    protected function handleSubmissionComplete(array $data)
    {
        $submissionId = $data['id'];
        if (empty($submissionId))
        {
            throw new \InvalidArgumentException('The given submission ID could not be found in the data');
        }

        $status = $data['status'];

        if (!in_array($status, [self::STATUS_COMPLETE, self::STATUS_ERROR]))
        {
            throw new \InvalidArgumentException(
                sprintf('The given status should be either COMPLETE or ERROR, %s given', $status)
            );
        }

        $isError = $status == self::STATUS_ERROR;
        $errorCode = null;

        if ($isError)
        {
            $errorCode = $data['error_code'];
        }

        $this->turnitinEventNotifier->submissionUploadProcessed($submissionId, $isError, $errorCode);
    }

    /**
     * @param array $data
     */
    protected function handleSimilarityComplete(array $data)
    {
        $submissionId = $data['submission_id'];
        if (empty($submissionId))
        {
            throw new \InvalidArgumentException('The given submission ID could not be found in the data');
        }

        $status = $data['status'];
        if (!$status == self::STATUS_COMPLETE)
        {
            throw new \InvalidArgumentException(sprintf('The given status should be COMPLETE, %s given', $status));
        }

        $overallMatchPercentage = $data['overall_match_percentage'];
        if (!is_integer($overallMatchPercentage) || $overallMatchPercentage < 0 || $overallMatchPercentage > 100)
        {
            throw new \InvalidArgumentException(
                sprintf(
                    'The given match percentage (%s) could not be found or is not within the valid range between 0 and 100',
                    $overallMatchPercentage
                )
            );
        }

        $this->turnitinEventNotifier->similarityReportGenerated($submissionId, $overallMatchPercentage);
    }

    /**
     * Validates the secret key from the webhook
     *
     * @param string $authorizationKey
     * @param string $requestBody
     */
    protected function validateAuthorizationKey(string $authorizationKey, string $requestBody)
    {
        $webhookSecret = $this->configurationConsulter->getSetting(
            ['Chamilo\Application\Plagiarism', 'turnitin_webhook_secret']
        );

        if (empty($storedAuthorizationKey))
        {
            throw new \RuntimeException(
                'The stored authorization key is empty and therefor it is insecure to call this service, request aborted'
            );
        }

        $expectedKey = hash_hmac('sha256', $requestBody, $webhookSecret);
        if($expectedKey != $authorizationKey)
        {
            throw new \RuntimeException('The given authorization key is not correct');
        }
    }

    /**
     * @param string $eventType
     */
    protected function validateEventType(string $eventType)
    {
        $availableEventTypes =
            [WebhookManager::WEBHOOK_SIMILARITY_COMPLETE, WebhookManager::WEBHOOK_SUBMISSION_COMPLETE];

        if (!in_array($eventType, $availableEventTypes))
        {
            throw new \InvalidArgumentException(sprintf('The given event type %s is invalid', $eventType));
        }
    }

    /**
     * @param string $requestBody
     *
     * @return array
     */
    protected function getDataFromRequestBody(string $requestBody)
    {
        $data = \json_decode($requestBody, true);
        if (empty($data))
        {
            throw new \InvalidArgumentException(
                sprintf('Could not decode the data from the request body, not a valid json string (%s)', $requestBody)
            );
        }

        return $data;
    }
}