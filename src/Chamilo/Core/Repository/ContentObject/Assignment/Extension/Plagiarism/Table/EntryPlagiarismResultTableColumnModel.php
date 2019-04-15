<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Table;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EntryPlagiarismResultTableColumnModel extends RecordTableColumnModel implements TableColumnModelActionsColumnSupport
{

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->addEntityColumns();
    }

    protected function addEntityColumns()
    {
        $entityProperties = $this->getEntryResultTableParameters()->getEntityProperties();
        foreach ($entityProperties as $entityProperty)
        {
            $this->add_column(
                new DataClassPropertyTableColumn($this->getEntryResultTableParameters()->getEntityClass(), $entityProperty)
            );
        }

        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject::class_name(), ContentObject::PROPERTY_TITLE)
        );

        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject::class_name(), ContentObject::PROPERTY_DESCRIPTION)
        );

        $this->add_column(
            new DataClassPropertyTableColumn(
                $this->getEntryResultTableParameters()->getEntryClassName(), Entry::PROPERTY_SUBMITTED, Translation::get('Submitted')
            )
        );

        $this->add_column(
            new DataClassPropertyTableColumn(
                $this->getEntryResultTableParameters()->getScoreClassName(), Score::PROPERTY_SCORE, Translation::get('Score')
            )
        );

        $this->add_column(
            new DataClassPropertyTableColumn(
                $this->getEntryResultTableParameters()->getEntryPlagiarismResultClassName(), EntryPlagiarismResult::PROPERTY_RESULT, Translation::get('PlagiarismScore')
            )
        );

        $this->add_column(
            new DataClassPropertyTableColumn(
                $this->getEntryResultTableParameters()->getEntryPlagiarismResultClassName(), EntryPlagiarismResult::PROPERTY_STATUS, Translation::get('Status'), true, 'plagiarism-column-status', 'plagiarism-column-status'
            )
        );
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Table\EntryPlagiarismResultTableParameters
     */
    protected function getEntryResultTableParameters()
    {
        return $this->getTable()->getEntryPlagiarismResultTableParameters();
    }

    /**
     * @return \Chamilo\Libraries\Format\Table\Table | \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Table\EntryPlagiarismResultTable
     */
    protected function getTable()
    {
        return $this->get_table();
    }
}