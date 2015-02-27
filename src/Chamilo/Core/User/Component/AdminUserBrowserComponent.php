<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Table\Admin\AdminUserTable;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBarSearchForm;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\ConditionProperty;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * $Id: admin_user_browser.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 *
 * @package user.lib.user_manager.component
 */
class AdminUserBrowserComponent extends Manager implements TableSupport
{

    private $firstletter;

    private $menu_breadcrumbs;

    private $action_bar;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $this->firstletter = Request :: get(self :: PARAM_FIRSTLETTER);

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->get_action_bar()->as_html() . '<br />';
        $html[] = $this->get_user_html();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function get_user_html()
    {
        $table = new AdminUserTable($this);

        $html = array();
        $html[] = '<div style="float: right; width: 100%;">';
        $html[] = $table->as_html();
        $html[] = '</div>';

        return implode($html, "\n");
    }

    public function get_parameters()
    {
        $parameters = parent :: get_parameters();
        if (isset($this->action_bar))
        {
            $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->get_action_bar()->get_query();
        }
        return $parameters;
    }

    /*
     * (non-PHPdoc) @see common\libraries.NewObjectTableSupport::get_object_table_condition()
     */
    public function get_table_condition($class_name)
    {
        // construct search properties
        $search_properties = array();
        $search_properties[] = new ConditionProperty(
            new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_FIRSTNAME));
        $search_properties[] = new ConditionProperty(
            new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_LASTNAME));
        $search_properties[] = new ConditionProperty(
            new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_USERNAME));
        $search_properties[] = new ConditionProperty(
            new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_OFFICIAL_CODE));
        $search_properties[] = new ConditionProperty(
            new PropertyConditionVariable(User :: class_name(), User :: PROPERTY_EMAIL));

        // get conditions
        $condition = $this->get_action_bar()->get_conditions($search_properties);

        return $condition;
    }

    public function get_action_bar()
    {
        if (! isset($this->action_bar))
        {
            $this->action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
            $this->action_bar->set_search_url($this->get_url(parent :: get_parameters()));

            if ($this->get_user()->is_platform_admin())
            {
                $this->action_bar->add_common_action(
                    new ToolbarItem(
                        Translation :: get('Add', null, Utilities :: COMMON_LIBRARIES),
                        Theme :: getInstance()->getCommonImagesPath() . 'action_add.png',
                        $this->get_url(array(Application :: PARAM_ACTION => self :: ACTION_CREATE_USER)),
                        ToolbarItem :: DISPLAY_ICON_AND_LABEL));

                $this->action_bar->add_tool_action(
                    new ToolBarItem(
                        Translation :: get('Report'),
                        Theme :: getInstance()->getCommonImagesPath() . 'action_reporting.png',
                        $this->get_reporting_url(),
                        ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }

            $this->action_bar->add_common_action(
                new ToolbarItem(
                    Translation :: get('Show', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagesPath() . 'action_browser.png',
                    $this->get_url(),
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        return $this->action_bar;
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('user_browser');
    }
}
