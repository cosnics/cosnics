<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Table\EntryPlagiarismResultTableParameters;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * Interface EntryPlagiarismResultServiceBridge
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Interfaces
 */
class EntryPlagiarismResultServiceBridge implements
    \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface
{
    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface
     */
    protected $assignmentEntryPlagiarismResultServiceBridge;

    /**
     * EntryPlagiarismResultServiceBridge constructor.
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface $assignmentEntryPlagiarismResultServiceBridge
     */
    public function __construct(
        \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface $assignmentEntryPlagiarismResultServiceBridge
    )
    {
        $this->assignmentEntryPlagiarismResultServiceBridge = $assignmentEntryPlagiarismResultServiceBridge;
    }

    /**
     * @return bool
     */
    public function checkForPlagiarismAfterSubmission()
    {
        return $this->assignmentEntryPlagiarismResultServiceBridge->checkForPlagiarismAfterSubmission();
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return EntryPlagiarismResult|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findEntryPlagiarismResultByEntry(Entry $entry)
    {
        return $this->assignmentEntryPlagiarismResultServiceBridge->findEntryPlagiarismResultByEntry($entry);
    }

    /**
     * @param string $externalId
     *
     * @return EntryPlagiarismResult|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findEntryPlagiarismResultByExternalId(string $externalId)
    {
        return $this->assignmentEntryPlagiarismResultServiceBridge->findEntryPlagiarismResultByExternalId($externalId);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param string $externalId
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult
     */
    public function createEntryPlagiarismResultForEntry(Entry $entry, string $externalId)
    {
        return $this->assignmentEntryPlagiarismResultServiceBridge->createEntryPlagiarismResultForEntry(
            $entry, $externalId
        );
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult $entryPlagiarismResult
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function updateEntryPlagiarismResult(EntryPlagiarismResult $entryPlagiarismResult)
    {
        return $this->assignmentEntryPlagiarismResultServiceBridge->updateEntryPlagiarismResult($entryPlagiarismResult);
    }

    /**
     * @param int $entityType
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     * @param int|null $offset
     * @param int|null $count
     * @param array $order_property
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass[]|\Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesWithPlagiarismResult(
        int $entityType, Condition $condition = null, int $offset = null, int $count = null, array $order_property = []
    )
    {
        return $this->assignmentEntryPlagiarismResultServiceBridge->findEntriesWithPlagiarismResult(
            $entityType, $condition, $offset, $count, $order_property
        );
    }

    /**
     * @param int $entityType
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return int
     */
    public function countEntriesWithPlagiarismResult(int $entityType, Condition $condition = null)
    {
        return $this->assignmentEntryPlagiarismResultServiceBridge->countEntriesWithPlagiarismResult(
            $entityType, $condition
        );
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
        return $this->assignmentEntryPlagiarismResultServiceBridge->getEntryPlagiarismResultTable(
            $entityType, $application, $entryPlagiarismResultTableParameters
        );
    }
}