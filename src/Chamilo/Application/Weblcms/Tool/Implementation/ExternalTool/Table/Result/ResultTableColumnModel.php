<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\ExternalTool\Table\Result;

use Chamilo\Application\Weblcms\Bridge\ExternalTool\Storage\DataClass\ExternalToolResult;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableColumnModel;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\ExternalTool\Table\Result
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ResultTableColumnModel extends RecordTableColumnModel
{

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(User::class, User::PROPERTY_FIRSTNAME));
        $this->add_column(new DataClassPropertyTableColumn(User::class, User::PROPERTY_LASTNAME));

        $this->add_column(
            new DataClassPropertyTableColumn(ExternalToolResult::class, ExternalToolResult::PROPERTY_RESULT)
        );
    }
}