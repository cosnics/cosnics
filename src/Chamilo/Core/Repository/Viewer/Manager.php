<?php
namespace Chamilo\Core\Repository\Viewer;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Core\Repository\Viewer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends Application
{
    // Parameters
    const PARAM_ACTION = 'viewer_action';
    const PARAM_EDIT = 'edit';
    const PARAM_ID = 'viewer_publish_id';
    const PARAM_VIEW_ID = 'viewer_view_id';
    const PARAM_EDIT_ID = 'viewer_edit_id';
    const PARAM_QUERY = 'query';
    const PARAM_CONTENT_OBJECT_TYPE = 'content_object_type';
    const PARAM_CONTENT_OBJECT_TEMPLATE_REGISTRATION_ID = 'template_id';
    const PARAM_PUBLISH_SELECTED = 'viewer_selected';
    const PARAM_IMPORT_TYPE = 'import_type';
    const PARAM_IMPORTED_CONTENT_OBJECT_IDS = 'imported_content_object_ids';
    const PARAM_WORKSPACE_ID = 'workspace_id';
    const PARAM_IN_WORKSPACES = 'in_workspaces';
    const PARAM_TAB = 'tab';

    // Actions
    const ACTION_CREATOR = 'Creator';
    const ACTION_BROWSER = 'Browser';
    const ACTION_VIEWER = 'Viewer';
    const ACTION_IMPORTER = 'Importer';

    const TAB_CREATOR = 'Creator';
    const TAB_BROWSER = 'Browser';
    const TAB_WORKSPACE_BROWSER = 'WorkspaceBrowser';
    const TAB_IMPORTER = 'Importer';
    const TAB_VIEWER = 'Viewer';

    // Default action
    const DEFAULT_ACTION = self::ACTION_CREATOR;

    // Configuration
    const SETTING_TABS_DISABLED = 'tabs_disabled';
    const SETTING_BREADCRUMBS_DISABLED = 'breadcrumbs_disabled';

    /**
     *
     * @var multitype:string
     */
    private $types;

    /**
     *
     * @var multitype:string
     */
    private $actions;

    private $parameters;

    private $maximum_select;

    private $excluded_objects;

    private $tabs;

    private $creation_defaults;

    /**
     * Allow selection of multiple content objects in the viewer
     *
     * @var int
     */
    const SELECT_MULTIPLE = 0;

    /**
     * Allow selection of just one content object in the viewer
     *
     * @var int
     */
    const SELECT_SINGLE = 1;

    /**
     *
     * @param \libraries\architecture\application\Application $parent
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);
        $this->maximum_select = self::SELECT_MULTIPLE;
        $this->default_content_objects = array();
        $this->parameters = array();
        $this->excluded_objects = array();

        $this->set_parameter(
            self::PARAM_ACTION,
            (Request::get(self::PARAM_ACTION) ? Request::get(self::PARAM_ACTION) : self::ACTION_CREATOR)
        );
    }

    public function render_header()
    {
        if ($this->areTabsDisabled())
        {
            return parent::render_header();
        }

        $html = array();

        $html[] = parent::render_header();

        $currentTab = $this->getRequest()->get(self::PARAM_TAB);
        if(empty($currentTab))
        {
            $currentTab = self::TAB_CREATOR;
        }

        $tabs = $this->getTabs();

        $this->tabs = new DynamicVisualTabsRenderer('viewer');

        foreach ($tabs as $tabName => $tabAction)
        {
            $selected = ($currentTab == $tabName ? true : false);

            $label = Translation::get(
                (string) StringUtilities::getInstance()->createString($tabName)->upperCamelize() . 'Title'
            );

            $this->tabs->add_tab(
                new DynamicVisualTab(
                    $tabName,
                    $label,
                    Theme::getInstance()->getImagePath(__NAMESPACE__, 'Tab/' . $tabName),
                    $tabAction,
                    $selected
                )
            );
        }

        $html[] = $this->tabs->header();
        $html[] = $this->tabs->body_header();

        return implode(PHP_EOL, $html);
    }

    public function render_footer()
    {
        if ($this->areTabsDisabled())
        {
            return parent::render_footer();
        }

        $html = array();

        $html[] = $this->tabs->body_footer();
        $html[] = $this->tabs->footer();
        $html[] = parent::render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param int $maximum_select
     */
    public function set_maximum_select($maximum_select)
    {
        $this->maximum_select = $maximum_select;
    }

