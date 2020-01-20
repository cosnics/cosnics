<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Service;

use Chamilo\Application\Plagiarism\Domain\SubmissionStatus;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Parameters\FilterParameters;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EntriesPlagiarismChecker
{
    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Service\PlagiarismChecker
     */
    protected $plagiarismChecker;

    /**
     * EntriesPlagiarismChecker constructor.
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Service\PlagiarismChecker $plagiarismChecker
     */
    public function __construct(
        \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Service\PlagiarismChecker $plagiarismChecker
    )
    {
        $this->plagiarismChecker = $plagiarismChecker;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $submitter
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface $assignmentServiceBridge
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface $entryPlagiarismResultServiceBridge
     *
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     */
    public function checkAllEntriesForPlagiarism(
        User $submitter,
        AssignmentServiceBridgeInterface $assignmentServiceBridge,
        EntryPlagiarismResultServiceBridgeInterface $entryPlagiarismResultServiceBridge
    )
    {
        $entries = $assignmentServiceBridge->findEntries();
        $entriesWithPlagiarismResults = $entryPlagiarismResultServiceBridge->findEntriesWithPlagiarismResult(
            $assignmentServiceBridge->getCurrentEntityType(), new FilterParameters()
        );

        $checkedEntryIds = [];

        foreach ($entriesWithPlagiarismResults as $entry)
        {
            if (empty($entry[EntryPlagiarismResult::PROPERTY_EXTERNAL_ID]))
            {
                continue;
            }

            $submissionStatus = new SubmissionStatus(
                $entry[EntryPlagiarismResult::PROPERTY_EXTERNAL_ID], $entry[EntryPlagiarismResult::PROPERTY_STATUS],
                null, $entry[EntryPlagiarismResult::PROPERTY_ERROR]
            );

            if ($submissionStatus->isReportGenerated() ||
                ($submissionStatus->isFailed() && !$submissionStatus->canRetry()))
            {
                $checkedEntryIds[] = $entry[Entry::PROPERTY_ID];
            }
        }

        foreach ($entries as $entry)
        {
            if (in_array($entry->getId(), $checkedEntryIds))
            {
                continue;
            }

            if ($this->plagiarismChecker->canCheckForPlagiarism($entry))
            {
                try {
                    $this->plagiarismChecker->checkEntryForPlagiarism(
                        $entry, $submitter, $entryPlagiarismResultServiceBridge
                    );
                } catch (\Exception $exception) {
                    //todo: long term fix
                }
            }
        }
    }

}
