<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Table\Admin\AdminUserTable;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\ActionBarSearchForm;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package user.lib.user_manager.component
 */
class AdminUserBrowserComponent extends Manager implements TableSupport
{

    private $firstletter;

    private $menu_breadcrumbs;

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::context(), 'ManageUsers');

        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        if (!$this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $this->firstletter = Request::get(self::PARAM_FIRSTLETTER);

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->buttonToolbarRenderer->render() . '<br />';
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
        $parameters = parent::get_parameters();
        if (isset($this->buttonToolbarRenderer))
        {
            $parameters[ActionBarSearchForm::PARAM_SIMPLE_SEARCH_QUERY] =
                $this->buttonToolbarRenderer->getSearchForm()->getQuery();
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
        $search_properties[] = new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME);
        $search_properties[] = new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME);
        $search_properties[] = new PropertyConditionVariable(User::class_name(), User::PROPERTY_USERNAME);
        $search_properties[] = new PropertyConditionVariable(User::class_name(), User::PROPERTY_OFFICIAL_CODE);
        $search_properties[] = new PropertyConditionVariable(User::class_name(), User::PROPERTY_EMAIL);

        // get conditions
        return $this->buttonToolbarRenderer->getConditions($search_properties);
    }

    public function getButtonToolbarRenderer()
    {
        if (!isset($this->buttonToolbarRenderer))
        {

            $buttonToolbar = new ButtonToolBar($this->get_url(parent::get_parameters()));
            $commonActions = new ButtonGroup();

            if ($this->get_user()->is_platform_admin())
            {
                $commonActions->addButton(
                    new Button(
                        Translation::get('Add', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('plus'),
                        $this->get_url(array(Application::PARAM_ACTION => self::ACTION_CREATE_USER)),
                        ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );

                $commonActions->addButton(
                    new Button(
                        new FontAwesomeGlyph('chart-pie', array(), null, 'fas'),
                        $this->get_reporting_url(), ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );
            }

            $commonActions->addButton(
                new Button(
                    Translation::get('Show', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('folder'),
                    $this->get_url(), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $buttonToolbar->addButtonGroup($commonActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('user_browser');
    }
}
