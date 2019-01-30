<?php

namespace Chamilo\Application\Plagiarism\Repository\Turnitin;

use Chamilo\Application\Plagiarism\Domain\Turnitin\Exception\MalformedRequestException;
use Chamilo\Application\Plagiarism\Domain\Turnitin\Exception\NotAuthenticatedException;
use Chamilo\Application\Plagiarism\Domain\Turnitin\Exception\NotFoundException;
use Chamilo\Application\Plagiarism\Domain\Turnitin\Exception\RateLimitException;
use Chamilo\Application\Plagiarism\Domain\Turnitin\Exception\UnexpectedErrorException;
use Chamilo\Application\Plagiarism\Domain\Turnitin\SimilarityReportSettings;
use Chamilo\Application\Plagiarism\Domain\Turnitin\TurnitinConfig;
use Chamilo\Application\Plagiarism\Domain\Turnitin\TurnitinRequest;
use Chamilo\Application\Plagiarism\Domain\Turnitin\ViewerLaunchSettings;
use Chamilo\Application\Plagiarism\Service\Turnitin\EulaService;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

/**
 * @package Chamilo\Application\Plagiarism\Repository\Turnitin
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TurnitinRepository
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var \Chamilo\Application\Plagiarism\Domain\Turnitin\TurnitinConfig
     */
    protected $turnitinConfig;

    /**
     * TurnitinRepository constructor.
     *
     * @param \Chamilo\Application\Plagiarism\Domain\Turnitin\TurnitinConfig $turnitinConfig
     */
    public function __construct(TurnitinConfig $turnitinConfig)
    {
        $this->turnitinConfig = $turnitinConfig;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getEnabledFeatures()
    {
        $request = new TurnitinRequest('GET', 'features-enabled', $this->getSecretKey());

        return $this->handleJSONRequest($request);
    }

    /**
     * @param string $versionId
     *
     * @return array
     * @throws \Exception
     */
    public function getEULAVersionInfo($versionId = 'latest')
    {
        $url = sprintf('eula/%s', $versionId);
        $request = new TurnitinRequest('GET', $url, $this->getSecretKey());

        return $this->handleJSONRequest($request);
    }

    /**
     * @param string $userId
     * @param \DateTime $acceptedTimestamp
     * @param string $language
     * @param string $versionId
     *
     * @return array
     * @throws \Exception
     */
    public function acceptEULAVersion(
        string $userId, \DateTime $acceptedTimestamp, string $language, $versionId = 'latest'
    )
    {
        $url = sprintf('eula/%s/accept', $versionId);

        $body = [
            'user_id' => $userId, 'accepted_timestamp' => $acceptedTimestamp->format(EulaService::ZULU_DATE_FORMAT),
            'language' => $language
        ];

        $bodyString = json_encode($body);

        $request = new TurnitinRequest('POST', $url, $this->getSecretKey(), $bodyString);

        return $this->handleJSONRequest($request);
    }

    /**
     * @param string $userId
     * @param string $versionId
     *
     * @return array
     * @throws \Exception
     */
    public function getEULAUserAcceptanceInfo(string $userId, $versionId = 'latest')
    {
        $url = sprintf('eula/%s/accept/%s', $versionId, $userId);
        $request = new TurnitinRequest('GET', $url, $this->getSecretKey());

        return $this->handleJSONRequest($request);
    }

    /**
     * @param string $versionId
     *
     * @return string
     * @throws \Exception
     */
    public function getEULAPage($versionId = 'latest')
    {
        $url = sprintf('eula/%s/view', $versionId);
        $request = new TurnitinRequest('GET', $url, $this->getSecretKey());

        return $this->handleRequest($request);
    }

    /**
     * @param string $userId
     * @param string $ownerId
     * @param string $title
     * @param bool $extractTextOnly
     * @param array $metadata
     * @param array $eula
     *
     * @return array
     * @throws \Exception
     */
    public function createSubmission(
        string $userId, string $ownerId, string $title, bool $extractTextOnly = false, array $metadata = null,
        array $eula = null
    )
    {
        $url = sprintf('submissions');

        $body = [
            'submitter' => $userId, 'owner' => $ownerId, 'title' => $title,
            'extract_text_only' => $extractTextOnly, 'metadata' => $metadata, 'eula' => $eula
        ];

        $bodyString = json_encode($body);
        $request = new TurnitinRequest('POST', $url, $this->getSecretKey(), $bodyString);

        return $this->handleJSONRequest($request);
    }

    /**
     * @param string $submissionId
     * @param string $filename
     * @param \resource $file
     *
     * @return array
     * @throws \Exception
     */
    public function uploadSubmissionFile(string $submissionId, string $filename, $file)
    {
        $url = sprintf('submissions/%s/original', $submissionId);
        $contentDisposition = sprintf('inline; filename="%s"', $filename);

        $headers = ['Content-Type' => 'binary/octet-stream', 'Content-Disposition' => $contentDisposition];
        $request = new TurnitinRequest('PUT', $url, $this->getSecretKey(), $file, $headers);

        return $this->handleJSONRequest($request);
    }

    /**
     * @param string $submissionId
     *
     * @return array
     * @throws \Exception
     */
    public function getSubmissionInfo(string $submissionId)
    {
        $url = sprintf('submissions/%s', $submissionId);
        $request = new TurnitinRequest('GET', $url, $this->getSecretKey());

        return $this->handleJSONRequest($request);
    }

    /**
     * @param string $submissionId
     * @param bool $hardDelete
     *
     * @return array
     * @throws \Exception
     */
    public function deleteSubmission(string $submissionId, bool $hardDelete = false)
    {
        $url = sprintf('submissions/%s', $submissionId);

        if ($hardDelete)
        {
            $url .= '?hard=true';
        }

        $request = new TurnitinRequest('DELETE', $url, $this->getSecretKey());

        return $this->handleJSONRequest($request);
    }

    /**
     * @param string $submissionId
     *
     * @return array
     * @throws \Exception
     */
    public function recoverSubmission(string $submissionId)
    {
        $url = sprintf('submissions/%s/recover', $submissionId);
        $request = new TurnitinRequest('PUT', $url, $this->getSecretKey());

        return $this->handleJSONRequest($request);
    }

    /**
     * @param string $submissionId
     * @param \Chamilo\Application\Plagiarism\Domain\Turnitin\SimilarityReportSettings $similarityReportSettings
     *
     * @return array
     * @throws \Exception
     */
    public function generateSimilarityReport(
        string $submissionId, SimilarityReportSettings $similarityReportSettings
    )
    {
        $url = sprintf('submissions/%s/similarity', $submissionId);

        $body = $similarityReportSettings->toArray();
        $bodyString = json_encode($body);

        $request = new TurnitinRequest('PUT', $url, $this->getSecretKey(), $bodyString);

        return $this->handleJSONRequest($request);
    }

    /**
     * @param string $submissionId
     *
     * @return array
     * @throws \Exception
     */
    public function getSimilarityReportInfo(string $submissionId)
    {
        $url = sprintf('submissions/%s/similarity', $submissionId);
        $request = new TurnitinRequest('GET', $url, $this->getSecretKey());

        return $this->handleJSONRequest($request);
    }

    /**
     * @param string $submissionId
     * @param string $viewerUserId
     * @param \Chamilo\Application\Plagiarism\Domain\Turnitin\ViewerLaunchSettings $viewerLaunchSettings
     *
     * @return array
     * @throws \Exception
     */
    public function createViewerLaunchURL(
        string $submissionId, string $viewerUserId, ViewerLaunchSettings $viewerLaunchSettings
    )
    {
        $url = sprintf('submissions/%s/viewer-url', $submissionId);

        $body = $viewerLaunchSettings->toArray();
        $body['viewerUserId'] = $viewerUserId;
        $bodyString = json_encode($body);

        $request = new TurnitinRequest('POST', $url, $this->getSecretKey(), $bodyString);

        return $this->handleJSONRequest($request);
    }

    /**
     * @param string $submissionId
     *
     * @return array
     * @throws \Exception
     */
    public function indexSubmission(string $submissionId)
    {
        $url = sprintf('submissions/%s/index', $submissionId);
        $request = new TurnitinRequest('PUT', $url, $this->getSecretKey());

        return $this->handleJSONRequest($request);
    }

    /**
     * @param string $submissionId
     *
     * @return array
     * @throws \Exception
     */
    public function getSubmissionIndex(string $submissionId)
    {
        $url = sprintf('submissions/%s/index', $submissionId);
        $request = new TurnitinRequest('GET', $url, $this->getSecretKey());

        return $this->handleJSONRequest($request);
    }

    /**
     * @param string $signingSecret (base 64 encoded)
     * @param string $webhookUrl
     * @param array $eventTypes
     * @param string $description
     * @param bool $allowInsecureURL
     *
     * @return array
     * @throws \Exception
     */
    public function createWebhook(
        string $signingSecret, string $webhookUrl, array $eventTypes, string $description = '',
        bool $allowInsecureURL = false
    )
    {
        $url = sprintf('webhooks');

        $body = [
            'description' => $description, 'signing_secret' => $signingSecret, 'url' => $webhookUrl,
            'event_types' => $eventTypes, 'allow_insecure' => $allowInsecureURL
        ];

        $bodyString = json_encode($body);

        $request = new TurnitinRequest('POST', $url, $this->getSecretKey(), $bodyString);

        return $this->handleJSONRequest($request);
    }

    /**
     * @param string $webhookId
     *
     * @return array
     * @throws \Exception
     */
    public function getWebhookInfo(string $webhookId)
    {
        $url = sprintf('webhooks/%s', $webhookId);
        $request = new TurnitinRequest('GET', $url, $this->getSecretKey());

        return $this->handleJSONRequest($request);
    }

    /**
     * @param string $webhookId
     *
     * @return array
     * @throws \Exception
     */
    public function deleteWebhook(string $webhookId)
    {
        $url = sprintf('webhooks/%s', $webhookId);
        $request = new TurnitinRequest('DELETE', $url, $this->getSecretKey());

        return $this->handleJSONRequest($request);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function listWebhooks()
    {
        $url = sprintf('webhooks');
        $request = new TurnitinRequest('GET', $url, $this->getSecretKey());

        return $this->handleJSONRequest($request);
    }

    /**
     * @param string $webhookId
     * @param array $eventTypes
     * @param string $description
     *
     * @return array
     * @throws \Exception
     */
    public function updateWebhook(string $webhookId, array $eventTypes, string $description = '')
    {
        $url = sprintf('webhooks/%s', $webhookId);

        $body = ['description' => $description, 'event_types' => $eventTypes];
        $bodyString = json_encode($body);

        $request = new TurnitinRequest('PATCH', $url, $this->getSecretKey(), $bodyString);

        return $this->handleJSONRequest($request);
    }

    /**
     * @return bool
     */
    public function isValidConfig()
    {
        return $this->turnitinConfig->isValid();
    }

    /**
     * @param \GuzzleHttp\Psr7\Request $request
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function handleJSONRequest(Request $request)
    {
        $content = $this->handleRequest($request);
        return json_decode($content, true);
    }

    /**
     * @param \GuzzleHttp\Psr7\Request $request
     *
     * @return string
     * @throws \Chamilo\Application\Plagiarism\Domain\Turnitin\Exception\MalformedRequestException
     * @throws \Chamilo\Application\Plagiarism\Domain\Turnitin\Exception\NotAuthenticatedException
     * @throws \Chamilo\Application\Plagiarism\Domain\Turnitin\Exception\NotFoundException
     * @throws \Chamilo\Application\Plagiarism\Domain\Turnitin\Exception\RateLimitException
     * @throws \Chamilo\Application\Plagiarism\Domain\Turnitin\Exception\UnexpectedErrorException
     */
    protected function handleRequest(Request $request): string
    {
        if (!$this->client instanceof Client)
        {
            $this->initializeClient();
        }

        $response = $this->client->send($request);

        switch ($response->getStatusCode())
        {
            case 400:
                throw new MalformedRequestException();
            case 403:
                throw new NotAuthenticatedException();
            case 404:
                throw new NotFoundException();
            case 429:
                throw new RateLimitException();
            case 500:
            case 409:
                throw new UnexpectedErrorException();
        }

        $content = $response->getBody()->getContents();

        return $content;
    }

    protected function initializeClient()
    {
        $this->client = new Client(['base_uri' => $this->turnitinConfig->getApiUrl()]);
    }

    /**
     * @return string
     */
    protected function getSecretKey()
    {
        return $this->turnitinConfig->getSecretKey();
    }


}