    /**
     *
     * @return int
     */
    public function get_maximum_select()
    {
        return $this->maximum_select;
    }

    /**
     * Returns the types of content object that the viewer can use.
     *
     * @return multitype:string
     */
    public function get_types()
    {
        return $this->get_application()->get_allowed_content_object_types();
    }

    /**
     *
     * @return multitype:string
     */
    public function get_actions()
    {
        return $this->actions;
    }

    /**
     *
     * @param multitype :string $actions
     */
    public function set_actions($actions)
    {
        $this->actions = $actions;
    }

    /**
     *
     * @return multitype:int
     */
    public function get_excluded_objects()
    {
        return $this->excluded_objects;
    }

    /**
     *
     * @param multitype :int $excluded_objects
     */
    public function set_excluded_objects($excluded_objects)
    {
        $this->excluded_objects = $excluded_objects;
    }

    /**
     *
     * @return boolean
     */
    public function any_object_selected()
    {
        return !is_null(self::get_selected_objects());
    }

    /**
     *
     * @return Ambigous <multitype:int, int>
     */
    public static function get_selected_objects()
    {
        $requestedObjects = Request::get(self::PARAM_ID);

        if (!$requestedObjects)
        {
            $requestedObjects = Request::post(self::PARAM_ID);
        }

        return $requestedObjects;
    }

    public function isReadyToBePublished()
    {
        return $this->getRequest()->get(self::PARAM_ID);
    }

    /**
     *
     * @return boolean @Deprecated any_object_selected()
     */
    public static function is_ready_to_be_published()
    {
        return (self::any_object_selected());
    }

    /**
     *
     * @return boolean
     */
    public function areTabsDisabled()
    {
        return $this->getApplicationConfiguration()->get(self::SETTING_TABS_DISABLED) === true ||
        !$this->isAuthorized(\Chamilo\Core\Repository\Manager::context());
    }

    /**
     *
     * @return boolean
     */
    public function areBreadcrumbsDisabled()
    {
        return $this->getApplicationConfiguration()->get(self::SETTING_BREADCRUMBS_DISABLED) === true;
    }

    /**
     * Returns the breadcrumb generator
     *
     * @return BreadcrumbGenerator
     */
    public function get_breadcrumb_generator()
    {
        return new BreadcrumbGenerator($this, BreadcrumbTrail::getInstance());
    }

    /**
     * Returns a list of the available tabs
     *
     * @return array
     */
    protected function getTabs()
    {
        $tabs = array();

        $tabs[self::TAB_CREATOR] = $this->get_url(
            array(self::PARAM_TAB => self::TAB_CREATOR, self::PARAM_ACTION => self::ACTION_CREATOR)
        );

        $tabs[self::TAB_BROWSER] = $this->get_url(
            array(
                self::PARAM_TAB => self::TAB_BROWSER, self::PARAM_ACTION => self::ACTION_BROWSER,
                self::PARAM_IN_WORKSPACES => false
            )
        );

        $tabs[self::TAB_WORKSPACE_BROWSER] = $this->get_url(
            array(
                self::PARAM_TAB => self::TAB_WORKSPACE_BROWSER, self::PARAM_ACTION => self::ACTION_BROWSER,
                self::PARAM_IN_WORKSPACES => true
            )
        );

        $tabs[self::TAB_IMPORTER] = $this->get_url(
            array(self::PARAM_TAB => self::TAB_IMPORTER, self::PARAM_ACTION => self::ACTION_IMPORTER)
        );

        if ($this->get_action() == self::ACTION_VIEWER)
        {
            $tabs[self::TAB_VIEWER] = $this->get_url(
                array(
                    self::PARAM_TAB => self::TAB_VIEWER, self::PARAM_ACTION => self::ACTION_VIEWER,
                    self::PARAM_ID => $this->getRequest()->get(self::PARAM_ID)
                )
            );
        }

        return $tabs;
    }
}
