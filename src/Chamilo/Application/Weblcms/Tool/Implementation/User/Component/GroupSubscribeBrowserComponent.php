<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component;

use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Component\UnsubscribedGroup\UnsubscribedGroupTable;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\User\PlatformgroupMenuRenderer;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package application.lib.weblcms.tool.user.component
 */
class GroupSubscribeBrowserComponent extends Manager implements TableSupport
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    /**
     * The currently selected group id
     *
     * @var int
     */
    private $groupId;

    /**
     * The root group
     *
     * @var Group
     */
    private $rootGroup;

    /**
     * The subscribed group ids
     *
     * @var int[]
     */
    private $subscribedGroups;

    /**
     * The translator service
     *
     * @var Translation
     */
    private $translator;

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
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->buttonToolbarRenderer->render();

        $html[] = $this->renderInformationMessage();

        $html[] = '<div class="row">';
        $html[] = $this->renderGroupMenu();
        $html[] = $this->renderCurrentGroup();
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
     * Renders the currently selected group
     *
     * @return string
     */
    protected function renderCurrentGroup()
    {
        $html = array();

        $html[] = '<div class="col-sm-10">';
        $html[] = $this->renderGroupDetails();
        $html[] = $this->renderGroupSubgroupTable();

        $html[] = '</div>';

        return implode("\n", $html);
    }

    /**
     * Renders the details for the currently selected group
     */
    protected function renderGroupDetails()
    {
        $group = $this->getCurrentGroup();

        $html = array();

        $html[] = '<div class="panel panel-default">';
        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">' . $group->get_name() . '</h3>';
        $html[] = '</div>';
        $html[] = '<div class="panel-body">';
        $html[] = '<div class="row">';
        $html[] = '<div class="col-sm-12">';
        $html[] = '<b>' . $this->getTranslation('Code') . ':</b> ' . $group->get_code();
        $html[] = '<br /><b>' . $this->getTranslation('Description') . ':</b> ' . $group->get_fully_qualified_name();
        $html[] = '<br /><b>' . $this->getTranslation('NumberOfUsers') . ':</b> ' . $group->count_users();
        $html[] = '<br /><b>' . $this->getTranslation('NumberOfSubgroups') . ':</b> ' . $group->count_subgroups(true);
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<div class="row">';
        $html[] = '<div class="col-sm-12">';
        $html[] = '<div style="margin-top: 20px;">';
        $html[] = $this->getGroupButtonToolbarRenderer($group)->render();
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        return implode("\n", $html);
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
     * Renders the table of subgroups
     */
    protected function renderGroupSubgroupTable()
    {
        $table = new UnsubscribedGroupTable($this, $this->get_parameters(), $this->get_table_condition(''));

        $html = array();
        $html[] = '<div class="panel panel-default">';
        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">' . $this->getTranslation('Subgroups') . '</h3>';
        $html[] = '</div>';
        $html[] = '<div class="panel-body">';
        $html[] = $table->as_html();
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

        $html = array();
        $html[] = '<div class="col-sm-2">';
        $html[] = $tree->render_as_tree();
        $html[] = '</div>';

        return implode("\n", $html);
    }

    /**
     * Builds and returns the toolbar renderer
     *
     * @return ButtonToolBarRenderer
     */
    protected function getButtonToolbarRenderer()
    {
        if (! isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $commonActions = new ButtonGroup();

            $commonActions->addButton(
                new Button(
                    Translation::get('ViewSubscribedUsers'),
                    Theme::getInstance()->getCommonImagePath('Action/Browser'),
                    $this->get_url(array(self::PARAM_ACTION => self::ACTION_UNSUBSCRIBE_BROWSER)),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL));

            $buttonToolbar->addButtonGroup($commonActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    /**
     * Builds the group button toolbar for the management of a single group
     *
     * @param Group $group
     *
     * @return ButtonToolBarRenderer
     */
    protected function getGroupButtonToolbarRenderer(Group $group)
    {
        $buttonToolbar = new ButtonToolBar();

        $courseManagementRights = CourseManagementRights::getInstance();

        $isAllowed = $courseManagementRights->is_allowed_for_platform_group(
            CourseManagementRights::TEACHER_DIRECT_SUBSCRIBE_RIGHT,
            $group->getId(),
            $this->get_course_id());

        if (! in_array($group->getId(), $this->subscribedGroups) && $isAllowed)
        {
            $buttonToolbar->addItem(
                new Button(
                    $this->getTranslation('SubscribeGroup'),
                    '',
                    $this->get_url(
                        array(
                            self::PARAM_ACTION => self::ACTION_SUBSCRIBE_GROUPS,
                            self::PARAM_OBJECTS => $group->getId())),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL,
                    false,
                    'btn-success'));
        }

        return new ButtonToolBarRenderer($buttonToolbar);
    }

    /**
     * Retrieves the currently selected group
     *
     * @return Group
     */
    protected function getCurrentGroup()
    {
        $groupId = $this->getGroupId();
        if (! $groupId)
        {
            return null;
        }

        return \Chamilo\Core\Group\Storage\DataManager::retrieve_by_id(Group::class_name(), $groupId);
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
     * Returns the condition for the table
     *
     * @param string $table_class_name
     *
     * @return Condition
     */
    public function get_table_condition($table_class_name)
    {
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_PARENT_ID),
            new StaticConditionVariable($this->getGroupId()));

        // filter already subscribed groups
        if ($this->subscribedGroups)
        {
            $conditions[] = new NotCondition(
                new InCondition(
                    new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_ID),
                    $this->subscribedGroups));
        }

        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();
        if (isset($query) && $query != '')
        {
            $conditions2[] = new PatternMatchCondition(
                new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_NAME),
                '*' . $query . '*');
            $conditions2[] = new PatternMatchCondition(
                new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_DESCRIPTION),
                '*' . $query . '*');
            $conditions[] = new OrCondition($conditions2);
        }

        return new AndCondition($conditions);
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
}
