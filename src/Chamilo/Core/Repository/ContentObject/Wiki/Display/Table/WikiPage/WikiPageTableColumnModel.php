<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Display\Table\WikiPage;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;

class WikiPageTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    public function initializeColumns()
    {
        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_TITLE));
        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION));
        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_MODIFICATION_DATE));
        $this->addColumn(new StaticTableColumn(Translation::get('Versions')));
    }
}
