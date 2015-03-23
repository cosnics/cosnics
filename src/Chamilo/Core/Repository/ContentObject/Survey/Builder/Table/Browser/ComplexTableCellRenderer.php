<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Builder\Table\Browser;

use Chamilo\Core\Repository\ContentObject\Survey\Builder\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Utilities\StringUtilities;

class ComplexTableCellRenderer extends \Chamilo\Core\Repository\Table\Complex\ComplexTableCellRenderer
{

    // Inherited
    public function render_cell($column, $cloi)
    {
        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_TITLE :

                $title = htmlspecialchars($cloi->get_ref_object()->get_title());
                $title_short = $title;
                $title_short = StringUtilities :: getInstance()->truncate($title_short, 53, false);

                if ($cloi instanceof ComplexContentObjectSupport)
                {
                    $url = Path :: getInstance()->getBasePath(true) . 'index.php?' . Application :: PARAM_APPLICATION .
                         '=' . \Chamilo\Core\Repository\Manager :: context() . '&' . Application :: PARAM_ACTION . '=' .
                         \Chamilo\Core\Repository\Manager :: ACTION_BUILD_COMPLEX_CONTENT_OBJECT . '&' .
                         \Chamilo\Core\Repository\Manager :: PARAM_CONTENT_OBJECT_ID . '=' . $cloi->get_id() . '&' .
                         \Chamilo\Core\Repository\Builder\Manager :: PARAM_POPUP . '=1';

                    $title_short = '<a href="#" onclick="javascript:openPopup(\'' . $url . '\'); return false">' .
                         $title_short . '</a>';
                }
                else
                {
                    $title_short = '<a href="' .
                         $this->get_component()->get_complex_content_object_item_view_url($cloi->get_id()) . '">' .
                         $title_short . '</a>';
                }

                return $title_short;
        }

        return parent :: render_cell($column, $cloi);
    }

    public function get_actions($cloi)
    {
        $toolbar = parent :: get_actions($cloi);
        $parent = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object($cloi->get_parent());

        if ($cloi->is_extended() || $this->browser instanceof Manager)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Edit'),
                    $this->browser->get_complex_content_object_item_edit_url($cloi->get_id()),
                    ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('EditNA', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/EditNa'),
                    null,
                    ToolbarItem :: DISPLAY_ICON));
        }

        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath('Action/Delete'),
                $this->browser->get_complex_content_object_item_delete_url($cloi->get_id()),
                ToolbarItem :: DISPLAY_ICON,
                true));

        $allowed = $this->check_move_allowed($cloi);

        if ($allowed["moveup"])
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('MoveUp', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Uup'),
                    $this->browser->get_complex_content_object_item_move_url(
                        $cloi->get_id(),
                        \Chamilo\Core\Repository\Manager :: PARAM_DIRECTION_UP),
                    ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('MoveUpNA', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/UpNa'),
                    null,
                    ToolbarItem :: DISPLAY_ICON));
        }

        if ($allowed["movedown"])
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('MoveDown', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Down'),
                    $this->browser->get_complex_content_object_item_move_url(
                        $cloi->get_id(),
                        \Chamilo\Core\Repository\Manager :: PARAM_DIRECTION_DOWN),
                    ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('MoveDownNA', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/DownNa'),
                    null,
                    ToolbarItem :: DISPLAY_ICON));
        }

        return $toolbar->as_html();
    }
}
