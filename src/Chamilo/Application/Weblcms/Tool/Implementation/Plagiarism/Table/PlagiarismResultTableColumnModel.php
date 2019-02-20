<?php

namespace Chamilo\Application\Plagiarism\Table;

use Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Storage\DataClass\ContentObjectPlagiarismResult;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableColumnModel;

/**
 * @package Chamilo\Application\Plagiarism\Table
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PlagiarismResultTableColumnModel extends RecordTableColumnModel
{

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_TITLE));
        $this->add_column(new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION));

        $this->add_column(
            new DataClassPropertyTableColumn(
                ContentObjectPlagiarismResult::class, ContentObjectPlagiarismResult::PROPERTY_STATUS
            )
        );

        $this->add_column(
            new DataClassPropertyTableColumn(
                ContentObjectPlagiarismResult::class, ContentObjectPlagiarismResult::PROPERTY_RESULT, null, true,
                'plagiarism-column-status', 'plagiarism-column-status'
            )
        );
    }
}