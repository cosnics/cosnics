<?php
namespace Chamilo\Core\Repository\Share\Component;

use Chamilo\Core\Repository\Share\Manager;
use Chamilo\Core\Repository\Share\Table\Group\GroupRightsTable;
use Chamilo\Core\Repository\Share\Table\User\UserRightsTable;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

/**
 * Browser for content object share rights
 *
 * @author Pieterjan Broekaert
 * @author Sven Vanpoucke
 */
class BrowserComponent extends Manager implements TableSupport
{
    const PARAM_TYPE = 'type';
    const TAB_DETAILS = 0;
    const TAB_SUBGROUPS = 1;
    const TYPE_USER = 'user';
    const TYPE_GROUP = 'group';

    private $type;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->type = Request :: get(self :: PARAM_TYPE);
        if (empty($this->type))
        {
            $this->type = self :: TYPE_USER;
        }

        $this->action_bar = $this->get_action_bar();

        // display the component
        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->action_bar->as_html();
        $html[] = $this->display_content_objects();
        $html[] = $this->display_body();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Displays the body
     */
    private function display_body()
    {
        switch ($this->type)
        {
            case self :: TYPE_USER :
                $table = $this->get_users_browser_html();
                break;
            case self :: TYPE_GROUP :
                $table = $this->get_groups_browser_html();
                break;
            default :
                $table = '';
                break;
        }

        $renderer_name = ClassnameUtilities :: getInstance()->getClassNameFromNamespace(__CLASS__, true);
        $tabs = new DynamicVisualTabsRenderer($renderer_name, $table);

        $label = htmlentities(Translation :: get('Users', null, \Chamilo\Core\User\Manager :: context()));
        $link = $this->get_url(array(self :: PARAM_TYPE => self :: TYPE_USER));
        $tabs->add_tab(
            new DynamicVisualTab(
                'users',
                $label,
                Theme :: getInstance()->getImagePath(
                    \Chamilo\Core\User\Manager :: context(),
                    'Logo/' . Theme :: ICON_SMALL),
                $link,
                ($this->type == self :: TYPE_USER)));

        $label = htmlentities(Translation :: get('Groups', null, \Chamilo\Core\Group\Manager :: context()));
        $link = $this->get_url(array(self :: PARAM_TYPE => self :: TYPE_GROUP));
        $tabs->add_tab(
            new DynamicVisualTab(
                'users',
                $label,
                Theme :: getInstance()->getImagePath(
                    \Chamilo\Core\Group\Manager :: context(),
                    'Logo/' . Theme :: ICON_SMALL),
                $link,
                ($this->type == self :: TYPE_GROUP)));

        return $tabs->render();
    }

    /**
     * Displays the users you shared with and the rights
     */
    private function get_users_browser_html()
    {
        $browser_table = new UserRightsTable($this);
        return $browser_table->as_html();
    }

    /**
     * Displays the groups you shared with and the rights
     */
    private function get_groups_browser_html()
    {
        $browser_table = new GroupRightsTable($this);

        return $browser_table->as_html();
    }

    /**
     * create an action bar
     */
    public function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->add_common_action(
            new ToolbarItem(
                Translation :: get('ShareWithOtherUsersGroups'),
                Theme :: getInstance()->getCommonImagePath('action_rights'),
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_ADD_ENTITIES))),
            ToolbarItem :: DISPLAY_ICON_AND_LABEL);

        return $action_bar;
    }

    /**
     * add additional breadcrumbs before the auto generated share_rights_browser breadcrumb
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help("repository_content_object_share_rights_browser");
    }

    public function get_additional_parameters()
    {
        return array(self :: PARAM_TYPE, \Chamilo\Core\Repository\Manager :: PARAM_CONTENT_OBJECT_ID);
    }

    /*
     * (non-PHPdoc) @see \libraries\format\TableSupport::get_table_condition()
     */
    public function get_table_condition($table_class_name)
    {
        return null;
    }
}
