<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;

/**
 * Interface EntryPlagiarismResultServiceBridge
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Interfaces
 */
class EntryPlagiarismResultServiceBridge implements
    \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface
{
    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface
     */
    protected $assignmentEntryPlagiarismResultServiceBridge;

    /**
     * @var TreeNode
     */
    protected $treeNode;

    /**
     * EntryPlagiarismResultServiceBridge constructor.
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface $assignmentEntryPlagiarismResultServiceBridge
     */
    public function __construct(
        \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface $assignmentEntryPlagiarismResultServiceBridge
    )
    {
        $this->assignmentEntryPlagiarismResultServiceBridge = $assignmentEntryPlagiarismResultServiceBridge;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     */
    public function setTreeNode(TreeNode $treeNode)
    {
        if (!$treeNode->getContentObject() instanceof Assignment)
        {
            throw new \RuntimeException(
                'The given treenode does not reference a valid assignment and should not be used'
            );
        }

        $this->treeNode = $treeNode;
    }

    /**
     * @return bool
     */
    public function checkForPlagiarismAfterSubmission()
    {
        return $this->assignmentEntryPlagiarismResultServiceBridge->checkForPlagiarismAfterSubmission($this->treeNode);
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
}