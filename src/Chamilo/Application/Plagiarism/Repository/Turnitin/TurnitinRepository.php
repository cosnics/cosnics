<?php

namespace Chamilo\Application\Plagiarism\Repository\Turnitin;

use Chamilo\Application\Plagiarism\Domain\Turnitin\Exception\MalformedRequestException;
use Chamilo\Application\Plagiarism\Domain\Turnitin\Exception\NotAuthenticatedException;
use Chamilo\Application\Plagiarism\Domain\Turnitin\Exception\NotFoundException;
use Chamilo\Application\Plagiarism\Domain\Turnitin\Exception\RateLimitException;
use Chamilo\Application\Plagiarism\Domain\Turnitin\Exception\UnexpectedErrorException;
use Chamilo\Application\Plagiarism\Domain\Turnitin\TurnitinConfig;
use Chamilo\Application\Plagiarism\Domain\Turnitin\TurnitinRequest;
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
     * @return string
     * @throws \Exception
     */
    public function getEnabledFeatures()
    {
        $request = new TurnitinRequest('GET', '/features-enabled', $this->getSecretKey());

        return $this->handleRequest($request);
    }

    /**
     * @param string $versionId
     *
     * @return string
     * @throws \Exception
     */
    public function getEULAVersionInfo($versionId = 'latest')
    {
        $url = sprintf('/eula/%s', $versionId);
        $request = new TurnitinRequest('GET', $url, $this->getSecretKey());

        return $this->handleRequest($request);
    }

    /**
     * @param string $userId
     * @param \DateTime $acceptedTimestamp
     * @param string $language
     * @param string $versionId
     *
     * @return string
     * @throws \Exception
     */
    public function acceptEULAVersion(
        string $userId, \DateTime $acceptedTimestamp, string $language, $versionId = 'latest'
    )
    {
        $url = sprintf('/eula/%s/accept', $versionId);

        $body = [
            'user_id' => $userId, 'accepted_timestamp' => $acceptedTimestamp->format(\DateTimeInterface::ISO8601),
            'language' => $language
        ];

        $bodyString = json_encode($body);

        $request = new TurnitinRequest('POST', $url, $this->getSecretKey(), $bodyString);

        return $this->handleRequest($request);
    }

    /**
     * @param string $userId
     * @param string $versionId
     *
     * @return string
     * @throws \Exception
     */
    public function getEULAUserAcceptanceInfo(string $userId, $versionId = 'latest')
    {
        $url = sprintf('/eula/%s/accept/%s', $versionId, $userId);
        $request = new TurnitinRequest('GET', $url, $this->getSecretKey());

        return $this->handleRequest($request);
    }

    /**
     * @param string $versionId
     *
     * @return string
     * @throws \Exception
     */
    public function getEULAPage($versionId = 'latest')
    {
        $url = sprintf('/eula/%s/view', $versionId);
        $request = new TurnitinRequest('GET', $url, $this->getSecretKey());

        return $this->handleRequest($request);
    }

    /**
     * @param string $userId
     * @param string $title
     * @param bool $extractTextOnly
     * @param array $metadata
     *
     * @return string
     * @throws \Exception
     */
    public function createSubmission(string $userId, string $title, bool $extractTextOnly = false, $metadata = [])
    {
        $url = sprintf('/submissions');

        $body = [
            'owner' => $userId, 'title' => $title, 'extract_text_only' => $extractTextOnly ? 'true' : 'false',
            'metadata' => $metadata
        ];

        $bodyString = json_encode($body);

        $request = new TurnitinRequest('POST', $url, $this->getSecretKey(), $bodyString);

        return $this->handleRequest($request);
    }

    /**
     * @param string $submissionId
     * @param string $filename
     * @param resource $file
     *
     * @return string
     * @throws \Exception
     */
    public function uploadSubmissionFile(string $submissionId, string $filename, resource $file)
    {
        $url = sprintf('/submissions/%s/original', $submissionId);
        $contentDisposition = sprintf('inline; filename="%s"', $filename);

        $headers = ['Content-Type' => 'binary/octet-stream', 'Content-Disposition' => $contentDisposition];
        $request = new TurnitinRequest('POST', $url, $this->getSecretKey(), $file, $headers);

        return $this->handleRequest($request);
    }



    /**
     * @param \GuzzleHttp\Psr7\Request $request
     *
     * @return string
     * @throws \Exception
     */
    protected function handleRequest(Request $request)
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
                throw new UnexpectedErrorException();
        }

        return $response->getBody()->getContents();
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