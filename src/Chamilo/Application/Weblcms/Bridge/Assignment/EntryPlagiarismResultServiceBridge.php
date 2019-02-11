<?php

namespace Chamilo\Application\Weblcms\Bridge\Assignment;

use Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity\EntityServiceManager;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataClass\Publication;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Table\EntryPlagiarismResultTableParameters;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * Interface EntryPlagiarismResultServiceBridge
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Interfaces
 */
class EntryPlagiarismResultServiceBridge implements
    \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface
{
    /**
     * @var \Chamilo\Application\Weblcms\Bridge\Assignment\Service\EntryPlagiarismResultService
     */
    protected $assignmentEntryPlagiarismResultService;

    /**
     * @var \Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity\EntityServiceManager
     */
    protected $entityServiceManager;

    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataClass\Publication
     */
    protected $assignmentPublication;

    /**
     * @var ContentObjectPublication
     */
    protected $contentObjectPublication;

    /**
     * EntryPlagiarismResultServiceBridge constructor.
     *
     * @param \Chamilo\Application\Weblcms\Bridge\Assignment\Service\EntryPlagiarismResultService $assignmentEntryPlagiarismResultService
     * @param \Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity\EntityServiceManager $entityServiceManager
     */
    public function __construct(
        \Chamilo\Application\Weblcms\Bridge\Assignment\Service\EntryPlagiarismResultService $assignmentEntryPlagiarismResultService,
        EntityServiceManager $entityServiceManager
    )
    {
        $this->assignmentEntryPlagiarismResultService = $assignmentEntryPlagiarismResultService;
        $this->entityServiceManager = $entityServiceManager;
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     */
    public function setContentObjectPublication(ContentObjectPublication $contentObjectPublication)
    {
        if (!$contentObjectPublication->getContentObject() instanceof Assignment)
        {
            throw new \RuntimeException(
                'The given treenode does not reference a valid assignment and should not be used'
            );
        }

        $this->contentObjectPublication = $contentObjectPublication;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataClass\Publication $assignmentPublication
     */
    public function setAssignmentPublication(Publication $assignmentPublication)
    {
        if (!isset($this->contentObjectPublication) ||
            $this->contentObjectPublication->getId() != $assignmentPublication->getPublicationId())
        {
            throw new \RuntimeException(
                'The given assignment publication does not belong to the given content object publication'
            );
        }

        $this->assignmentPublication = $assignmentPublication;
    }

    /**
     * @return bool
     */
    public function checkForPlagiarismAfterSubmission()
    {
        return $this->assignmentPublication->getCheckForPlagiarism();
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return EntryPlagiarismResult|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findEntryPlagiarismResultByEntry(Entry $entry)
    {
        return $this->assignmentEntryPlagiarismResultService->findEntryPlagiarismResultByEntry($entry);
    }

    /**
     * @param string $externalId
     *
     * @return EntryPlagiarismResult|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findEntryPlagiarismResultByExternalId(string $externalId)
    {
        return $this->assignmentEntryPlagiarismResultService->findEntryPlagiarismResultByExternalId($externalId);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param string $externalId
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult
     */
    public function createEntryPlagiarismResultForEntry(Entry $entry, string $externalId)
    {
        return $this->assignmentEntryPlagiarismResultService->createEntryPlagiarismResultForEntry(
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
        return $this->assignmentEntryPlagiarismResultService->updateEntryPlagiarismResult($entryPlagiarismResult);
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
        $entityService = $this->entityServiceManager->getEntityServiceByType($entityType);

        return $entityService->findEntriesWithPlagiarismResult(
            $this->contentObjectPublication, $condition, $offset, $count, $order_property
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
        $entityService = $this->entityServiceManager->getEntityServiceByType($entityType);

        return $entityService->countEntriesWithPlagiarismResult($this->contentObjectPublication, $condition);
    }

    /**
     * @param int $entityType
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Table\EntryPlagiarismResultTableParameters $entryPlagiarismResultTableParameters
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Table\EntryPlagiarismResultTable
     */
    public function getEntryPlagiarismResultTable(
        int $entityType, Application $application, EntryPlagiarismResultTableParameters $entryPlagiarismResultTableParameters
    )
    {
        $entityService = $this->entityServiceManager->getEntityServiceByType($entityType);
        return $entityService->getEntryPlagiarismResultTable($application, $entryPlagiarismResultTableParameters);
    }

}