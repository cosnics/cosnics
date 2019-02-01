<?php

namespace Chamilo\Application\Plagiarism\Service;

use Chamilo\Application\Plagiarism\Domain\SubmissionStatus;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Application\Plagiarism\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface PlagiarismCheckerInterface
{
    /**
     * @param string $filePath
     * @param string $filename
     *
     * @return bool
     */
    public function canCheckForPlagiarism(string $filePath, string $filename);

    /**
     * @param \Chamilo\Application\Plagiarism\Domain\SubmissionStatus $currentSubmissionStatus
     * @param \Chamilo\Core\User\Storage\DataClass\User $owner
     * @param \Chamilo\Core\User\Storage\DataClass\User $submitter
     * @param string $title
     * @param string $filePath
     * @param string $filename
     *
     * @return \Chamilo\Application\Plagiarism\Domain\SubmissionStatus
     *
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     */
    public function checkForPlagiarism(
        User $owner, User $submitter, string $title, string $filePath,
        string $filename, SubmissionStatus $currentSubmissionStatus = null
    );

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $submissionId
     *
     * @return string
     */
    public function getReportUrlForSubmission(User $user, string $submissionId);

    /**
     * @param string $redirectToURL
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     */
    public function getRedirectToEULAPageResponse(string $redirectToURL);
}