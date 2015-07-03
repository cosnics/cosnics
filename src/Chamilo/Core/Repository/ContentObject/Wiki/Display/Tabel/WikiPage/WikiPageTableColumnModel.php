<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Display\Tabel\WikiPage;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Platform\Translation;

class WikiPageTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    public function initialize_columns()
    {
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject :: class_name(), ContentObject :: PROPERTY_TITLE));
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject :: class_name(), ContentObject :: PROPERTY_DESCRIPTION));
        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject :: class_name(), ContentObject :: PROPERTY_MODIFICATION_DATE));
        $this->add_column(new StaticTableColumn(Translation :: get('Versions')));
    }
}
