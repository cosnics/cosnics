<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Preview\Bridge;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Table\EntryPlagiarismResultTableParameters;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Storage\Parameters\FilterParameters;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Preview\Bridge
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EntryPlagiarismResultServiceBridge implements EntryPlagiarismResultServiceBridgeInterface
{

    /**
     * @return bool
     */
    public function checkForPlagiarismAfterSubmission()
    {
        return false;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return EntryPlagiarismResult|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findEntryPlagiarismResultByEntry(Entry $entry)
    {
        // TODO: Implement findEntryPlagiarismResultByEntry() method.
    }

    /**
     * @param string $externalId
     *
     * @return EntryPlagiarismResult|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findEntryPlagiarismResultByExternalId(string $externalId)
    {
        // TODO: Implement findEntryPlagiarismResultByExternalId() method.
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param string $externalId
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult
     */
    public function createEntryPlagiarismResultForEntry(Entry $entry, string $externalId)
    {
        // TODO: Implement createEntryPlagiarismResultForEntry() method.
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult $entryPlagiarismResult
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function updateEntryPlagiarismResult(EntryPlagiarismResult $entryPlagiarismResult)
    {
        // TODO: Implement updateEntryPlagiarismResult() method.
    }

    /**
     * @param int $entityType
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass[]|\Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesWithPlagiarismResult(int $entityType, FilterParameters $filterParameters)
    {
        return [];
    }

    /**
     * @param int $entityType
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     *
     * @return int
     */
    public function countEntriesWithPlagiarismResult(int $entityType, FilterParameters $filterParameters)
    {
        return 0;
    }

    /**
     * @param int $entityType
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Table\EntryPlagiarismResultTableParameters $entryPlagiarismResultTableParameters
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Table\EntryPlagiarismResultTable
     */
    public function getEntryPlagiarismResultTable(
        int $entityType, Application $application,
        EntryPlagiarismResultTableParameters $entryPlagiarismResultTableParameters
    )
    {
        // TODO: Implement getEntryPlagiarismResultTable() method.
    }

    /**
     * @return bool
     */
    public function isPlagiarismEnabled()
    {
        return false;
    }
}