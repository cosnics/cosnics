<?php

namespace Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Libraries\Storage\Parameters\FilterParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 *
 * @package Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EntryPlagiarismResultService extends \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Service\EntryPlagiarismResultService
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

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findUserEntriesWithPlagiarismResult(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, FilterParameters $filterParameters
    )
    {
        return $this->entryPlagiarismResultRepository->findUserEntriesWithPlagiarismResult(
            $contentObjectPublication, $treeNodeData, $filterParameters
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     *
     * @return int
     */
    public function countUserEntriesWithPlagiarismResult(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, FilterParameters $filterParameters
    )
    {
        return $this->entryPlagiarismResultRepository->countUserEntriesWithPlagiarismResult(
            $contentObjectPublication, $treeNodeData, $filterParameters
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findCourseGroupEntriesWithPlagiarismResult(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, FilterParameters $filterParameters
    )
    {
        return $this->entryPlagiarismResultRepository->findCourseGroupEntriesWithPlagiarismResult(
            $contentObjectPublication, $treeNodeData, $filterParameters
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     *
     * @return int
     */
    public function countCourseGroupEntriesWithPlagiarismResult(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, FilterParameters $filterParameters
    )
    {
        return $this->entryPlagiarismResultRepository->countCourseGroupEntriesWithPlagiarismResult(
            $contentObjectPublication, $treeNodeData, $filterParameters
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findPlatformGroupEntriesWithPlagiarismResult(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, FilterParameters $filterParameters
    )
    {
        return $this->entryPlagiarismResultRepository->findPlatformGroupEntriesWithPlagiarismResult(
            $contentObjectPublication, $treeNodeData, $filterParameters
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     *
     * @return int
     */
    public function countPlatformGroupEntriesWithPlagiarismResult(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, FilterParameters $filterParameters
    )
    {
        return $this->entryPlagiarismResultRepository->countPlatformGroupEntriesWithPlagiarismResult(
            $contentObjectPublication, $treeNodeData, $filterParameters
        );
    }
}