<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\Group;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Core\Group\Storage\DataClass\GroupRelUser;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

/**
 * *************************************************************************** Cell renderer for a platform group rel
 * user browser table.
 *
 * @author Stijn Van Hoecke ****************************************************************************
 */
class PlatformGroupRelUserTableCellRenderer extends DataClassTableCellRenderer
    implements TableCellRendererActionsColumnSupport
{

    public function get_actions($groupreluser)
    {
        // construct the toolbar
        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);

        // always show details
        $parameters = [];
        $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] = Manager::ACTION_USER_DETAILS;
        $parameters[Manager::PARAM_TAB] = Request::get(Manager::PARAM_TAB);
        $parameters[Manager::PARAM_OBJECTS] = $groupreluser->get_user_id();
        $details_url = $this->get_component()->get_url($parameters);

        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Details'), new FontAwesomeGlyph('info-circle'), $details_url,
                ToolbarItem::DISPLAY_ICON
            )
        );

        // if we have editing rights, display the reporting action but never
        // allow unsubscribe
        if ($this->get_component()->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('UnsubscribeNotAvailableForGroups'),
                    new FontAwesomeGlyph('minus-square', array('text-muted')), null, ToolbarItem::DISPLAY_ICON
                )
            );

            $params = [];
            $params[Manager::PARAM_OBJECTS] = $groupreluser->get_user_id();
            $params[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] = Manager::ACTION_REPORTING;
            $parameters[Manager::PARAM_TAB] = Request::get(Manager::PARAM_TAB);
            $reporting_url = $this->get_component()->get_url($params);

            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Report'), new FontAwesomeGlyph('chart-pie'), $reporting_url,
                    ToolbarItem::DISPLAY_ICON
                )
            );
        }

        // return
        return $toolbar->as_html();
    }

    public function render_cell($column, $groupreluser)
    {
        switch ($column->get_name())
        {
            case GroupRelUser::PROPERTY_USER_ID :
                $user_id = parent::render_cell($column, $groupreluser);
                $user = DataManager::retrieve_by_id(User::class, $user_id);

                return $user->get_fullname();
        }

        return parent::render_cell($column, $groupreluser);
    }
}
