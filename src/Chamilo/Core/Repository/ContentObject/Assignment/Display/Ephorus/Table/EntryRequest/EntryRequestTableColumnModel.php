<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Table\EntryRequest;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 * Column model for ephorus requests browser table.
 *
 * @author Tom Goethals
 */
class EntryRequestTableColumnModel extends DataClassTableColumnModel implements
    TableColumnModelActionsColumnSupport
{
    const COLUMN_NAME_AUTHOR = 'author';

    /**
     * Gets the column names to use in the table.
     */
    public function initializeColumns()
    {
        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_TITLE)
        );
        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION)
        );
        $this->addColumn(new StaticTableColumn(self::COLUMN_NAME_AUTHOR));

        $this->addColumn(
            new DataClassPropertyTableColumn(
                Entry::class,
                Entry::PROPERTY_SUBMITTED
            )
        );
        $this->addColumn(new DataClassPropertyTableColumn(Request::class, Request::PROPERTY_REQUEST_TIME));
        $this->addColumn(new DataClassPropertyTableColumn(Request::class, Request::PROPERTY_PERCENTAGE));
        $this->addColumn(new DataClassPropertyTableColumn(Request::class, Request::PROPERTY_STATUS));
        $this->addColumn(
            new DataClassPropertyTableColumn(Request::class, Request::PROPERTY_VISIBLE_IN_INDEX)
        );
    }
}
