<?php

namespace Chamilo\Application\Plagiarism\Service;

/**
 * @package Chamilo\Application\Plagiarism\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PlagiarismCheckerFactory
{
    protected \Chamilo\Application\Plagiarism\Service\Turnitin\PlagiarismChecker $turnitinPlagiarismChecker;
    protected \Chamilo\Application\Plagiarism\Service\StrikePlagiarism\PlagiarismChecker $strikePlagiarismChecker;

    public function __construct(Turnitin\PlagiarismChecker $turnitinPlagiarismChecker, StrikePlagiarism\PlagiarismChecker $strikePlagiarismChecker)
    {
        $this->turnitinPlagiarismChecker = $turnitinPlagiarismChecker;
        $this->strikePlagiarismChecker = $strikePlagiarismChecker;
    }

    public function createPlagiarismChecker()
    {
        return $this->turnitinPlagiarismChecker;

        //return $this->strikePlagiarismChecker;
    }
}