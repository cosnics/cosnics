<?php

namespace Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\Plagiarism;

/**
 * @package Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\Plagiarism
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PlagiarismEventListener extends \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Service\PlagiarismEventListener
{
    /**
     * @var \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\EntryPlagiarismResultService
     */
    protected $entryPlagiarismResultService;

    /**
     * PlagiarismEventListener constructor.
     *
     * @param \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\EntryPlagiarismResultService $entryPlagiarismResultService
     */
    public function __construct(
        \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\EntryPlagiarismResultService $entryPlagiarismResultService
    )
    {
        parent::__construct($entryPlagiarismResultService);
    }

}