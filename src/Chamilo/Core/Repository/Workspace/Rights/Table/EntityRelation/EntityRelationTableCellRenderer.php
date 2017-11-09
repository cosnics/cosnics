<?php
namespace Chamilo\Core\Repository\Workspace\Rights\Table\EntityRelation;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Repository\Workspace\Rights\Manager;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceEntityRelation;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Rights\Table\EntityRelation
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntityRelationTableCellRenderer extends DataClassTableCellRenderer implements 
    TableCellRendererActionsColumnSupport
{

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer::render_cell()
     */
    public function render_cell($column, $entityRelation)
    {
        switch ($column->get_name())
        {
            case EntityRelationTableColumnModel::COLUMN_ENTITY :
                if ($entityRelation->get_entity_type() == UserEntity::ENTITY_TYPE)
                {
                    return \Chamilo\Libraries\Storage\DataManager\DataManager::retrieve_by_id(
                        User::class_name(), 
                        $entityRelation->get_entity_id())->get_fullname();
                }
                else
                {
                    return \Chamilo\Libraries\Storage\DataManager\DataManager::retrieve_by_id(
                        Group::class_name(), 
                        $entityRelation->get_entity_id())->get_name();
                }
            case RightsService::RIGHT_VIEW :
                return $this->getRightsIcon(RightsService::RIGHT_VIEW, $entityRelation);
            case RightsService::RIGHT_ADD :
                return $this->getRightsIcon(RightsService::RIGHT_ADD, $entityRelation);
            case RightsService::RIGHT_EDIT :
                return $this->getRightsIcon(RightsService::RIGHT_EDIT, $entityRelation);
            case RightsService::RIGHT_DELETE :
                return $this->getRightsIcon(RightsService::RIGHT_DELETE, $entityRelation);
            case RightsService::RIGHT_USE :
                return $this->getRightsIcon(RightsService::RIGHT_USE, $entityRelation);
            case RightsService::RIGHT_COPY :
                return $this->getRightsIcon(RightsService::RIGHT_COPY, $entityRelation);
            case RightsService::RIGHT_MANAGE :
                return $this->getRightsIcon(RightsService::RIGHT_MANAGE, $entityRelation);
        }
        
        return parent::render_cell($column, $entityRelation);
    }

    /**
     *
     * @param integer $right
     * @param WorkspaceEntityRelation $entityRelation
     * @return string
     */
    private function getRightsIcon($right, WorkspaceEntityRelation $entityRelation)
    {
        $state = $entityRelation->get_rights() & $right ? 'True' : 'False';
        return Theme::getInstance()->getCommonImage('Action/Setting' . $state);
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport::get_actions()
     */
    public function get_actions($entityRelation)
    {
        $toolbar = new Toolbar();
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Edit', null, Utilities::COMMON_LIBRARIES), 
                Theme::getInstance()->getCommonImagePath('Action/Edit'), 
                $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_UPDATE, 
                        Manager::PARAM_ENTITY_RELATION_ID => $entityRelation->get_id())), 
                ToolbarItem::DISPLAY_ICON));
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Delete', null, Utilities::COMMON_LIBRARIES), 
                Theme::getInstance()->getCommonImagePath('Action/Delete'), 
                $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_DELETE, 
                        Manager::PARAM_ENTITY_RELATION_ID => $entityRelation->get_id())), 
                ToolbarItem::DISPLAY_ICON, 
                true));
        
        return $toolbar->as_html();
    }
}
