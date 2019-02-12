<?php

namespace Chamilo\Application\Weblcms\Bridge\Assignment\Service;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 *
 * @package Chamilo\Application\Weblcms\Bridge\Assignment\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EntryPlagiarismResultService extends \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Service\EntryPlagiarismResultService
{
    /**
     * @var \Chamilo\Application\Weblcms\Bridge\Assignment\Storage\Repository\EntryPlagiarismResultRepository
     */
    protected $entryPlagiarismResultRepository;

    /**
     * EntryPlagiarismResultService constructor.
     *
     * @param \Chamilo\Application\Weblcms\Bridge\Assignment\Storage\Repository\EntryPlagiarismResultRepository $entryPlagiarismResultRepository
     */
    public function __construct(
        \Chamilo\Application\Weblcms\Bridge\Assignment\Storage\Repository\EntryPlagiarismResultRepository $entryPlagiarismResultRepository
    )
    {
        parent::__construct($entryPlagiarismResultRepository);
    }

    /**
     * @return EntryPlagiarismResult
     */
    protected function createEntryPlagiarismResultInstance()
    {
        return new \Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\EntryPlagiarismResult();
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     * @param null $offset
     * @param null $count
     * @param array $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findUserEntriesWithPlagiarismResult(
        ContentObjectPublication $contentObjectPublication, Condition $condition = null, $offset = null, $count = null,
        $orderProperty = []
    )
    {
        return $this->entryPlagiarismResultRepository->findUserEntriesWithPlagiarismResult(
            $contentObjectPublication, $condition, $offset, $count, $orderProperty
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return int
     */
    public function countUserEntriesWithPlagiarismResult(
        ContentObjectPublication $contentObjectPublication, Condition $condition = null
    )
    {
        return $this->entryPlagiarismResultRepository->countUserEntriesWithPlagiarismResult(
            $contentObjectPublication, $condition
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     * @param null $offset
     * @param null $count
     * @param array $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findCourseGroupEntriesWithPlagiarismResult(
        ContentObjectPublication $contentObjectPublication, Condition $condition = null, $offset = null, $count = null,
        $orderProperty = []
    )
    {
        return $this->entryPlagiarismResultRepository->findCourseGroupEntriesWithPlagiarismResult(
            $contentObjectPublication, $condition, $offset, $count, $orderProperty
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return int
     */
    public function countCourseGroupEntriesWithPlagiarismResult(
        ContentObjectPublication $contentObjectPublication, Condition $condition = null
    )
    {
        return $this->entryPlagiarismResultRepository->countCourseGroupEntriesWithPlagiarismResult(
            $contentObjectPublication, $condition
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     * @param null $offset
     * @param null $count
     * @param array $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findPlatformGroupEntriesWithPlagiarismResult(
        ContentObjectPublication $contentObjectPublication, Condition $condition = null, $offset = null, $count = null,
        $orderProperty = []
    )
    {
        return $this->entryPlagiarismResultRepository->findPlatformGroupEntriesWithPlagiarismResult(
            $contentObjectPublication, $condition, $offset, $count, $orderProperty
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return int
     */
    public function countPlatformGroupEntriesWithPlagiarismResult(
        ContentObjectPublication $contentObjectPublication, Condition $condition = null
    )
    {
        return $this->entryPlagiarismResultRepository->countPlatformGroupEntriesWithPlagiarismResult(
            $contentObjectPublication, $condition
        );
    }
}