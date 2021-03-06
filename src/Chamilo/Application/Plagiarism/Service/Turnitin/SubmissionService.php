<?php

namespace Chamilo\Application\Plagiarism\Service\Turnitin;

use Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException;
use Chamilo\Application\Plagiarism\Domain\Turnitin\Exception\EulaNotAcceptedException;
use Chamilo\Application\Plagiarism\Domain\Turnitin\Exception\InvalidConfigurationException;
use Chamilo\Application\Plagiarism\Domain\Turnitin\Exception\InvalidFileException;
use Chamilo\Application\Plagiarism\Domain\Turnitin\SimilarityReportSettings;
use Chamilo\Application\Plagiarism\Domain\SubmissionStatus;
use Chamilo\Application\Plagiarism\Domain\Turnitin\ViewerLaunchSettings;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Utilities\StringUtilities;
use Spipu\Html2Pdf\Tag\Sub;
use function filesize;

/**
 * @package Chamilo\Application\Plagiarism\Service\Turnitin
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class SubmissionService
{
    // 100 MB
    const MAX_ALLOWED_FILE_SIZE = 100 * 1024 * 1024;

    /**
     * @var \Chamilo\Application\Plagiarism\Repository\Turnitin\TurnitinRepository
     */
    protected $turnitinRepository;

    /**
     * @var \Chamilo\Application\Plagiarism\Service\UserConverter\UserConverterInterface
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
     * @var \Chamilo\Application\Plagiarism\Service\Turnitin\SubmissionStatusParser
     */
    protected $submissionStatusParser;

    /**
     * TurnitinService constructor.
     *
     * @param \Chamilo\Application\Plagiarism\Repository\Turnitin\TurnitinRepository $turnitinRepository
     * @param \Chamilo\Application\Plagiarism\Service\UserConverter\UserConverterInterface $userConverter
     * @param \Chamilo\Application\Plagiarism\Service\Turnitin\EulaService $eulaService
     * @param \Chamilo\Application\Plagiarism\Service\Turnitin\WebhookManager $webhookManager
     * @param \Chamilo\Application\Plagiarism\Service\Turnitin\SubmissionStatusParser $submissionStatusParser
     */
    public function __construct(
        \Chamilo\Application\Plagiarism\Repository\Turnitin\TurnitinRepository $turnitinRepository,
        \Chamilo\Application\Plagiarism\Service\UserConverter\UserConverterInterface $userConverter,
        EulaService $eulaService, WebhookManager $webhookManager, SubmissionStatusParser $submissionStatusParser
    )
    {
        $this->turnitinRepository = $turnitinRepository;
        $this->userConverter = $userConverter;
        $this->eulaService = $eulaService;
        $this->webhookManager = $webhookManager;
        $this->submissionStatusParser = $submissionStatusParser;
    }

    /**
     * Checks whether or not a file can be uploaded
     *   File must exist
     *   Filename must be within a list of valid extensions
     *
     * @param string $filePath
     * @param string $filename
     *
     * @return bool
     */
    public function canUploadFile(string $filePath, string $filename)
    {
        if (!$this->isPlagiarismCheckerActive())
        {
            return false;
        }

        if (!file_exists($filePath))
        {
            return false;
        }

        $fileParts = explode('.', $filename);
        $extension = array_pop($fileParts);
        if (!in_array($extension, $this->getAllowedFileExtensions()))
        {
            return false;
        }

        $fileSize = filesize($filePath);
        if($fileSize > self::MAX_ALLOWED_FILE_SIZE)
        {
            return false;
        }

        return true;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $submitter
     * @param \Chamilo\Core\User\Storage\DataClass\User $owner
     * @param string $title
     * @param string $filePath
     * @param string $filename
     *
     * @param bool $extractTextOnly
     *
     * @return string
     *
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     */
    public function uploadFile(
        User $submitter, User $owner, string $title, string $filePath, string $filename,
        bool $extractTextOnly = false
    )
    {
        try
        {
            $submissionId = $this->createSubmission($submitter, $owner, $title, $extractTextOnly);
            $filename = urlencode($filename); //StringUtilities::getInstance()->createString($filename)->toAscii();
            $this->uploadFileForSubmission($submissionId, $filePath, $filename);

            return $submissionId;
        }
        catch (\Exception $ex)
        {
            $this->handleException($ex);
        }

        return null;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $submitter
     * @param \Chamilo\Core\User\Storage\DataClass\User $owner
     * @param string $title
     * @param bool $extractTextOnly
     *
     * @return null
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     */
    public function createSubmission(User $submitter, User $owner, string $title, bool $extractTextOnly = false)
    {
        try
        {
            if (!$this->isPlagiarismCheckerActive())
            {
                throw new InvalidConfigurationException();
            }

            if (!$this->eulaService->userHasAcceptedEULA($submitter))
            {
                throw new EulaNotAcceptedException();
            }

            $submitterId = $this->userConverter->convertUserToId($submitter);
            $ownerId = $this->userConverter->convertUserToId($owner);

            $metadata = [
                'owners' =>
                    [
                        [
                            'id' => $ownerId,
                            'email' => $owner->get_email(),
                            'family_name' => $owner->get_lastname(),
                            'given_name' => $owner->get_firstname()
                        ]
                    ]
            ];

            $createSubmissionResponse = $this->turnitinRepository->createSubmission(
                $submitterId, $ownerId, $title, $extractTextOnly, $metadata
            );

            $submissionId = $createSubmissionResponse['id'];

            return $submissionId;
        }
        catch (\Exception $ex)
        {
            $this->handleException($ex);
        }

        return null;
    }

    /**
     * @param string $submissionId
     * @param string $filePath
     * @param string $filename
     *
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     */
    public function uploadFileForSubmission(string $submissionId, string $filePath, string $filename)
    {
        try
        {
            if (!$this->isPlagiarismCheckerActive())
            {
                throw new InvalidConfigurationException();
            }

            if (!$this->canUploadFile($filePath, $filename))
            {
                throw new InvalidFileException($filePath, $filename);
            }

            $this->turnitinRepository->uploadSubmissionFile($submissionId, $filename, fopen($filePath, 'r'));
        }
        catch (\Exception $ex)
        {
            $this->handleException($ex);
        }
    }

    /**
     * @param string $submissionId
     *
     * @return \Chamilo\Application\Plagiarism\Domain\SubmissionStatus
     * @throws \Exception
     */
    public function getUploadStatus(string $submissionId)
    {
        $submissionInfo = $this->turnitinRepository->getSubmissionInfo($submissionId);
        if (empty($submissionInfo))
        {
            throw new PlagiarismException(sprintf('Could not retrieve the submission info for id %s', $submissionId));
        }

        return $this->submissionStatusParser->parse(SubmissionStatusParser::SUBMISSION_STATUS_UPLOAD, $submissionInfo);
    }

    /**
     * @param string $submissionId
     * @param bool $hardDelete
     *
     * @throws \Exception
     */
    public function deleteSubmission(string $submissionId, bool $hardDelete = false)
    {
        $this->turnitinRepository->deleteSubmission($submissionId, $hardDelete);
    }

    /**
     * @param string $submissionId
     * @param \Chamilo\Application\Plagiarism\Domain\Turnitin\SimilarityReportSettings $similarityReportSettings
     *
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     */
    public function generateSimilarityReport(string $submissionId, SimilarityReportSettings $similarityReportSettings)
    {
        try
        {
            if (!$this->isPlagiarismCheckerActive())
            {
                throw new InvalidConfigurationException();
            }

            if (!$similarityReportSettings->isValid())
            {
                throw new \InvalidArgumentException('The given similarity report settings are not valid');
            }

            $this->turnitinRepository->generateSimilarityReport($submissionId, $similarityReportSettings);
        }
        catch (\Exception $ex)
        {
            $this->handleException($ex);
        }
    }

    /**
     * @param string $submissionId
     *
     * @return \Chamilo\Application\Plagiarism\Domain\SubmissionStatus
     * @throws \Exception
     */
    public function getSimilarityReportStatus(string $submissionId)
    {
        $similarityReportInfo = $this->turnitinRepository->getSimilarityReportInfo($submissionId);
        if (empty($similarityReportInfo))
        {
            throw new PlagiarismException(
                sprintf('Could not retrieve the similarity report info for submission  %s', $submissionId)
            );
        }

        return $this->submissionStatusParser->parse(
            SubmissionStatusParser::SUBMISSION_STATUS_REPORT_GENERATION, $similarityReportInfo
        );
    }

    /**
     * @param string $submissionId
     * @param \Chamilo\Core\User\Storage\DataClass\User $viewUser
     * @param \Chamilo\Application\Plagiarism\Domain\Turnitin\ViewerLaunchSettings $viewerLaunchSettings
     *
     * @return string
     *
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     */
    public function createViewerLaunchURL(
        string $submissionId, User $viewUser, ViewerLaunchSettings $viewerLaunchSettings
    )
    {
        try
        {
            if (!$this->isPlagiarismCheckerActive())
            {
                throw new InvalidConfigurationException();
            }

            if (!$this->eulaService->userHasAcceptedEULA($viewUser))
            {
                throw new EulaNotAcceptedException();
            }

            if (!$viewerLaunchSettings->isValid())
            {
                throw new \InvalidArgumentException('The given viewer launcher settings are not valid');
            }

            $response = $this->turnitinRepository->createViewerLaunchURL(
                $submissionId, $this->userConverter->convertUserToId($viewUser), $viewerLaunchSettings
            );

            return $response['viewer_url'];
        }
        catch (\Exception $ex)
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
     * @param string $redirectToURL
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     */
    public function getRedirectToEULAPageResponse(string $redirectToURL)
    {
        return $this->eulaService->getRedirectToEULAPageResponse($redirectToURL);
    }

    /**
     * @param \Exception $ex
     *
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     */
    protected function handleException(\Exception $ex)
    {
        if ($ex instanceof PlagiarismException)
        {
            throw $ex;
        }

        throw new PlagiarismException($ex->getMessage(), 0, $ex);
    }

    /**
     * @return array
     */
    protected function getAllowedFileExtensions()
    {
        return ['doc', 'txt', 'rtf', 'sxw', 'odt', 'pdf', 'html', 'htm', 'docx', 'wpd'];
    }

}
