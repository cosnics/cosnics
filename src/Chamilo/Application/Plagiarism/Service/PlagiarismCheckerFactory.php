<?php

namespace Chamilo\Application\Plagiarism\Service;

/**
 * @package Chamilo\Application\Plagiarism\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PlagiarismCheckerFactory
{
    /**
     * @var \Chamilo\Application\Plagiarism\Service\Turnitin\PlagiarismChecker
     */
    protected $turnitinPlagiarismChecker;

    /**
     * PlagiarismCheckerFactory constructor.
     *
     * @param \Chamilo\Application\Plagiarism\Service\Turnitin\PlagiarismChecker $turnitinPlagiarismChecker
     */
    public function __construct(
        \Chamilo\Application\Plagiarism\Service\Turnitin\PlagiarismChecker $turnitinPlagiarismChecker
    )
    {
        $this->turnitinPlagiarismChecker = $turnitinPlagiarismChecker;
    }

    /**
     * @return \Chamilo\Application\Plagiarism\Service\PlagiarismCheckerInterface
     */
    public function createPlagiarismChecker()
    {
        return $this->turnitinPlagiarismChecker;
    }
}