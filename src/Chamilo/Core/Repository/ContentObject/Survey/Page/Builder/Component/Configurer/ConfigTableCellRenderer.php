<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Component\Configurer;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataClass\PageConfig;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class ConfigTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    function render_cell($column, PageConfig $config)
    {
        switch ($column->get_name())
        {
            case PageConfig :: PROPERTY_NAME :
                return $config->get_name();
                break;
            case PageConfig :: PROPERTY_DESCRIPTION :
                return $config->get_description();
                break;
            case PageConfig :: PROPERTY_CONFIG_CREATED :
                return DatetimeUtilities :: format_locale_date(null, $config->get_config_created());
                break;
            case PageConfig :: PROPERTY_CONFIG_UPDATED :
                return DatetimeUtilities :: format_locale_date(null, $config->get_config_updated());
                break;
            case PageConfig :: PROPERTY_FROM_VISIBLE_QUESTION_ID :
                return $config->get_from_visible_question_id();
                break;
            case PageConfig :: PROPERTY_TO_VISIBLE_QUESTION_IDS :
                return implode(', ', $config->get_to_visible_question_ids());
                break;
            default :
                ;
                break;
        }
    }

    function get_actions($config)
    {
        $toolbar = new Toolbar();
        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get(
                    'Edit', 
                    array('OBJECT' => Translation :: get('PageConfig')), 
                    Utilities :: COMMON_LIBRARIES), 
                Theme :: getInstance()->getCommonImagesPath() . 'action_edit.png', 
                $this->get_component()->get_config_update_url($config->get_id()), 
                ToolbarItem :: DISPLAY_ICON));
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get(
                    'Delete', 
                    array('OBJECT' => Translation :: get('PageConfig')), 
                    Utilities :: COMMON_LIBRARIES), 
                Theme :: getInstance()->getCommonImagesPath() . 'action_delete.png', 
                $this->get_component()->get_config_delete_url($config->get_id()), 
                ToolbarItem :: DISPLAY_ICON, 
                true));
        return $toolbar->as_html();
    }

    public function render_id_cell($config)
    {
        return $config->get_id();
    }
}
?>