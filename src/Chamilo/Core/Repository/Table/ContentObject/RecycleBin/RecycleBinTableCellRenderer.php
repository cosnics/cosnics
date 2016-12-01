<?php
namespace Chamilo\Core\Repository\Table\ContentObject\RecycleBin;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: recycle_bin_browser_table_cell_renderer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * 
 * @package repository.lib.repository_manager.component.recycle_bin_browser
 */

/**
 * Cell renderer for the recycle bin browser table
 */
class RecycleBinTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    private $parent_title_cache = array();

    public function render_cell($column, $content_object)
    {
        switch ($column->get_name())
        {
            case ContentObject::PROPERTY_TITLE :
                $title = parent::render_cell($column, $content_object);
                $title_short = $title;
                if (strlen($title_short) > 53)
                {
                    $title_short = substr($title_short, 0, 50) . '&hellip;';
                }
                return '<a href="' .
                     htmlentities($this->get_component()->get_content_object_viewing_url($content_object)) . '" title="' .
                     htmlentities($title) . '">' . $title_short . '</a>';
            case Translation::get(RecycleBinTableColumnModel::ORIGINAL_LOCATION) :
                $pid = $content_object->get_parent_id();
                if (! isset($this->parent_title_cache[$pid]))
                {
                    $category = DataManager::retrieve_categories(
                        new EqualityCondition(
                            new PropertyConditionVariable(
                                RepositoryCategory::class_name(), 
                                RepositoryCategory::PROPERTY_ID), 
                            new StaticConditionVariable($pid)))->next_result();
                    
                    $this->parent_title_cache[$pid] = '<a href="' .
                         htmlentities(
                            $this->get_component()->get_url(
                                array(
                                    Manager::PARAM_CATEGORY_ID => $pid, 
                                    Manager::PARAM_ACTION => Manager::ACTION_BROWSE_CONTENT_OBJECTS))) . '" title="' .
                         htmlentities(Translation::get('BrowseThisCategory')) . '">' . ($category ? $category->get_name() : Translation::get(
                            'Root', 
                            null, 
                            Utilities::COMMON_LIBRARIES)) . '</a>';
                }
                
                return $this->parent_title_cache[$pid];
            
            case Theme::getInstance()->getCommonImage(
                'Action/Category', 
                'png', 
                Translation::get('Type'), 
                null, 
                ToolbarItem::DISPLAY_ICON) :
                return $content_object->get_icon_image(Theme::ICON_MINI);
            
            case ContentObject::PROPERTY_DESCRIPTION :
                return Utilities::htmlentities(
                    StringUtilities::getInstance()->truncate($content_object->get_description(), 50));
        }
        return parent::render_cell($column, $content_object);
    }

    public function get_actions($content_object)
    {
        $toolbar = new Toolbar();
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Restore', null, Utilities::COMMON_LIBRARIES), 
                Theme::getInstance()->getCommonImagePath('Action/Restore'), 
                $this->get_component()->get_content_object_restoring_url($content_object), 
                ToolbarItem::DISPLAY_ICON));
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Delete', null, Utilities::COMMON_LIBRARIES), 
                Theme::getInstance()->getCommonImagePath('Action/Delete'), 
                $this->get_component()->get_content_object_deletion_url($content_object), 
                ToolbarItem::DISPLAY_ICON, 
                true));
        return $toolbar->as_html();
    }
}
