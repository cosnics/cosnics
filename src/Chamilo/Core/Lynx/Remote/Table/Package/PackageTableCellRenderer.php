<?php
namespace Chamilo\Core\Lynx\Remote\Table\Package;

use Chamilo\Core\Lynx\Remote\Manager;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

class PackageTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    /**
     * Returns the actions toolbar
     *
     * @param $course Course
     * @return String
     */
    public function get_actions($package)
    {
        $toolbar = new Toolbar();

        if ($package->is_downloadable())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Download'),
                    Theme::getInstance()->getCommonImagePath('Action/Download'),
                    $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_DOWNLOAD,
                            Manager::PARAM_PACKAGE_ID => $package->get_id())),
                    ToolbarItem::DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('DownloadNotAvailable'),
                    Theme::getInstance()->getCommonImagePath('Action/DownloadNa'),
                    null,
                    ToolbarItem::DISPLAY_ICON));
        }

        return $toolbar->as_html();
    }
}
