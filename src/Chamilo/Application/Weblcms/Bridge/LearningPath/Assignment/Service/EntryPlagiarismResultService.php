<?php

namespace Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult;

/**
 *
 * @package Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EntryPlagiarismResultService extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Extension\Plagiarism\Bridge\Service\EntryPlagiarismResultService
{
    /**
     * @var \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\Repository\EntryPlagiarismResultRepository
     */
    protected $entryPlagiarismResultRepository;

    /**
     * EntryPlagiarismResultService constructor.
     *
     * @param \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\Repository\EntryPlagiarismResultRepository $entryPlagiarismResultRepository
     */
    public function __construct(
        \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\Repository\EntryPlagiarismResultRepository $entryPlagiarismResultRepository
    )
    {
        parent::__construct($entryPlagiarismResultRepository);
    }

    /**
     * @return EntryPlagiarismResult
     */
    protected function createEntryPlagiarismResultInstance()
    {
        return new \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\EntryPlagiarismResult();
    }
}