<?php

namespace Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Domain\AssignmentConfiguration;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;

/**
 * Interface EntryPlagiarismResultServiceBridge
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Interfaces
 */
class EntryPlagiarismResultServiceBridge implements EntryPlagiarismResultServiceBridgeInterface
{
    /**
     * @var \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\EntryPlagiarismResultService
     */
    protected $assignmentEntryPlagiarismResultService;

    /**
     * EntryPlagiarismResultServiceBridge constructor.
     *
     * @param \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\EntryPlagiarismResultService $assignmentEntryPlagiarismResultService
     */
    public function __construct(
        \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Service\EntryPlagiarismResultService $assignmentEntryPlagiarismResultService
    )
    {
        $this->assignmentEntryPlagiarismResultService = $assignmentEntryPlagiarismResultService;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     *
     * @return bool
     */
    public function checkForPlagiarismAfterSubmission(TreeNode $treeNode)
    {
        /** @var AssignmentConfiguration $configuration */
        $configuration = $treeNode->getConfiguration(new AssignmentConfiguration());
        return $configuration->getCheckForPlagiarism();
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
}