<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Table;

use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTable;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EntryPlagiarismResultTable extends RecordTable
{
    /**
     * @var EntryPlagiarismResultTableParameters
     */
    protected $entryPlagiarismResultTableParameters;

    /**
     * @param \Chamilo\Libraries\Architecture\Application\Application $component
     * @param EntryPlagiarismResultTableParameters $entryPlagiarismResultTableParameters
     *
     * @throws \Exception
     */
    public function __construct($component, EntryPlagiarismResultTableParameters $entryPlagiarismResultTableParameters)
    {
        $this->entryPlagiarismResultTableParameters = $entryPlagiarismResultTableParameters;

        parent::__construct($component);
    }

    /**
     * @return EntryPlagiarismResultTableParameters
     */
    public function getEntryPlagiarismResultTableParameters(): EntryPlagiarismResultTableParameters
    {
        return $this->entryPlagiarismResultTableParameters;
    }

}