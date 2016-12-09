<?php
namespace Chamilo\Core\Home\Rights\Table\BlockTypeTargetEntity;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Home\Rights\Manager;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Builds the table for the BlockTypeTargetEntity data class
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class BlockTypeTargetEntityTableCellRenderer extends RecordTableCellRenderer implements 
    TableCellRendererActionsColumnSupport
{

    /**
     * Renders a single cell
     * 
     * @param TableColumn $column
     * @param string[] $row
     *
     * @return String
     */
    public function render_cell($column, $row)
    {
        if ($column->get_name() == 'target_entities')
        {
            return $this->renderTargetEntities($row['target_entities']);
        }
        
        return parent::render_cell($column, $row);
    }

    /**
     * Renders the target entities
     * 
     * @param array $targetEntities
     *
     * @return string
     */
    protected function renderTargetEntities($targetEntities = array())
    {
        $translator = Translation::getInstance();
        
        if (empty($targetEntities))
        {
            return $translator->getTranslation('Everybody', null, Utilities::COMMON_LIBRARIES);
        }
        
        $targetEntitiesHtml = array();
        
        $targetEntitiesHtml[] = '<select>';
        
        foreach ($targetEntities as $entityType => $entityIds)
        {
            switch ($entityType)
            {
                case PlatformGroupEntity::ENTITY_TYPE :
                    foreach ($entityIds as $group_id)
                    {
                        $group = \Chamilo\Core\Group\Storage\DataManager::retrieve_by_id(Group::class_name(), $group_id);
                        if ($group)
                        {
                            $targetEntitiesHtml[] = '<option>' .
                                 $translator->getTranslation('GroupShort', null, Manager::context()) . ': ' .
                                 $group->get_name() . '</option>';
                        }
                    }
                    break;
                case UserEntity::ENTITY_TYPE :
                    foreach ($entityIds as $user_id)
                    {
                        $targetEntitiesHtml[] = '<option>' .
                             $translator->getTranslation('UserShort', null, Manager::context()) . ': ' .
                             \Chamilo\Core\User\Storage\DataManager::get_fullname_from_user($user_id) . '</option>';
                    }
                    break;
            }
        }
        
        $targetEntitiesHtml[] = '</select>';
        
        return implode(PHP_EOL, $targetEntitiesHtml);
    }

    /**
     * Returns the actions toolbar
     * 
     * @param mixed $result
     *
     * @return String
     */
    public function get_actions($result)
    {
        $translator = Translation::getInstance();
        
        $toolbar = new Toolbar();
        
        $toolbar->add_item(
            new ToolbarItem(
                $translator->getTranslation('SetBlockTypeTargetEntitiesComponent', null, Manager::context()), 
                Theme::getInstance()->getImagePath(Manager::context(), 'SetBlockTypeTargetEntities'), 
                $this->get_component()->get_set_block_type_target_entities_url($result['block_type']), 
                ToolbarItem::DISPLAY_ICON));
        
        return $toolbar->render();
    }
}