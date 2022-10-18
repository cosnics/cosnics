<?php
namespace Chamilo\Core\Admin\Announcement\Table\Publication;

use Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

class PublicationTableColumnModel extends RecordTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const COLUMN_STATUS = 'status';
    const COLUMN_PUBLISHED_FOR = 'published_for';

    /**
     * Initializes the columns for the table
     */
    public function initializeColumns()
    {
        $this->addColumn(new StaticTableColumn(self::COLUMN_STATUS));
        
        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_TITLE));
        
        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION));
        
        $this->addColumn(
            new DataClassPropertyTableColumn(Publication::class, Publication::PROPERTY_PUBLICATION_DATE));
        
        $this->addColumn(
            new DataClassPropertyTableColumn(Publication::class, Publication::PROPERTY_PUBLISHER_ID));
        
        $this->addColumn(new StaticTableColumn(self::COLUMN_PUBLISHED_FOR));
    }
}
