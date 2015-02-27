<?php
namespace Chamilo\Core\Home\Renderer\Type;

use Chamilo\Core\Home\BlockRendition;
use Chamilo\Core\Home\Manager;
use Chamilo\Core\Home\Renderer\Renderer;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Home\Storage\DataClass\Column;
use Chamilo\Core\Home\Storage\DataClass\Row;
use Chamilo\Core\Home\Storage\DataClass\Tab;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Cache\DataClassCache;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\ResultSet\ResultSet;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Home\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Basic extends Renderer
{

    /**
     *
     * @return string
     */
    public function render()
    {
        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->render_homepage();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function render_homepage()
    {
        $current_tab = $this->get_current_tab();
        $user = $this->get_user();
        $user_home_allowed = PlatformSetting :: get('allow_user_home', Manager :: context());
        $general_mode = \Chamilo\Libraries\Platform\Session\Session :: retrieve(__NAMESPACE__ . '\general');

        // Get user id
        if ($user instanceof User && $general_mode && $user->is_platform_admin())
        {
            $user_id = '0';
        }
        elseif ($user_home_allowed && $user instanceof User)
        {
            $user_id = $user->get_id();
        }
        else
        {
            $user_id = '0';
        }

        if (($general_mode && $user instanceof User && $user->is_platform_admin()))
        {
            $html[] = '<div class="general_mode">' . Translation :: get('HomepageInGeneralMode') . '</div>';
        }

        $tabs_condition = new EqualityCondition(
            new PropertyConditionVariable(Tab :: class_name(), Tab :: PROPERTY_USER),
            new StaticConditionVariable($user_id));
        $parameters = new DataClassRetrievesParameters(
            $tabs_condition,
            null,
            null,
            array(new OrderBy(new PropertyConditionVariable(Tab :: class_name(), Tab :: PROPERTY_SORT))));
        $tabs = DataManager :: retrieves(Tab :: class_name(), $parameters);

        // If the homepage can be personalised but we have no rows, get the
        // default (to prevent lockouts) and display a warning / notification
        // which tells the user he can personalise his homepage
        if ($user_home_allowed && $user instanceof User && $tabs->size() == 0)
        {
            $this->create_user_home();

            $tabs_condition = new EqualityCondition(
                new PropertyConditionVariable(Tab :: class_name(), Tab :: PROPERTY_USER),
                new StaticConditionVariable($user->get_id()));
            $parameters = new DataClassRetrievesParameters(
                $tabs_condition,
                null,
                null,
                array(new OrderBy(new PropertyConditionVariable(Tab :: class_name(), Tab :: PROPERTY_SORT))));
            $tabs = DataManager :: retrieves(Tab :: class_name(), $parameters);
        }

        $html[] = '<div id="tab_menu"><ul id="tab_elements">';
        while ($tab = $tabs->next_result())
        {
            $tab_id = $tab->get_id();

            if (($tab_id == $current_tab) || ($tabs->position() == ResultSet :: POSITION_SINGLE) ||
                 (! isset($current_tab) && $tabs->position() == ResultSet :: POSITION_FIRST))
            {
                $class = 'current';
            }
            else
            {
                $class = 'normal';
            }

            $html[] = '<li class="' . $class . '" id="tab_select_' . $tab->get_id() . '"><a class="tabTitle" href="' .
                 htmlspecialchars($this->get_home_tab_viewing_url($tab)) . '">' . htmlspecialchars($tab->get_title()) .
                 '</a>';

            $isUser = $this->get_user() instanceof User;
            $homeAllowed = ($user_home_allowed || ($this->get_user()->is_platform_admin()) && $general_mode);
            $isAnonymous = $isUser && $this->get_user()->is_anonymous_user();

            if ($isUser && $homeAllowed && ! $isAnonymous)
            {
                $html[] = '<a class="deleteTab"><img src="' .
                     htmlspecialchars(Theme :: getInstance()->getImagesPath('Chamilo\Core\Home')) .
                     'action_delete_tab.png" /></a>';
            }

            $html[] = '</li>';
        }
        $html[] = '</ul>';

        if ($user instanceof User && ($user_home_allowed || $user->is_platform_admin()))
        {

            $style = (! $user_home_allowed && ! $general_mode && $user->is_platform_admin()) ? ' style="display:block;"' : '';

            $html[] = '<div id="tab_actions" ' . $style . '>';

            if ($user_home_allowed || $general_mode)
            {
                $html[] = '<a class="addTab" href="#"><img src="' .
                     htmlspecialchars(Theme :: getInstance()->getImagesPath('Chamilo\Core\Home')) .
                     'action_add_tab.png" />&nbsp;' . htmlspecialchars(Translation :: get('NewTab')) . '</a>';
                $html[] = '<a class="addColumn" href="#"><img src="' .
                     htmlspecialchars(Theme :: getInstance()->getImagesPath('Chamilo\Core\Home')) .
                     'action_add_column.png" />&nbsp;' . htmlspecialchars(Translation :: get('NewColumn')) . '</a>';
                $html[] = '<a class="addEl" href="#"><img src="' .
                     htmlspecialchars(Theme :: getInstance()->getImagesPath('Chamilo\Core\Home')) .
                     'action_add_block.png" />&nbsp;' . htmlspecialchars(Translation :: get('NewBlock')) . '</a>';

                $reset_url = Redirect :: get_link(
                    array(Manager :: PARAM_ACTION => Manager :: ACTION_TRUNCATE),
                    array(),
                    false,
                    Redirect :: TYPE_CORE);

                if ($user_id != '0')
                {
                    $html[] = '<a onclick="return confirm(\'' .
                         Translation :: get('Confirm', null, Utilities :: COMMON_LIBRARIES) . '\');" href="' . $reset_url .
                         '"><img src="' . htmlspecialchars(Theme :: getInstance()->getImagesPath('Chamilo\Core\Home')) .
                         'action_reset.png" />&nbsp;' . htmlspecialchars(Translation :: get('ResetHomepage')) . '</a>';
                }
            }

            if (! $general_mode && $user->is_platform_admin())
            {
                $manage_url = Redirect :: get_link(
                    array(Manager :: PARAM_ACTION => Manager :: ACTION_MANAGE_HOME),
                    array(),
                    false,
                    Redirect :: TYPE_CORE);

                $html[] = '<a href="' . $manage_url . '"><img src="' .
                     htmlspecialchars(Theme :: getInstance()->getImagesPath('Chamilo\Core\Home')) .
                     'action_configure.png" />&nbsp;' . htmlspecialchars(Translation :: get('ConfigureDefault')) . '</a>';
            }
            elseif ($general_mode && $user->is_platform_admin())
            {
                $personal_url = Redirect :: get_link(
                    array(Manager :: PARAM_ACTION => Manager :: ACTION_PERSONAL),
                    array(),
                    false,
                    Redirect :: TYPE_CORE);

                $title = $user_home_allowed ? 'BackToPersonal' : 'ViewDefault';

                $html[] = '<a href="' . $personal_url . '"><img src="' .
                     htmlspecialchars(Theme :: getInstance()->getImagesPath('Chamilo\Core\Home')) .
                     'action_home.png" />&nbsp;' . htmlspecialchars(Translation :: get($title)) . '</a>';
            }

            $html[] = '</div>';
        }

        $html[] = '<div style="font-size: 0px; clear: both; height: 0px; line-height: 0px;">&nbsp;</div>';
        $html[] = '</div>';
        $html[] = '<div style="clear: both; height: 0px; line-height: 0px;">&nbsp;</div>';

        $tabs->reset();

        while ($tab = $tabs->next_result())
        {
            $html[] = '<div class="portal_tab" id="portal_tab_' . $tab->get_id() . '" style="display: ' . (((! isset(
                $current_tab) &&
                 ($tabs->position() == ResultSet :: POSITION_FIRST || $tabs->position() == ResultSet :: POSITION_SINGLE)) ||
                 $current_tab == $tab->get_id()) ? 'block' : 'none') . ';">';

            $rows_conditions = array();
            $rows_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Row :: class_name(), Row :: PROPERTY_TAB),
                new StaticConditionVariable($tab->get_id()));
            $rows_condition = new AndCondition($rows_conditions);
            $parameters = new DataClassRetrievesParameters(
                $rows_condition,
                null,
                null,
                array(new OrderBy(new PropertyConditionVariable(Row :: class_name(), Row :: PROPERTY_SORT))));
            $rows = DataManager :: retrieves(Row :: class_name(), $parameters);

            while ($row = $rows->next_result())
            {
                $rows_position = $rows->position();
                $html[] = '<div class="portal_row" id="portal_row_' . $row->get_id() . '" style="' .
                     ($rows_position != ResultSet :: POSITION_LAST ? 'margin-bottom: 1%;' : '') . '">';

                $conditions = array();
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Column :: class_name(), Column :: PROPERTY_ROW),
                    new StaticConditionVariable($row->get_id()));
                $condition = new AndCondition($conditions);

                // Get the user or platform columns
                $parameters = new DataClassRetrievesParameters(
                    $condition,
                    null,
                    null,
                    array(new OrderBy(new PropertyConditionVariable(Column :: class_name(), Column :: PROPERTY_SORT))));
                $columns = DataManager :: retrieves(Column :: class_name(), $parameters);

                while ($column = $columns->next_result())
                {
                    $columns_position = $columns->position();

                    $html[] = '<div class="portal_column" id="portal_column_' . $column->get_id() . '" style="width: ' .
                         $column->get_width() . '%;' .
                         ($columns_position != ResultSet :: POSITION_LAST ? ' margin-right: 1%;' : '') . '">';

                    $conditions = array();
                    $conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(Block :: class_name(), Block :: PROPERTY_COLUMN),
                        new StaticConditionVariable($column->get_id()));
                    $condition = new AndCondition($conditions);

                    $parameters = new DataClassRetrievesParameters(
                        $condition,
                        null,
                        null,
                        array(new OrderBy(new PropertyConditionVariable(Block :: class_name(), Block :: PROPERTY_SORT))));
                    $blocks = DataManager :: retrieves(Block :: class_name(), $parameters);

                    while ($block = $blocks->next_result())
                    {
                        $block_component = BlockRendition :: factory($this, $block);
                        if ($block_component->is_visible())
                        {
                            $html[] = $block_component->as_html();
                        }
                    }

                    $footer_style = ($blocks->size() > 0) ? 'style="display:none;"' : '';
                    $html[] = '<div class="empty_portal_column" ' . $footer_style . '>';
                    $html[] = htmlspecialchars(Translation :: get('EmptyColumnText'));
                    $html[] = '<div class="deleteColumn"><a href="#"><img src="' .
                         htmlspecialchars(Theme :: getInstance()->getImagesPath('Chamilo\Core\Home')) .
                         'action_remove_column.png" /></a></div>';
                    $html[] = '<div style="clear:both"></div>';
                    $html[] = '</div>';

                    $html[] = '</div>';
                }

                $html[] = '</div>';
                $html[] = '<div style="clear: both; height: 0px; line-height: 0px;">&nbsp;</div>';
            }

            $html[] = '</div>';
        }

        $html[] = '<div style="clear: both; height: 0px; line-height: 0px;">&nbsp;</div>';

        if ($user instanceof User && ($user_home_allowed || ($user->is_platform_admin() && $general_mode)))
        {
            $html[] = '<script type="text/javascript" src="' .
                 Path :: getInstance()->namespaceToFullPath('Chamilo\Core\Home', true) .
                 'Resources/Javascript/HomeAjax.js' . '"></script>';
        }

        return implode(PHP_EOL, $html);
    }

    public function create_user_home()
    {
        $user = $this->get_user();

        $tabs_condition = new EqualityCondition(
            new PropertyConditionVariable(Tab :: class_name(), Tab :: PROPERTY_USER),
            new StaticConditionVariable('0'));
        $tabs = DataManager :: retrieves(Tab :: class_name(), $tabs_condition);

        while ($tab = $tabs->next_result())
        {
            $old_tab_id = $tab->get_id();
            $tab->set_user($user->get_id());
            $tab->create();

            $rows_conditions = array();
            $rows_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Row :: class_name(), Row :: PROPERTY_TAB),
                new StaticConditionVariable($old_tab_id));
            $rows_condition = new AndCondition($rows_conditions);
            $rows = DataManager :: retrieves(Row :: class_name(), $rows_condition);

            while ($row = $rows->next_result())
            {
                $old_row_id = $row->get_id();
                $row->set_user($user->get_id());
                $row->set_tab($tab->get_id());
                $row->create();

                $conditions = array();
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Column :: class_name(), Column :: PROPERTY_ROW),
                    new StaticConditionVariable($old_row_id));
                $condition = new AndCondition($conditions);

                $columns = DataManager :: retrieves(Column :: class_name(), $condition);

                while ($column = $columns->next_result())
                {
                    $old_column_id = $column->get_id();
                    $column->set_user($user->get_id());
                    $column->set_row($row->get_id());
                    $column->create();

                    $conditions = array();
                    $conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(Block :: class_name(), Block :: PROPERTY_COLUMN),
                        new StaticConditionVariable($old_column_id));
                    $condition = new AndCondition($conditions);

                    $blocks = DataManager :: retrieves(Block :: class_name(), $condition);

                    while ($block = $blocks->next_result())
                    {
                        $block->set_user($user->get_id());
                        $block->set_column($column->get_id());
                        $block->create();
                    }
                }
            }
        }

        DataClassCache :: truncate(Tab :: class_name());
    }
}
