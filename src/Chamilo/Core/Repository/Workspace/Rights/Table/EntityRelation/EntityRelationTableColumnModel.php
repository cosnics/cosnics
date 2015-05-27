<?php
namespace Chamilo\Core\Repository\Workspace\Rights\Table\EntityRelation;

use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceEntityRelation;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Structure\ToolbarItem;

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

        $this->add_column(
            new StaticTableColumn(
                RightsService :: RIGHT_VIEW,
                $this->getRightIcon(RightsService :: RIGHT_VIEW, 'ViewRight')));
        $this->add_column(
            new StaticTableColumn(
                RightsService :: RIGHT_ADD,
                $this->getRightIcon(RightsService :: RIGHT_ADD, 'AddRight')));
        $this->add_column(
            new StaticTableColumn(
                RightsService :: RIGHT_EDIT,
                $this->getRightIcon(RightsService :: RIGHT_EDIT, 'EditRight')));
        $this->add_column(
            new StaticTableColumn(
                RightsService :: RIGHT_DELETE,
                $this->getRightIcon(RightsService :: RIGHT_DELETE, 'DeleteRight')));
        $this->add_column(
            new StaticTableColumn(
                RightsService :: RIGHT_USE,
                $this->getRightIcon(RightsService :: RIGHT_USE, 'UseRight')));
        $this->add_column(
            new StaticTableColumn(
                RightsService :: RIGHT_COPY,
                $this->getRightIcon(RightsService :: RIGHT_COPY, 'CopyRight')));
    }

    private function getRightIcon($right, $translationVariable)
    {
        return Theme :: getInstance()->getImage(
            'Rights/' . $right,
            'png',
            Translation :: get($translationVariable),
            null,
            ToolbarItem :: DISPLAY_ICON,
            false,
            \Chamilo\Core\Repository\Workspace\Manager :: context());
    }
}
