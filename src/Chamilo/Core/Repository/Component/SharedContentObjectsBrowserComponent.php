<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Filter\Renderer\ConditionFilterRenderer;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\RepositoryRights;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataClass\SharedContentObjectRelCategory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Table\ContentObject\Shared\SharedTable;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\ConditionProperty;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Architecture\ClassnameUtilities;

/**
 * $Id: shared_content_objects_browser.class.php 204 2009-11-13 12:51:30Z kariboe $
 *
 * @package repository.lib.repository_manager.component
 */
/**
 * Default repository manager component which allows the user to browse through the different categories and objects in
 * the repository.
 */
class SharedContentObjectsBrowserComponent extends Manager implements TableSupport
{

    private $form;

    private $view;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->view = Request :: get(self :: PARAM_SHARED_VIEW);
        if (is_null($this->view))
        {
            $this->view = self :: SHARED_VIEW_OTHERS_OBJECTS;
        }

        $trail = BreadcrumbTrail :: get_instance();

        $query = $this->get_action_bar()->get_query();
        if (isset($query) && $query != '')
        {
            $trail->add(
                new Breadcrumb(
                    $this->get_url(),
                    Translation :: get('SearchResultsFor', null, Utilities :: COMMON_LIBRARIES) . ' ' . $query));
        }

        $this->action_bar = $this->get_action_bar();
        $output = $this->get_content_objects_html();

        $html = array();

        $html[] = $this->render_header();

        $html[] = $this->action_bar->as_html();
        $html[] = $output;
        $html[] = ResourceManager :: get_instance()->get_resource_html(
            Path :: getInstance()->getJavascriptPath('Chamilo\Core\Repository', true) . 'Repository.js');

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Gets the table which shows the content objects
     */
    private function get_content_objects_html()
    {
        $table = new SharedTable($this);
        return $table->as_html();
    }

