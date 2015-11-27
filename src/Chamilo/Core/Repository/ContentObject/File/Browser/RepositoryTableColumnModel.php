<?php
namespace Chamilo\Core\Repository\ContentObject\File\Browser;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;

/**
 * Table column model for the repository browser table
 */
class RepositoryTableColumnModel extends \Chamilo\Core\Repository\Table\ContentObject\Table\RepositoryTableColumnModel
{

    public function add_type_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(File :: class_name(), File :: PROPERTY_EXTENSION));
        $this->add_column(new DataClassPropertyTableColumn(File :: class_name(), File :: PROPERTY_FILESIZE));
    }
}
