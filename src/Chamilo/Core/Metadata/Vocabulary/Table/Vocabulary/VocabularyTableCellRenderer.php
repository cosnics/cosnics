<?php
namespace Chamilo\Core\Metadata\Vocabulary\Table\Vocabulary;

use Chamilo\Core\Metadata\Vocabulary\Manager;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Table cell renderer for the schema
 * 
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class VocabularyTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    /**
     * Renders a single cell
     * 
     * @param TableColumn $column
     * @param mixed $result
     *
     * @return string
     */
    public function render_cell($column, $result)
    {
        switch ($column->get_name())
        {
            case VocabularyTableColumnModel::COLUMN_DEFAULT :
                $image = 'Action/Default';
                $image .= $result->isDefault() ? '' : 'Na';
                
                $translationVariable = 'Default';
                $translationVariable .= $result->isDefault() ? '' : 'Na';
                
                return Theme::getInstance()->getImage(
                    $image, 
                    'png', 
                    Translation::get($translationVariable, null, $this->get_component()->package()), 
                    $this->get_component()->get_url(
                        array(
                            \Chamilo\Core\Metadata\Vocabulary\Manager::PARAM_ACTION => \Chamilo\Core\Metadata\Vocabulary\Manager::ACTION_DEFAULT, 
                            Manager::PARAM_VOCABULARY_ID => $result->get_id())), 
                    ToolbarItem::DISPLAY_ICON, 
                    false, 
                    $this->get_component()->package());
                break;
        }
        
        return parent::render_cell($column, $result);
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
        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Edit', null, Utilities::COMMON_LIBRARIES), 
                Theme::getInstance()->getCommonImagePath('Action/Edit'), 
                $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_UPDATE, 
                        Manager::PARAM_VOCABULARY_ID => $result->get_id())), 
                ToolbarItem::DISPLAY_ICON));
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Delete', null, Utilities::COMMON_LIBRARIES), 
                Theme::getInstance()->getCommonImagePath('Action/Delete'), 
                $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_DELETE, 
                        Manager::PARAM_VOCABULARY_ID => $result->get_id())), 
                ToolbarItem::DISPLAY_ICON, 
                true));
        return $toolbar->as_html();
    }
}