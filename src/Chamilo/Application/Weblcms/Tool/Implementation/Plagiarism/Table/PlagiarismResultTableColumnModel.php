<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Table;

use Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Storage\DataClass\ContentObjectPlagiarismResult;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Platform\Translation;

/**
 * @package Chamilo\Application\Plagiarism\Table
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PlagiarismResultTableColumnModel extends RecordTableColumnModel implements TableColumnModelActionsColumnSupport
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
                User::class, User::PROPERTY_LASTNAME,
                Translation::getInstance()->getTranslation('RequestedBy', null, Manager::context())
            )
        );

        $this->add_column(
            new DataClassPropertyTableColumn(
                ContentObjectPlagiarismResult::class, ContentObjectPlagiarismResult::PROPERTY_REQUEST_DATE
            )
        );

        $this->add_column(
            new DataClassPropertyTableColumn(
                ContentObjectPlagiarismResult::class, ContentObjectPlagiarismResult::PROPERTY_STATUS, null, true,
                'plagiarism-column-status', 'plagiarism-column-status'
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