<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\User\PlatformgroupMenuRenderer;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Tabs\Link\LinkTab;
use Chamilo\Libraries\Format\Tabs\Link\LinkTabsRenderer;
use Chamilo\Libraries\Format\Tabs\TabsCollection;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package application.lib.weblcms.tool.user.component
 */
abstract class SubscribeGroupsTabComponent extends Manager implements TableSupport
{

    /**
     * The current selected group
     *
     * @var Group
     */
    protected $currentGroup;

    /**
     * The currently selected group id
     *
     * @var int
     */
    protected $groupId;

    /**
     *
     * @var ButtonToolBarRenderer
     */
    protected $rootButtonToolbarRenderer;

    /**
     * The root group
     *
     * @var Group
     */
    protected $rootGroup;

    /**
     * The subscribed group ids
     *
     * @var int[]
     */
    protected $subscribedGroups;

    /**
     *
     * @var ButtonToolBarRenderer
     */
    protected $tabButtonToolbarRenderer;

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
        if (!$this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }

        $this->translator = Translation::getInstance();

        $this->subscribedGroups = $this->get_subscribed_platformgroup_ids($this->get_course_id());
        $this->rootButtonToolbarRenderer = $this->getRootButtonToolbarRenderer();
        $this->tabButtonToolbarRenderer = $this->getTabButtonToolbarRenderer();

        $html = [];

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

    /**
     * Returns additional parameters that need to be registered
     *
     * @return array
     */
    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = self::PARAM_TAB;
        $additionalParameters[] = \Chamilo\Application\Weblcms\Manager::PARAM_GROUP;

        return parent::getAdditionalParameters($additionalParameters);
    }

    /**
     * Retrieves the currently selected group
     *
     * @return Group
     */
    protected function getCurrentGroup()
    {
        if (!isset($this->currentGroup))
        {
            $groupId = $this->getGroupId();
            if (!$groupId)
            {
                return null;
            }

            $this->currentGroup = DataManager::retrieve_by_id(Group::class, $groupId);
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
        if (!$this->groupId)
        {
            $this->groupId = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_GROUP);

            if (!$this->groupId)
            {
                $this->groupId = $this->getRootGroup()->get_id();
            }
        }

        return $this->groupId;
    }

    public function getLinkTabsRenderer(): LinkTabsRenderer
    {
        return $this->getService(LinkTabsRenderer::class);
    }

    /**
     * Builds and returns the root toolbar renderer
     *
     * @return ButtonToolBarRenderer
     */
    protected function getRootButtonToolbarRenderer()
    {
        if (!isset($this->rootButtonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();

            $buttonToolbar->addItem(
                new Button(
                    Translation::getInstance()->getTranslation(
                        'SubscribeGroupsSearcherComponent', null, Manager::context()
                    ), new FontAwesomeGlyph('search'),
                    $this->get_url(array(self::PARAM_ACTION => self::ACTION_SUBSCRIBE_GROUPS_SEARCHER)),
                    Button::DISPLAY_ICON_AND_LABEL, null, ['pull-right']
                )
            );

            $this->rootButtonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->rootButtonToolbarRenderer;
    }

    /**
     * Retrieves the root group
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function getRootGroup()
    {
        if (!$this->rootGroup)
        {
            $group = DataManager::retrieve(
                Group::class, new DataClassRetrieveParameters(
                    new EqualityCondition(
                        new PropertyConditionVariable(Group::class, Group::PROPERTY_PARENT_ID),
                        new StaticConditionVariable(0)
                    )
                )
            );
            $this->rootGroup = $group;
        }

        return $this->rootGroup;
    }

    /**
     * Builds and returns the tab toolbar renderer
     *
     * @return ButtonToolBarRenderer
     */
    protected function getTabButtonToolbarRenderer()
    {
        if (!isset($this->tabButtonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $this->tabButtonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->tabButtonToolbarRenderer;
    }

    /**
     * Helper function to get translations in the current context
     *
     * @param $variable
     * @param array $parameters
     *
     * @return string
     */
    protected function getTranslation($variable, $parameters = [])
    {
        return $this->translator->getTranslation($variable, $parameters, Manager::context());
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

    protected function renderInformationMessage()
    {
        $html = [];

        $html[] = '<div class="row">';
        $html[] = '<div class="col-sm-12">';

        $html[] = '<div class="alert alert-info">';
        $html[] = $this->getTranslation('SubscribeGroupsInformationMessage');
        $html[] = '</div>';

        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * Renders the tab content
     *
     * @return string
     */
    abstract protected function renderTabContent();

    /**
     * Renders the tabs as HTML
     *
     * @return string
     */
    protected function renderTabs()
    {
        $tabs = new TabsCollection();

        $tabs->add(
            new LinkTab(
                'view_details', $this->getCurrentGroup()->get_name(), new FontAwesomeGlyph('info-circle'),
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_SUBSCRIBE_GROUP_DETAILS)),
                get_class($this) == SubscribeGroupsDetailsComponent::class
            )
        );

        $tabs->add(
            new LinkTab(
                'view_subgroups', $this->getTranslation('BrowseChildren'), new FontAwesomeGlyph('folder'),
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_SUBSCRIBE_GROUP_SUBGROUP_BROWSER)),
                get_class($this) == SubscribeGroupsBrowseSubgroupsComponent::class
            )
        );

        return $this->getLinkTabsRenderer()->render($tabs, $this->renderTabContent());
    }
}
