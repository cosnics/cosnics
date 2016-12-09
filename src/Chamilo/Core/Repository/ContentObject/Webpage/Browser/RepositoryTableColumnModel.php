<?php
namespace Chamilo\Core\Repository\ContentObject\Webpage\Browser;

use Chamilo\Core\Repository\ContentObject\Webpage\Storage\DataClass\Webpage;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;

/**
 * Table column model for the repository browser table
 */
class RepositoryTableColumnModel extends \Chamilo\Core\Repository\Table\ContentObject\Table\RepositoryTableColumnModel
{

    public function add_type_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(Webpage::class_name(), Webpage::PROPERTY_EXTENSION));
        $this->add_column(new DataClassPropertyTableColumn(Webpage::class_name(), Webpage::PROPERTY_FILESIZE));
    }
}
