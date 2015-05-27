<?php
namespace Chamilo\Core\Repository\Workspace\Rights\Table\EntityRelation;

use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceEntityRelation;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Platform\Translation;

class EntityRelationTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    public function initialize_columns()
    {
        $this->add_column(
            new DataClassPropertyTableColumn(
                WorkspaceEntityRelation :: class_name(),
                WorkspaceEntityRelation :: PROPERTY_ENTITY_TYPE,
                null,
                false));

        $this->add_column(
            new DataClassPropertyTableColumn(
                WorkspaceEntityRelation :: class_name(),
                WorkspaceEntityRelation :: PROPERTY_ENTITY_ID,
                null,
                false));

        $this->add_column(new StaticTableColumn(RightsService :: RIGHT_VIEW, Translation :: get('ViewRight')));
        $this->add_column(new StaticTableColumn(RightsService :: RIGHT_ADD, Translation :: get('AddRight')));
        $this->add_column(new StaticTableColumn(RightsService :: RIGHT_EDIT, Translation :: get('EditRight')));
        $this->add_column(new StaticTableColumn(RightsService :: RIGHT_DELETE, Translation :: get('DeleteRight')));
        $this->add_column(new StaticTableColumn(RightsService :: RIGHT_USE, Translation :: get('UseRight')));
        $this->add_column(new StaticTableColumn(RightsService :: RIGHT_COPY, Translation :: get('CopyRight')));
    }
}
