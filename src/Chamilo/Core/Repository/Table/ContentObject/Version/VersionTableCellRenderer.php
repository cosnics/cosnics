<?php
namespace Chamilo\Core\Repository\Table\ContentObject\Version;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Core\Repository\Workspace\Service\RightsService;

class VersionTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    public function render_cell($column, $content_object)
    {
        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_TITLE :
                $title = parent :: render_cell($column, $content_object);
                $title_short = StringUtilities :: getInstance()->truncate($title, 53, false);
                return '<a href="' .
                     htmlentities($this->get_component()->get_content_object_viewing_url($content_object)) . '" title="' .
                     $title . '">' . $title_short . '</a>';
            case Translation :: get(VersionTableColumnModel :: USER, null, \Chamilo\Core\User\Manager :: context()) :
                return $content_object->get_owner_fullname();
            case ContentObject :: PROPERTY_TYPE :
                return $content_object->get_icon_image(Theme :: ICON_MINI);

            case Theme :: getInstance()->getCommonImage(
                'Action/Category',
                'png',
                Translation :: get('Type'),
                null,
                ToolbarItem :: DISPLAY_ICON) :
                return $content_object->get_icon_image(Theme :: ICON_MINI);
            case ContentObject :: PROPERTY_DESCRIPTION :
                return Utilities :: htmlentities(
                    StringUtilities :: getInstance()->truncate($content_object->get_description(), 50));
            case ContentObject :: PROPERTY_MODIFICATION_DATE :
                return DatetimeUtilities :: format_locale_date(
                    Translation :: get('DateTimeFormatLong', null, Utilities :: COMMON_LIBRARIES),
                    $content_object->get_modification_date());
        }

        return parent :: render_cell($column, $content_object);
    }

    public function get_actions($content_object)
    {
        $toolbar = new Toolbar();

        if (RightsService :: getInstance()->canDestroyContentObject($this->get_component()->get_user(), $content_object))
        {
            $remove_url = $this->get_component()->get_content_object_deletion_url($content_object, 'version');
            if ($remove_url)
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES),
                        Theme :: getInstance()->getCommonImagePath('Action/Remove'),
                        $remove_url,
                        ToolbarItem :: DISPLAY_ICON));
            }
            else
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('DeleteNotAvailable', null, Utilities :: COMMON_LIBRARIES),
                        Theme :: getInstance()->getCommonImagePath('Action/RemoveNa'),
                        null,
                        ToolbarItem :: DISPLAY_ICON));
            }
        }

        if (RightsService :: getInstance()->canEditContentObject($this->get_component()->get_user(), $content_object))
        {
            $revert_url = $this->get_component()->get_content_object_revert_url($content_object, 'version');

            if ($revert_url)
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('Revert', null, Utilities :: COMMON_LIBRARIES),
                        Theme :: getInstance()->getCommonImagePath('Action/Revert'),
                        $revert_url,
                        ToolbarItem :: DISPLAY_ICON));
            }
            else
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('RevertNotAvailable', null, Utilities :: COMMON_LIBRARIES),
                        Theme :: getInstance()->getCommonImagePath('Action/RevertNa'),
                        null,
                        ToolbarItem :: DISPLAY_ICON));
            }
        }

        return $toolbar->as_html();
    }
}