    /**
     * get the condition to retrieve content objects shared with you
     *
     * @return Condition
     */
    private function get_content_objects_shared_with_me_condition()
    {
        $entities = array();
        $entities[] = new UserEntity();
        $entities[] = new PlatformGroupEntity();

        $conditions = array();
        $conditions[] = $this->get_base_condition();

        $shared_content_object_ids = $this->get_shared_content_object_ids($entities);

        $category_conditions = array();
        $category_conditions[] = new InCondition(
            new PropertyConditionVariable(
                SharedContentObjectRelCategory :: class_name(),
                SharedContentObjectRelCategory :: PROPERTY_CONTENT_OBJECT_ID),
            $shared_content_object_ids,
            SharedContentObjectRelCategory :: get_table_name());

        // inside a category?
        $share_category = Request :: get(self :: PARAM_SHARED_CATEGORY_ID);
        if ($share_category)
        {
            $category_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    SharedContentObjectRelCategory :: class_name(),
                    SharedContentObjectRelCategory :: PROPERTY_CATEGORY_ID),
                new StaticConditionVariable($share_category));
        }
        else
        {
            $category_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(RepositoryCategory :: class_name(), RepositoryCategory :: PROPERTY_USER_ID),
                new StaticConditionVariable($this->get_user_id()));
        }

        $category_condition = new AndCondition($category_conditions);

        $shared_content_objects_ids_in_categories = array();

        $shared_content_objects_in_categories = DataManager :: retrieve_shared_content_object_rel_categories(
            $category_condition);

        while ($shared_content_object_in_category = $shared_content_objects_in_categories->next_result())
        {
            $shared_content_objects_ids_in_categories[] = $shared_content_object_in_category->get_content_object_id();
        }

        if ($share_category)
        {
            $valid_ids = $shared_content_objects_ids_in_categories;
        }
        else
        {
            $valid_ids = array_diff($shared_content_object_ids, $shared_content_objects_ids_in_categories);
        }

        $conditions[] = new InCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID),
            $valid_ids,
            ContentObject :: get_table_name());

        $condition = new AndCondition($conditions);

        return $condition;
    }

    protected function get_shared_content_object_ids($entities = null, $tree_identifier = null)
    {
        $retrieve_types = array(RepositoryRights :: TYPE_USER_CONTENT_OBJECT);

        $shared_locations_by_type = RepositoryRights :: get_instance()->get_location_overview_with_rights_granted(
            ClassnameUtilities :: getInstance()->getNamespaceParent(self :: context(), 1),
            Session :: get_user_id(),
            $entities,
            null,
            $retrieve_types,
            RepositoryRights :: TREE_TYPE_USER,
            $tree_identifier);

        if (empty($shared_locations_by_type))
        {
            $shared_locations_by_type[] = - 1;
        }

        return $shared_locations_by_type[RepositoryRights :: TYPE_USER_CONTENT_OBJECT];
    }

    /**
     * Returns the base condition
     *
     * @return \libraries\storage\AndCondition
     */
    protected function get_base_condition()
    {
        $conditions = array();

        $action_bar_condition = $this->action_bar->get_conditions(
            array(
                new ConditionProperty(
                    new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_TITLE)),
                new ConditionProperty(
                    new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_DESCRIPTION))));
        if ($action_bar_condition)
        {
            $conditions[] = $action_bar_condition;
        }

        $filter_condition_renderer = ConditionFilterRenderer :: factory(
            FilterData :: get_instance(),
            $this->get_user_id(),
            $this->get_allowed_content_object_types());
        $filter_condition = $filter_condition_renderer->render();

        if ($filter_condition instanceof Condition)
        {
            $conditions[] = $filter_condition;
        }

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_STATE),
            new StaticConditionVariable(ContentObject :: STATE_NORMAL));

        return new AndCondition($conditions);
    }

    /**
     * gets the general conditions for the share browser
     *
     * @return Condition
     */
    private function get_content_objects_share_conditions($entities = null, $tree_identifier = null)
    {
        $shared_content_object_ids = $this->get_shared_content_object_ids($entities, $tree_identifier);

        $conditions = array();
        $conditions[] = $this->get_base_condition();

        $conditions[] = new InCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID),
            array_unique($shared_content_object_ids));

        return new AndCondition($conditions);
    }

    public function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url());
        $action_bar->add_common_action(
            new ToolbarItem(
                Translation :: get('ShowAll', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath('Action/Browser'),
                $this->get_url(),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        if ($this->view == self :: SHARED_VIEW_OTHERS_OBJECTS)
        {
            $action_bar->add_common_action(
                new ToolbarItem(
                    Translation :: get('ManageCategories'),
                    Theme :: getInstance()->getCommonImagePath('Action/Category'),
                    $this->get_url(
                        array(
                            Application :: PARAM_ACTION => self :: ACTION_MANAGE_CATEGORIES,
                            self :: PARAM_CATEGORY_TYPE => RepositoryCategory :: TYPE_SHARED)),
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        return $action_bar;
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTENT_OBJECTS)),
                Translation :: get('RepositoryManagerBrowserComponent')));
        $breadcrumbtrail->add_help('repository_shared_content_object_browser');
    }

    public function get_additional_parameters()
    {
        return array(self :: PARAM_SHARED_VIEW, self :: PARAM_SHARED_CATEGORY_ID);
    }

    public function show_my_objects()
    {
        return Request :: get(self :: PARAM_SHARED_VIEW) == 1;
    }

    public function get_view()
    {
        return $this->view;
    }

    public function get_table_condition($table_class_name)
    {
        if ($this->get_view() == self :: SHARED_VIEW_OTHERS_OBJECTS)
        {
            $condition = $this->get_content_objects_shared_with_me_condition();
        }
        else
        {
            $condition = $this->get_content_objects_share_conditions(array(), $this->get_user_id());
        }

        return $condition;
    }
}
