<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Extension\Plagiarism\Service;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Extension\Plagiarism\Storage\DataClass\EntryPlagiarismResult;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Extension\Plagiarism\Storage\Repository\EntryPlagiarismResultRepository;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Extension\Plagiarism\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class EntryPlagiarismResultService
{
    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Extension\Plagiarism\Storage\Repository\EntryPlagiarismResultRepository
     */
    protected $entryPlagiarismResultRepository;

    /**
     * EntryPlagiarismResultService constructor.
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Extension\Plagiarism\Storage\Repository\EntryPlagiarismResultRepository $entryPlagiarismResultRepository
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
     */
    public function createEntryPlagiarismResultForEntry(Entry $entry, string $externalId)
    {
        $plagiarismInstance = $this->createEntryPlagiarismResultInstance();
    }

    abstract protected function createEntryPlagiarismResultInstance();

}
