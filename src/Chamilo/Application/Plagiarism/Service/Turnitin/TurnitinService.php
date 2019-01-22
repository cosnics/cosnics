<?php

namespace Chamilo\Application\Plagiarism\Service\Turnitin;

use Chamilo\Application\Plagiarism\Domain\Turnitin\SimilarityReportSettings;
use Chamilo\Application\Plagiarism\Domain\Turnitin\ViewerLaunchSettings;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Application\Plagiarism\Service\Turnitin
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TurnitinService
{
    /**
     * @var \Chamilo\Application\Plagiarism\Repository\Turnitin\TurnitinRepository
     */
    protected $turnitinRepository;

    /**
     * @var \Chamilo\Application\Plagiarism\Service\Turnitin\UserConverterInterface
     */
    protected $userConverter;

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
     * @throws \Exception
     */
    public function uploadFile(
        User $submitter, User $owner, string $title, string $filePath, string $filename,
        bool $extractTextOnly = false, array $metadata = [], array $eula = []
    )
    {
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

    /**
     * @param string $submissionId
     * @param \Chamilo\Application\Plagiarism\Domain\Turnitin\SimilarityReportSettings $similarityReportSettings
     *
     * @throws \Exception
     */
    public function generateSimilarityReport(string $submissionId, SimilarityReportSettings $similarityReportSettings)
    {
        if (!$similarityReportSettings->isValid())
        {
            throw new \InvalidArgumentException('The given similarity report settings are not valid');
        }

        $this->turnitinRepository->generateSimilarityReport($submissionId, $similarityReportSettings);
    }

    /**
     * @param string $submissionId
     * @param \Chamilo\Core\User\Storage\DataClass\User $viewUser
     * @param \Chamilo\Application\Plagiarism\Domain\Turnitin\ViewerLaunchSettings $viewerLaunchSettings
     */
    public function createViewerLaunchURL(
        string $submissionId, User $viewUser, ViewerLaunchSettings $viewerLaunchSettings
    )
    {

    }

}