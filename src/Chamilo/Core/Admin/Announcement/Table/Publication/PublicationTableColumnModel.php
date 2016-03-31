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
    public function initialize_columns()
    {
        $this->add_column(new StaticTableColumn(self :: COLUMN_STATUS));
        
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject :: class_name(), ContentObject :: PROPERTY_TITLE));
        
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject :: class_name(), ContentObject :: PROPERTY_DESCRIPTION));
        
        $this->add_column(
            new DataClassPropertyTableColumn(Publication :: class_name(), Publication :: PROPERTY_PUBLICATION_DATE));
        
        $this->add_column(
            new DataClassPropertyTableColumn(Publication :: class_name(), Publication :: PROPERTY_PUBLISHER_ID));
        
        $this->add_column(new StaticTableColumn(self :: COLUMN_PUBLISHED_FOR));
    }
}
