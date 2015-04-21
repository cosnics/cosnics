<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Table\Configuration;

use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataClass\Configuration;

class ConfigurationTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    function render_cell($column, Configuration $config)
    {
        switch ($column->get_name())
        {
            case Configuration :: PROPERTY_NAME :
                return $config->getName();
                break;
            case Configuration :: PROPERTY_DESCRIPTION :
                return $config->getDescription();
                break;
            case Configuration :: PROPERTY_CREATED :
                return DatetimeUtilities :: format_locale_date(null, $config->getCreated());
                break;
            case Configuration :: PROPERTY_UPDATED :
                return DatetimeUtilities :: format_locale_date(null, $config->getUpdated());
                break;
            case Configuration :: PROPERTY_COMPLEX_QUESTION_ID :
                return $config->getComplexQuestionId();
                break;
            case Configuration :: PROPERTY_TO_VISIBLE_QUESTION_IDS :
                return implode(', ', $config->getToVisibleQuestionIds());
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
                Theme :: getInstance()->getCommonImagePath('Action/Edit'),
                $this->get_component()->get_config_update_url($config->get_id()),
                ToolbarItem :: DISPLAY_ICON));

        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get(
                    'Delete',
                    array('OBJECT' => Translation :: get('PageConfig')),
                    Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath('Action/Delete'),
                $this->get_component()->get_config_delete_url($config->get_id()),
                ToolbarItem :: DISPLAY_ICON,
                true));
        return $toolbar->as_html();
    }

    public function render_id_cell($configuration)
    {
        return $configuration->get_id();
    }
}
?>