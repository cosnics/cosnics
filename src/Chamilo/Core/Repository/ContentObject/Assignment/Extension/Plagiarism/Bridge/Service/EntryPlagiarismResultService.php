<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Service;

use Chamilo\Application\Plagiarism\Domain\Turnitin\SubmissionStatus;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\Repository\EntryPlagiarismResultRepository;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class EntryPlagiarismResultService
{
    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\Repository\EntryPlagiarismResultRepository
     */
    protected $entryPlagiarismResultRepository;

    /**
     * EntryPlagiarismResultService constructor.
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\Repository\EntryPlagiarismResultRepository $entryPlagiarismResultRepository
     */
    public function __construct(
        EntryPlagiarismResultRepository $entryPlagiarismResultRepository
    )
    {
        $this->entryPlagiarismResultRepository = $entryPlagiarismResultRepository;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return EntryPlagiarismResult|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findEntryPlagiarismResultByEntry(Entry $entry)
    {
        return $this->entryPlagiarismResultRepository->findEntryPlagiarismResultByEntry($entry);
    }

    /**
     * @param string $externalId
     *
     * @return EntryPlagiarismResult|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findEntryPlagiarismResultByExternalId(string $externalId)
    {
        return $this->entryPlagiarismResultRepository->findEntryPlagiarismResultByExternalId($externalId);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param string $externalId
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult
     */
    public function createEntryPlagiarismResultForEntry(Entry $entry, string $externalId)
    {
        $entryPlagiarismResult = $this->createEntryPlagiarismResultInstance();

        $entryPlagiarismResult->setEntryId($entry->getId());
        $entryPlagiarismResult->setExternalId($externalId);
        $entryPlagiarismResult->setStatus(SubmissionStatus::STATUS_UPLOAD_IN_PROGRESS);

        if (!$this->entryPlagiarismResultRepository->createEntryPlagiarismResult($entryPlagiarismResult))
        {
            throw new \InvalidArgumentException(
                sprintf('The entry plagiarism result for entry %s could not be created', $entry->getId())
            );
        }

        return $entryPlagiarismResult;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult $entryPlagiarismResult
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function updateEntryPlagiarismResult(EntryPlagiarismResult $entryPlagiarismResult)
    {
        if(!$this->entryPlagiarismResultRepository->updateEntryPlagiarismResult($entryPlagiarismResult))
        {
            throw new \RuntimeException(
                sprintf('The entry plagiarism result %s could not be update', $entryPlagiarismResult->getId())
            );
        }

        return $entryPlagiarismResult;
    }

    /**
     * @return EntryPlagiarismResult
     */
    abstract protected function createEntryPlagiarismResultInstance();

}
