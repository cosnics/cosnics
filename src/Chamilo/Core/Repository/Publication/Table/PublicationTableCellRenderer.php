<?php

namespace Chamilo\Core\Repository\Publication\Table;

use Chamilo\Core\Repository\Publication\Manager;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;

class PublicationTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass|string[]|Attributes $publication_attributes
     *
     * @return string
     */
    public function get_actions($publication_attributes)
    {
        $toolbar = new Toolbar();

        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Delete', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_DELETE,
                        Manager::PARAM_PUBLICATION_ID => $publication_attributes->get_id(),
                        Manager::PARAM_PUBLICATION_APPLICATION => $publication_attributes->get_application(),
                        Manager::PARAM_PUBLICATION_CONTEXT => $publication_attributes->getPublicationContext()
                    )
                ), ToolbarItem::DISPLAY_ICON, true
            )
        );

        if (!$publication_attributes->get_content_object()->is_latest_version())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Update', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('undo'),
                    $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_UPDATE,
                            Manager::PARAM_PUBLICATION_APPLICATION => $publication_attributes->get_application(),
                            Manager::PARAM_PUBLICATION_ID => $publication_attributes->get_id()
                        )
                    ), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        return $toolbar->as_html();
    }

    public function renderCell(TableColumn $column, $publication_attributes): string
    {

        // Add special features here
        switch ($column->get_name())
        {
            case Attributes::PROPERTY_DATE :
                return DatetimeUtilities::getInstance()->formatLocaleDate(
                    Translation::get('DateFormatShort', null, StringUtilities::LIBRARIES) . ', ' .
                    Translation::get('TimeNoSecFormat', null, StringUtilities::LIBRARIES),
                    $publication_attributes->get_date()
                );
            case Attributes::PROPERTY_APPLICATION :
                return Translation::get('TypeName', null, $publication_attributes->get_application());
            case Attributes::PROPERTY_TITLE :
                $url = $publication_attributes->get_url();

                return '<a href="' . $url . '"><span title="' . htmlentities($publication_attributes->get_title()) .
                    '">' . StringUtilities::getInstance()->truncate($publication_attributes->get_title(), 50) .
                    '</span></a>';
            case Attributes::PROPERTY_DATE :
                return date('Y-m-d, H:i', $publication_attributes->get_date());
        }

        return parent::renderCell($column, $publication_attributes);
    }
}
