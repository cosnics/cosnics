<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\User\PlatformgroupMenuRenderer;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package application.lib.weblcms.tool.user.component
 */
abstract class SubscribeGroupsTabComponent extends Manager implements TableSupport
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    protected $rootButtonToolbarRenderer;

    /**
     *
     * @var ButtonToolBarRenderer
     */
    protected $tabButtonToolbarRenderer;

    /**
     * The currently selected group id
     *
     * @var int
     */
    protected $groupId;

    /**
     * The root group
     *
     * @var Group
     */
    protected $rootGroup;

    /**
     * The current selected group
     *
     * @var Group
     */
    protected $currentGroup;

    /**
     * The subscribed group ids
     *
     * @var int[]
     */
    protected $subscribedGroups;

    /**
     * The translator service
     *
     * @var Translation
     */
    protected $translator;

    /**
     * Runs this component
     *
     * @return string
     *
     * @throws NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function run()
    {
        if (! $this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }

        $this->translator = Translation::getInstance();

        $this->subscribedGroups = $this->get_subscribed_platformgroup_ids($this->get_course_id());
        $this->rootButtonToolbarRenderer = $this->getRootButtonToolbarRenderer();
        $this->tabButtonToolbarRenderer = $this->getTabButtonToolbarRenderer();

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->renderInformationMessage();

        $html[] = $this->rootButtonToolbarRenderer->render();

        $html[] = '<div class="row">';
        $html[] = '<div class="col-sm-4 col-md-3" style="margin-bottom: 20px;">';
        $html[] = $this->renderGroupMenu();
        $html[] = '</div>';
        $html[] = '<div class="col-sm-8 col-md-9">';
        $html[] = $this->renderTabs();
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    protected function renderInformationMessage()
    {
        $html = array();

        $html[] = '<div class="row">';
        $html[] = '<div class="col-sm-12">';

        $html[] = '<div class="alert alert-info">';
        $html[] = $this->getTranslation('SubscribeGroupsInformationMessage');
        $html[] = '</div>';

        $html[] = '</div>';
        $html[] = '</div>';

        return implode("\n", $html);
    }

    /**
     * Renders the group menu
     *
     * @return string
     */
    protected function renderGroupMenu()
    {
        $tree = new PlatformgroupMenuRenderer($this, array($this->getRootGroup()->get_id()));

        return $tree->render_as_tree();
    }

    /**
     * Renders the tabs as HTML
     *
     * @return string
     */
    protected function renderTabs()
    {
        $theme = Theme::getInstance();

        $tabs = new DynamicVisualTabsRenderer('course_groups', $this->renderTabContent());

        $tabs->add_tab(
            new DynamicVisualTab(
                'view_details',
                $this->getCurrentGroup()->get_name(),
                $theme->getCommonImagePath('Action/Details'),
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_SUBSCRIBE_GROUP_DETAILS)),
                get_class($this) == SubscribeGroupsDetailsComponent::class_name()));

        $tabs->add_tab(
            new DynamicVisualTab(
                'view_subgroups',
                $this->getTranslation('BrowseChildren'),
                $theme->getCommonImagePath('Action/Browser'),
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_SUBSCRIBE_GROUP_SUBGROUP_BROWSER)),
                get_class($this) == SubscribeGroupsBrowseSubgroupsComponent::class_name()));

        return $tabs->render();
    }

    /**
     * Helper function to get translations in the current context
     *
     * @param $variable
     * @param array $parameters
     *
     * @return string
     */
    protected function getTranslation($variable, $parameters = array())
    {
        return $this->translator->getTranslation($variable, $parameters, Manager::context());
    }

    /**
     * Builds and returns the tab toolbar renderer
     *
     * @return ButtonToolBarRenderer
     */
    protected function getTabButtonToolbarRenderer()
    {
        if (! isset($this->tabButtonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $this->tabButtonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->tabButtonToolbarRenderer;
    }

    /**
     * Builds and returns the root toolbar renderer
     *
     * @return ButtonToolBarRenderer
     */
    protected function getRootButtonToolbarRenderer()
    {
        if (! isset($this->rootButtonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();

            $buttonToolbar->addItem(
                new Button(
                    Translation::getInstance()->getTranslation(
                        'SubscribeGroupsSearcherComponent',
                        null,
                        Manager::context()),
                    new FontAwesomeGlyph('search'),
                    $this->get_url(array(self::PARAM_ACTION => self::ACTION_SUBSCRIBE_GROUPS_SEARCHER)),
                    Button::DISPLAY_ICON_AND_LABEL,
                    false,
                    'pull-right'));

            $this->rootButtonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->rootButtonToolbarRenderer;
    }

    /**
     * Retrieves the currently selected group
     *
     * @return Group
     */
    protected function getCurrentGroup()
    {
        if (! isset($this->currentGroup))
        {
            $groupId = $this->getGroupId();
            if (! $groupId)
            {
                return null;
            }

            $this->currentGroup = \Chamilo\Core\Group\Storage\DataManager::retrieve_by_id(Group::class_name(), $groupId);
        }

        return $this->currentGroup;
    }

    /**
     * Returns the id of the currently selected group, or the root group
     *
     * @return int
     */
    protected function getGroupId()
    {
        if (! $this->groupId)
        {
            $this->groupId = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_GROUP);

            if (! $this->groupId)
            {
                $this->groupId = $this->getRootGroup()->get_id();
            }
        }

        return $this->groupId;
    }

    /**
     * Retrieves the root group
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function getRootGroup()
    {
        if (! $this->rootGroup)
        {
            $group = \Chamilo\Core\Group\Storage\DataManager::retrieve(
                Group::class_name(),
                new DataClassRetrieveParameters(
                    new EqualityCondition(
                        new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_PARENT_ID),
                        new StaticConditionVariable(0))));
            $this->rootGroup = $group;
        }

        return $this->rootGroup;
    }

    /**
     * Returns additional parameters that need to be registered
     *
     * @return array
     */
    public function get_additional_parameters()
    {
        return array(self::PARAM_TAB, \Chamilo\Application\Weblcms\Manager::PARAM_GROUP);
    }

    /**
     * Renders the tab content
     *
     * @return string
     */
    abstract protected function renderTabContent();
}
