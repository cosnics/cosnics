<?php

namespace Chamilo\Application\Plagiarism\Service\Turnitin;

use Chamilo\Application\Plagiarism\Domain\Turnitin\Exception\EulaNotAcceptedException;
use Chamilo\Application\Plagiarism\Domain\Turnitin\Exception\InvalidConfigurationException;
use Chamilo\Application\Plagiarism\Domain\Turnitin\SimilarityReportSettings;
use Chamilo\Application\Plagiarism\Domain\Turnitin\ViewerLaunchSettings;
use Chamilo\Application\Plagiarism\PlagiarismException;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Application\Plagiarism\Service\Turnitin
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 *
 * TODO: CHECK FOR VALID FILES BEFORE UPLOADING
 */
class PlagiarismChecker
{
    /**
     * @var \Chamilo\Application\Plagiarism\Repository\Turnitin\TurnitinRepository
     */
    protected $turnitinRepository;

    /**
     * @var \Chamilo\Application\Plagiarism\Service\Turnitin\UserConverter\UserConverterInterface
     */
    protected $userConverter;

    /**
     * @var \Chamilo\Application\Plagiarism\Service\Turnitin\EulaService
     */
    protected $eulaService;

    /**
     * @var \Chamilo\Application\Plagiarism\Service\Turnitin\WebhookManager
     */
    protected $webhookManager;

    /**
     * TurnitinService constructor.
     *
     * @param \Chamilo\Application\Plagiarism\Repository\Turnitin\TurnitinRepository $turnitinRepository
     * @param \Chamilo\Application\Plagiarism\Service\Turnitin\UserConverter\UserConverterInterface $userConverter
     * @param \Chamilo\Application\Plagiarism\Service\Turnitin\EulaService $eulaService
     * @param \Chamilo\Application\Plagiarism\Service\Turnitin\WebhookManager $webhookManager
     */
    public function __construct(
        \Chamilo\Application\Plagiarism\Repository\Turnitin\TurnitinRepository $turnitinRepository,
        \Chamilo\Application\Plagiarism\Service\Turnitin\UserConverter\UserConverterInterface $userConverter,
        EulaService $eulaService, WebhookManager $webhookManager
    )
    {
        $this->turnitinRepository = $turnitinRepository;
        $this->userConverter = $userConverter;
        $this->eulaService = $eulaService;
        $this->webhookManager = $webhookManager;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $submitter
     * @param \Chamilo\Core\User\Storage\DataClass\User $owner
     * @param string $title
     * @param string $filePath
     * @param string $filename
     *
     * @param bool $extractTextOnly
     * @param array $metadata
     * @param array $eula
     *
     * @throws \Chamilo\Application\Plagiarism\PlagiarismException
     */
    public function uploadFile(
        User $submitter, User $owner, string $title, string $filePath, string $filename,
        bool $extractTextOnly = false, array $metadata = [], array $eula = []
    )
    {
        try
        {
            if(!$this->isPlagiarismCheckerActive())
            {
                throw new InvalidConfigurationException();
            }

            if(!$this->eulaService->userHasAcceptedEULA($submitter))
            {
                throw new EulaNotAcceptedException();
            }

            if (!file_exists($filePath))
            {
                throw new \InvalidArgumentException(sprintf('The given file with path %s does not exist', $filePath));
            }

            $submitterId = $this->userConverter->convertUserToId($submitter);
            $ownerId = $this->userConverter->convertUserToId($owner);

            $createSubmissionResponse = $this->turnitinRepository->createSubmission(
                $submitterId, $ownerId, $title, $extractTextOnly, $metadata, $eula
            );

            $submissionId = $createSubmissionResponse['id'];

            $this->turnitinRepository->uploadSubmissionFile($submissionId, $filename, fopen($filePath, 'r'));
        }
        catch(\Exception $ex)
        {
            $this->handleException($ex);
        }
    }

    /**
     * @param string $submissionId
     * @param \Chamilo\Application\Plagiarism\Domain\Turnitin\SimilarityReportSettings $similarityReportSettings
     *
     * @throws \Chamilo\Application\Plagiarism\PlagiarismException
     */
    public function generateSimilarityReport(string $submissionId, SimilarityReportSettings $similarityReportSettings)
    {
        try
        {
            if(!$this->isPlagiarismCheckerActive())
            {
                throw new InvalidConfigurationException();
            }

            if (!$similarityReportSettings->isValid())
            {
                throw new \InvalidArgumentException('The given similarity report settings are not valid');
            }

            $this->turnitinRepository->generateSimilarityReport($submissionId, $similarityReportSettings);
        }
        catch(\Exception $ex)
        {
            $this->handleException($ex);
        }

    }

    /**
     * @param string $submissionId
     * @param \Chamilo\Core\User\Storage\DataClass\User $viewUser
     * @param \Chamilo\Application\Plagiarism\Domain\Turnitin\ViewerLaunchSettings $viewerLaunchSettings
     *
     * @throws \Chamilo\Application\Plagiarism\PlagiarismException
     */
    public function createViewerLaunchURL(
        string $submissionId, User $viewUser, ViewerLaunchSettings $viewerLaunchSettings
    )
    {
        try
        {
            if(!$this->isPlagiarismCheckerActive())
            {
                throw new InvalidConfigurationException();
            }

            if(!$this->eulaService->userHasAcceptedEULA($viewUser))
            {
                throw new EulaNotAcceptedException();
            }

            if (!$viewerLaunchSettings->isValid())
            {
                throw new \InvalidArgumentException('The given viewer launcher settings are not valid');
            }

            $this->turnitinRepository->createViewerLaunchURL(
                $submissionId, $this->userConverter->convertUserToId($viewUser), $viewerLaunchSettings
            );
        }
        catch(\Exception $ex)
        {
            $this->handleException($ex);
        }

    }

    /**
     * @return bool
     */
    public function isPlagiarismCheckerActive()
    {
        return $this->webhookManager->isWebhookRegistered() && $this->turnitinRepository->isValidConfig();
    }

    /**
     * @param \Exception $ex
     *
     * @throws \Chamilo\Application\Plagiarism\PlagiarismException
     */
    protected function handleException(\Exception $ex)
    {
        if ($ex instanceof PlagiarismException)
        {
            throw $ex;
        }

        throw new PlagiarismException($ex->getMessage());
    }

}