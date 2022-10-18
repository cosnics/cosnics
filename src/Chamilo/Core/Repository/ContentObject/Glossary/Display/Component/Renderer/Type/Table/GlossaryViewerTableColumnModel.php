<?php
namespace Chamilo\Core\Repository\ContentObject\Glossary\Display\Component\Renderer\Type\Table;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

class GlossaryViewerTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    public function initializeColumns()
    {
        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_TITLE));
        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION));
    }
}
