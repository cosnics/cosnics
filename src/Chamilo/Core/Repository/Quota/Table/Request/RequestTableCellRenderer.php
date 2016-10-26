<?php
namespace Chamilo\Core\Repository\Quota\Table\Request;

use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Core\Repository\Quota\Manager;
use Chamilo\Core\Repository\Quota\Storage\DataClass\Request;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class RequestTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    public function render_cell($column, $object)
    {
        $calculator = new Calculator($object->get_user());

        switch ($column->get_name())
        {
            case Translation :: get('User') :
                return $object->get_user()->get_fullname();
            case Request :: PROPERTY_QUOTA :
                return Filesystem :: format_file_size($object->get_quota());
            case Request :: PROPERTY_CREATION_DATE :
                return DatetimeUtilities :: format_locale_date(null, $object->get_creation_date());
            case Request :: PROPERTY_DECISION_DATE :
                return DatetimeUtilities :: format_locale_date(null, $object->get_decision_date());
            case Request :: PROPERTY_DECISION :
                return $object->get_decision_icon();
            case Translation :: get('UsedDiskSpace') :
                return Filesystem :: format_file_size($calculator->getUsedUserDiskQuota());
            case Translation :: get('MaximumUsedDiskSpace') :
                return Filesystem :: format_file_size($calculator->getMaximumUserDiskQuota());
        }
        return parent :: render_cell($column, $object);
    }

    public function get_actions($object)
    {
        $toolbar = new Toolbar();

        if (\Chamilo\Core\Repository\Quota\Rights\Rights :: get_instance()->quota_is_allowed())
        {
            if (! $object->was_granted() && (\Chamilo\Core\Repository\Quota\Rights\Rights :: get_instance()->is_target_user(
                $this->get_component()->get_user(),
                $object->get_user_id()) || $this->get_component()->get_user()->is_platform_admin()))
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('Grant'),
                        Theme :: getInstance()->getImagePath('Chamilo\Core\Repository\Quota', 'Action/Grant'),
                        $this->get_component()->get_url(
                            array(
                                Manager :: PARAM_ACTION => Manager :: ACTION_GRANT,
                                Manager :: PARAM_REQUEST_ID => $object->get_id())),
                        ToolbarItem :: DISPLAY_ICON));
            }

            if ($object->is_pending() && (\Chamilo\Core\Repository\Quota\Rights\Rights :: get_instance()->is_target_user(
                $this->get_component()->get_user(),
                $object->get_user_id()) || $this->get_component()->get_user()->is_platform_admin()))
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('Deny'),
                        Theme :: getInstance()->getImagePath('Chamilo\Core\Repository\Quota', 'Action/Deny'),
                        $this->get_component()->get_url(
                            array(
                                Manager :: PARAM_ACTION => Manager :: ACTION_DENY,
                                Manager :: PARAM_REQUEST_ID => $object->get_id())),
                        ToolbarItem :: DISPLAY_ICON));
            }
        }

        if ($this->get_component()->get_user()->is_platform_admin() ||
             ($this->get_component()->get_user_id() == $object->get_user_id() && $object->is_pending()))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Delete'),
                    $this->get_component()->get_url(
                        array(
                            Manager :: PARAM_ACTION => Manager :: ACTION_DELETE,
                            Manager :: PARAM_REQUEST_ID => $object->get_id())),
                    ToolbarItem :: DISPLAY_ICON));
        }

        return $toolbar->as_html();
    }
}
