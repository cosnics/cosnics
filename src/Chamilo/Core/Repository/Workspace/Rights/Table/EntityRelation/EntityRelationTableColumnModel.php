<?php
namespace Chamilo\Core\Repository\Workspace\Rights\Table\EntityRelation;

use Chamilo\Core\Repository\Workspace\Rights\Manager;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

class EntityRelationTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const COLUMN_ENTITY = 'entity';

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\TableColumnModel::initialize_columns()
     */
    public function initialize_columns()
    {
        $this->add_column(
            new StaticTableColumn(
                self::COLUMN_ENTITY, 
                Translation::getInstance()->getTranslation('Entity', null, Manager::context())));
        $this->add_column(
            new StaticTableColumn(
                RightsService::RIGHT_VIEW, 
                $this->getRightIcon(RightsService::RIGHT_VIEW, 'ViewRight')));
        $this->add_column(
            new StaticTableColumn(RightsService::RIGHT_ADD, $this->getRightIcon(RightsService::RIGHT_ADD, 'AddRight')));
        $this->add_column(
            new StaticTableColumn(
                RightsService::RIGHT_EDIT, 
                $this->getRightIcon(RightsService::RIGHT_EDIT, 'EditRight')));
        $this->add_column(
            new StaticTableColumn(
                RightsService::RIGHT_DELETE, 
                $this->getRightIcon(RightsService::RIGHT_DELETE, 'DeleteRight')));
        $this->add_column(
            new StaticTableColumn(RightsService::RIGHT_USE, $this->getRightIcon(RightsService::RIGHT_USE, 'UseRight')));
        $this->add_column(
            new StaticTableColumn(
                RightsService::RIGHT_COPY, 
                $this->getRightIcon(RightsService::RIGHT_COPY, 'CopyRight')));
        $this->add_column(
            new StaticTableColumn(
                RightsService::RIGHT_MANAGE, 
                $this->getRightIcon(RightsService::RIGHT_MANAGE, 'ManageRight')));
    }

    /**
     *
     * @param integer $right
     * @param string $translationVariable
     *
     * @return string
     */
    private function getRightIcon($right, $translationVariable)
    {
        return Theme::getInstance()->getImage(
            'Rights/' . $right, 
            'png', 
            Translation::get($translationVariable), 
            null, 
            ToolbarItem::DISPLAY_ICON, 
            false, 
            \Chamilo\Core\Repository\Workspace\Manager::context());
    }
}
