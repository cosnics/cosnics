<?php
namespace Chamilo\Application\Weblcms\Tool\Action\Component;

use Chamilo\Application\Weblcms\CourseSettingsConnector;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Menu\PublicationCategoriesTree;
use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSection;
use Chamilo\Application\Weblcms\Tool\Action\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\Categorizable;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\NotificationMessage;
use Chamilo\Libraries\Format\Structure\ActionBar\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SplitDropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\InequalityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: viewer.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.complex_display.assessment.component
 */
class BrowserComponent extends Manager implements DelegateComponent
{

    private $action_bar;

    private $introduction_text;

    private $publication_category_tree;

    private $publications;

    private $is_course_admin;

    public function run()
    {
        // set if we are browsing as course admin, used for displaying the
        // additional tabs and actions
        $this->is_course_admin = $this->get_course()->is_course_admin($this->get_user());

        if (! $this->is_allowed(WeblcmsRights :: VIEW_RIGHT))
        {
            throw new NotAllowedException();
        }

        $this->introduction_text = $this->get_parent()->get_introduction_text();
        $this->action_bar = $this->get_action_bar();

        $this->publication_category_tree = new PublicationCategoriesTree($this);

        $publication_renderer = ContentObjectPublicationListRenderer :: factory(
            $this->get_parent()->get_browser_type(),
            $this);

        $actions = new TableFormActions(
            'Chamilo\Application\Weblcms\Table\Publication\Table',
            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID);

        if (method_exists($this->get_parent(), 'get_additional_form_actions'))
        {
            $additional_form_actions = $this->get_parent()->get_additional_form_actions();
            foreach ($additional_form_actions as $form_action)
            {
                $actions->add_form_action($form_action);
            }
        }

        $actions->add_form_action(
            new TableFormAction(
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_DELETE)),
                Translation :: get('RemoveSelected', null, Utilities :: COMMON_LIBRARIES)));

        $actions->add_form_action(
            new TableFormAction(
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_TOGGLE_VISIBILITY)),
                Translation :: get('ToggleVisibility'),
                false));

        if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT) && $this->get_parent() instanceof Categorizable)
        {
            $actions->add_form_action(
                new TableFormAction(
                    $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_MOVE_TO_CATEGORY)),
                    Translation :: get('MoveSelected', null, Utilities :: COMMON_LIBRARIES),
                    false));
        }

        $publication_renderer->set_actions($actions);
        if ($this->get_parent()->get_browser_type() == ContentObjectPublicationListRenderer :: TYPE_GALLERY ||
             $this->get_parent()->get_browser_type() == ContentObjectPublicationListRenderer :: TYPE_SLIDESHOW)
        {
            $messages = Session :: retrieve(Application :: PARAM_MESSAGES);
            $messages[Application :: PARAM_MESSAGE_TYPE][] = NotificationMessage :: TYPE_WARNING;
            $messages[Application :: PARAM_MESSAGE][] = Translation :: get('BrowserWarningPreview');

            Session :: register(Application :: PARAM_MESSAGES, $messages);
        }

        $html = array();

        $html[] = $this->render_header();

        $content = array();

        $course_settings_controller = CourseSettingsController :: get_instance();

        if ($course_settings_controller->get_course_setting(
            $this->get_course(),
            \Chamilo\Application\Weblcms\CourseSettingsConnector :: ALLOW_INTRODUCTION_TEXT))
        {
            $html[] = $this->get_parent()->display_introduction_text($this->introduction_text);
        }

        $html[] = $this->action_bar->render();

        if ($this->get_parent() instanceof Categorizable)
        {
            $html[] = '<div class="tree_menu_on_top" style="max-height:150px; overflow: auto;">';
            $html[] = '<div id="tree_menu_hide_container" class="tree_menu_hide_container" style="float: right;' .
                 'overflow: auto; ">';
            $html[] = '<a id="tree_menu_action_hide" class="tree_menu_hide" href="#">' . Translation :: get('ShowAll') .
                 '</a>';
            $html[] = '</div>';
            $html[] = '<div id=tree style="width:90%;overflow: auto;">';
            $html[] = $this->publication_category_tree->render_as_tree();
            $html[] = '</div>';
            $html[] = ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath('Chamilo\Application\Weblcms', true) . 'TreeMenu.js');
            $html[] = '</div>';

            $cat_id = intval(Request :: get(\Chamilo\Application\Weblcms\Manager :: PARAM_CATEGORY));

            if (! $cat_id || $cat_id == 0)
            {
                $cat_name = Translation :: get('Root');
            }
            else
            {
                $category = $this->retrieve_category($cat_id);
                if ($category)
                {
                    $cat_name = $category->get_name();
                }
                else
                {
                    $cat_name = Translation :: get('Root');
                }
            }
            $html[] = '<div style="color: #797268; font-weight: bold;">' . Translation :: get('CurrentCategory') . ': ' .
                 $cat_name . '</div><br />';
        }

        $content[] = $publication_renderer->as_html();

        $html[] = '<div class="clear"></div>';

        if (method_exists($this->get_parent(), 'show_additional_information'))
        {
            $html[] = $this->get_parent()->show_additional_information($this);
        }

        if ($this->get_publication_count() > 0 &&
             $this->get_parent()->get_tool_registration()->get_section_type() == CourseSection :: TYPE_DISABLED)
        {
            $html[] = Display :: warning_message(Translation :: get('ToolInvisible'));
        }

        $html[] = implode(PHP_EOL, $content);

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Retrieves the publications
     *
     * @return array An array of ContentObjectPublication objects
     */
    public function get_publications($offset, $max_objects, $object_table_order = array())
    {
        if (empty($this->publications))
        {

            if ($this->get_publication_type() == \Chamilo\Application\Weblcms\Tool\Manager :: PUBLICATION_TYPE_FROM_ME)
            {

                $publications_resultset = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_my_publications(
                    $this->get_location(),
                    $this->get_entities(),
                    $this->get_publication_conditions(),
                    $object_table_order,
                    $offset,
                    $max_objects,
                    $this->get_user_id());
            }
            elseif ($this->get_publication_type() == \Chamilo\Application\Weblcms\Tool\Manager :: PUBLICATION_TYPE_ALL)
            {
                $publications_resultset = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_content_object_publications(
                    $this->get_publication_conditions(),
                    $object_table_order,
                    $offset,
                    $max_objects);
            }
            else
            {
                $publications_resultset = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_content_object_publications_with_view_right_granted_in_category_location(
                    $this->get_location(),
                    $this->get_entities(),
                    $this->get_publication_conditions(),
                    $object_table_order,
                    $offset,
                    $max_objects,
                    $this->get_user_id());
            }
            if ($publications_resultset)
            {
                $this->publications = $publications_resultset->as_array();
            }
        }

        if ($this->publications)
        {
            return $this->publications;
        }
    }

    /**
     * Retrieves the number of published content objects
     *
     * @return int
     */
    public function get_publication_count()
    {
        if ($this->get_publication_type() == \Chamilo\Application\Weblcms\Tool\Manager :: PUBLICATION_TYPE_FROM_ME)
        {
            $count = \Chamilo\Application\Weblcms\Storage\DataManager :: count_my_publications(
                $this->get_location(),
                $this->get_entities(),
                $this->get_publication_conditions(),
                $this->get_user_id());
        }
        elseif ($this->get_publication_type() == \Chamilo\Application\Weblcms\Tool\Manager :: PUBLICATION_TYPE_ALL)
        {
            $count = \Chamilo\Application\Weblcms\Storage\DataManager :: count_content_object_publications(
                $this->get_publication_conditions());
        }
        else
        {
            $count = \Chamilo\Application\Weblcms\Storage\DataManager :: count_content_object_publications_with_view_right_granted_in_category_location(
                $this->get_location(),
                $this->get_entities(),
                $this->get_publication_conditions(),
                $this->get_user_id());
        }
        return $count;
    }

    public function get_action_bar()
    {
        $buttonToolBar = new ButtonToolBar($this->get_url());

        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $commonActions = new ButtonGroup();
        $toolActions = new ButtonGroup();

        $buttonToolBar->addButtonGroup($commonActions);
        $buttonToolBar->addButtonGroup($toolActions);

        if ($this->is_allowed(WeblcmsRights :: ADD_RIGHT))
        {

            $publish_type = PlatformSetting :: get('display_publication_screen', __NAMESPACE__);
            if ($publish_type == \Chamilo\Application\Weblcms\Tool\Manager :: PUBLISH_TYPE_BOTH)
            {
                $publishActions = new SplitDropdownButton(

                    Translation :: get('QuickPublish', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Publish'),
                    $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_PUBLISH,
                            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLISH_MODE => \Chamilo\Application\Weblcms\Tool\Manager :: PUBLISH_MODE_QUICK)),
                    Button :: DISPLAY_ICON_AND_LABEL);
            }

            // added tool dependent publish button
            $tool_dependent_publish = PlatformSetting :: get('tool_dependent_publish_button', __NAMESPACE__);

            if ($tool_dependent_publish == \Chamilo\Application\Weblcms\Tool\Manager :: PUBLISH_INDEPENDENT)
            {
                $publishActions = new SplitDropdownButton(
                    Translation :: get('Publish', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Publish'),
                    $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_PUBLISH)),
                    Button :: DISPLAY_ICON_AND_LABEL);
            }
            else
            {
                $tool = Request :: get('tool');
                $publishActions = new SplitDropdownButton(
                    Translation :: get(
                        'PublishToolDependent',
                        array(
                            'TYPE' => Translation :: get(
                                'TypeNameSingle',
                                null,
                                'Chamilo\Application\Weblcms\Tool\Implementation\\' . $tool)),
                        Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Publish'),
                    $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_PUBLISH)),
                    Button :: DISPLAY_ICON_AND_LABEL);
            }

            $commonActions->addButton($publishActions);
        }

        if ($this->is_course_admin)
        {
            $commonActions->addButton(
                new Button(
                    Translation :: get('ManageRights', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Rights'),
                    $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_EDIT_RIGHTS,
                            \Chamilo\Application\Weblcms\Manager :: PARAM_CATEGORY => Request :: get(
                                \Chamilo\Application\Weblcms\Manager :: PARAM_CATEGORY))),
                    Button :: DISPLAY_ICON_AND_LABEL));
        }

        if ($this->is_course_admin && $this->get_parent() instanceof Categorizable)
        {
            $commonActions->addButton(
                new Button(
                    Translation :: get('ManageCategories', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Category'),
                    $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_MANAGE_CATEGORIES)),
                    Button :: DISPLAY_ICON_AND_LABEL));
        }

        $course_settings_controller = CourseSettingsController :: get_instance();

        if (! $this->introduction_text && $this->is_course_admin && $course_settings_controller->get_course_setting(
            $this->get_course(),
            CourseSettingsConnector :: ALLOW_INTRODUCTION_TEXT))
        {
            $publishActions->addSubButton(
                new SubButton(
                    Translation :: get('PublishIntroductionText', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Introduce'),
                    $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_PUBLISH_INTRODUCTION)),
                    Button :: DISPLAY_ICON_AND_LABEL));
        }

        if (method_exists($this->get_parent(), 'get_tool_actions'))
        {
            $toolActions->setButtons($this->get_parent()->get_tool_actions());
        }

        if (method_exists($this->get_parent(), 'addToolActions'))
        {
            $toolActions->setButtons($this->get_parent()->addToolActions($buttonToolBar));
        }

        $publicationType = $this->get_publication_type();
        $publicationsActions = array();

        if ($this->is_course_admin)
        {
            $publicationsActions[] = new SubButton(
                Translation :: get('AllPublications'),
                Theme :: getInstance()->getCommonImagePath('Treemenu/SharedObjects'),
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_BROWSE_PUBLICATION_TYPE => \Chamilo\Application\Weblcms\Tool\Manager :: PUBLICATION_TYPE_ALL)),
                Button :: DISPLAY_LABEL,
                false,
                $publicationType == \Chamilo\Application\Weblcms\Tool\Manager :: PUBLICATION_TYPE_ALL ? 'selected' : '');
        }

        $publicationsActions[] = new SubButton(
            Translation :: get('PublishedForMe'),
            Theme :: getInstance()->getCommonImagePath('Treemenu/SharedObjects'),
            $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_BROWSE_PUBLICATION_TYPE => \Chamilo\Application\Weblcms\Tool\Manager :: PUBLICATION_TYPE_FOR_ME)),
            Button :: DISPLAY_LABEL,
            false,
            $publicationType == \Chamilo\Application\Weblcms\Tool\Manager :: PUBLICATION_TYPE_FOR_ME ? 'selected' : '');

        $publicationsActions[] = new SubButton(
            Translation :: get('MyPublications'),
            Theme :: getInstance()->getCommonImagePath('Treemenu/Publication'),
            $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_BROWSE_PUBLICATION_TYPE => \Chamilo\Application\Weblcms\Tool\Manager :: PUBLICATION_TYPE_FROM_ME)),
            Button :: DISPLAY_LABEL,
            false,
            $publicationType == \Chamilo\Application\Weblcms\Tool\Manager :: PUBLICATION_TYPE_FROM_ME ? 'selected' : '');

        switch ($publicationType)
        {
            case \Chamilo\Application\Weblcms\Tool\Manager :: PUBLICATION_TYPE_ALL :
                $variable = 'AllPublications';
                $imagePath = Theme :: getInstance()->getCommonImagePath('Treemenu/SharedObjects');
                break;
            case \Chamilo\Application\Weblcms\Tool\Manager :: PUBLICATION_TYPE_FOR_ME :
                $variable = 'PublishedForMe';
                $imagePath = Theme :: getInstance()->getCommonImagePath('Treemenu/SharedObjects');
                break;
            case \Chamilo\Application\Weblcms\Tool\Manager :: PUBLICATION_TYPE_FROM_ME :
                $variable = 'MyPublications';
                $imagePath = Theme :: getInstance()->getCommonImagePath('Treemenu/Publication');
                break;
        }

        $publicationsAction = new DropdownButton(Translation :: get($variable), $imagePath);
        $publicationsAction->setSubButtons($publicationsActions);

        $toolActions->addButton($publicationsAction);

        $browser_types = $this->get_parent()->get_available_browser_types();

        $browserTypeActions = new DropdownButton(
            Translation :: get($this->get_parent()->get_browser_type() . 'View', null, Utilities :: COMMON_LIBRARIES),
            Theme :: getInstance()->getCommonImagePath('View/' . $this->get_parent()->get_browser_type()));
        $toolActions->addButton($browserTypeActions);

        if (count($browser_types) > 1)
        {
            foreach ($browser_types as $browser_type)
            {
                if ($this->get_parent()->get_browser_type() != $browser_type)
                {
                    $action = $this->get_url(
                        array(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_BROWSER_TYPE => $browser_type));
                    $classes = '';
                }
                else
                {
                    $action = '';
                    $classes = 'selected';
                }

                $browserTypeActions->addSubButton(
                    new SubButton(
                        Translation :: get(
                            (string) StringUtilities :: getInstance()->createString($browser_type)->upperCamelize() .
                                 'View',
                                null,
                                Utilities :: COMMON_LIBRARIES),
                        Theme :: getInstance()->getCommonImagePath('View/' . $browser_type),
                        $action,
                        Button :: DISPLAY_LABEL,
                        false,
                        $classes));
            }
        }

        $buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        return $buttonToolbarRenderer;
    }

    public function get_publication_conditions()
    {
        $conditions = array();

        $type = $this->get_publication_type();
        switch ($type)
        {
            // Begin with the publisher condition when FROM_ME and add the
            // remaining conditions. Skip the publisher
            // condition when ALL.
            case \Chamilo\Application\Weblcms\Tool\Manager :: PUBLICATION_TYPE_FROM_ME :
                $va_id = Session :: get(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_VIEW_AS_ID);
                $course_id = Session :: get(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_VIEW_AS_COURSE_ID);
                $user_id = Session :: get_user_id();

                $publisher_id = (isset($va_id) && isset($course_id) && $course_id == $this->get_course_id()) ? $va_id : $user_id;

                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        ContentObjectPublication :: class_name(),
                        ContentObjectPublication :: PROPERTY_PUBLISHER_ID),
                    new StaticConditionVariable($publisher_id));

            case \Chamilo\Application\Weblcms\Tool\Manager :: PUBLICATION_TYPE_ALL :
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        ContentObjectPublication :: class_name(),
                        ContentObjectPublication :: PROPERTY_COURSE_ID),
                    new StaticConditionVariable($this->get_course_id()));

                if ($this->get_tool_id())
                {
                    $conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(
                            ContentObjectPublication :: class_name(),
                            ContentObjectPublication :: PROPERTY_TOOL),
                        new StaticConditionVariable($this->get_tool_id()));
                }

                $category_id = Request :: get(\Chamilo\Application\Weblcms\Manager :: PARAM_CATEGORY);
                if (! $category_id)
                {
                    $category_id = 0;
                }

                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        ContentObjectPublication :: class_name(),
                        ContentObjectPublication :: PROPERTY_CATEGORY_ID),
                    new StaticConditionVariable($category_id));

                break;
            default :

                $from_date_variables = new PropertyConditionVariable(
                    ContentObjectPublication :: class_name(),
                    ContentObjectPublication :: PROPERTY_FROM_DATE);

                $to_date_variable = new PropertyConditionVariable(
                    ContentObjectPublication :: class_name(),
                    ContentObjectPublication :: PROPERTY_TO_DATE);

                $time_conditions = array();

                $time_conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        ContentObjectPublication :: class_name(),
                        ContentObjectPublication :: PROPERTY_HIDDEN),
                    new StaticConditionVariable(0));

                $forever_conditions = array();

                $forever_conditions[] = new EqualityCondition($from_date_variables, new StaticConditionVariable(0));

                $forever_conditions[] = new EqualityCondition($to_date_variable, new StaticConditionVariable(0));

                $forever_condition = new AndCondition($forever_conditions);

                $between_conditions = array();

                $between_conditions[] = new InequalityCondition(
                    $from_date_variables,
                    InequalityCondition :: LESS_THAN_OR_EQUAL,
                    new StaticConditionVariable(time()));

                $between_conditions[] = new InequalityCondition(
                    $to_date_variable,
                    InequalityCondition :: GREATER_THAN_OR_EQUAL,
                    new StaticConditionVariable(time()));

                $between_condition = new AndCondition($between_conditions);

                $time_conditions[] = new OrCondition(array($forever_condition, $between_condition));

                $conditions[] = new AndCondition($time_conditions);
                break;
        }

        if ($this->get_search_condition())
        {
            $conditions[] = $this->get_search_condition();
        }

        $conditions[] = new InCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_TYPE),
            $this->get_allowed_types());

        if (method_exists($this->get_parent(), 'get_tool_conditions'))
        {
            foreach ($this->get_parent()->get_tool_conditions() as $tool_condition)
            {
                $conditions[] = $tool_condition;
            }
        }

        if ($conditions)
        {
            return new AndCondition($conditions);
        }
        else
        {
            return null;
        }
    }

    public function get_search_condition()
    {
        $query = $this->action_bar->getSearchForm()->getQuery();
        if (isset($query) && $query != '')
        {
            $conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_TITLE),
                '*' . $query . '*');

            $conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_DESCRIPTION),
                '*' . $query . '*');

            return new OrCondition($conditions);
        }

        return null;
    }

    public function count_tool_categories()
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory :: class_name(),
                ContentObjectPublicationCategory :: PROPERTY_TOOL),
            new StaticConditionVariable($this->get_tool_id()));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory :: class_name(),
                ContentObjectPublicationCategory :: PROPERTY_COURSE),
            new StaticConditionVariable($this->get_course_id()));

        $condition = new AndCondition($conditions);

        return \Chamilo\Application\Weblcms\Storage\DataManager :: count(
            ContentObjectPublicationCategory :: class_name(),
            $condition);
    }

    private function retrieve_category($category_id)
    {
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory :: class_name(),
                ContentObjectPublicationCategory :: PROPERTY_ID),
            new StaticConditionVariable($category_id));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory :: class_name(),
                ContentObjectPublicationCategory :: PROPERTY_COURSE),
            new StaticConditionVariable($this->get_parent()->get_course_id()));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory :: class_name(),
                ContentObjectPublicationCategory :: PROPERTY_TOOL),
            new StaticConditionVariable($this->get_parent()->get_tool_id()));

        $condition = new AndCondition($conditions);

        $objects = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieves(
            ContentObjectPublicationCategory :: class_name(),
            new DataClassRetrievesParameters($condition));

        return $objects->next_result();
    }

    public function get_publication_type()
    {
        $type = Request :: get(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_BROWSE_PUBLICATION_TYPE);
        if (! $type)
        {
            if ($this->is_course_admin)
            {
                $type = \Chamilo\Application\Weblcms\Tool\Manager :: PUBLICATION_TYPE_ALL;
            }
            else
            {
                $type = \Chamilo\Application\Weblcms\Tool\Manager :: PUBLICATION_TYPE_FOR_ME;
            }
        }

        return $type;
    }

    /**
     * Returns the default object table order for the browser.
     * Can be "overridden" by the individual component to force
     * a different order if needed. Because the individual component is not an actual implementation but merely this
     * parent, there is a check if the method exists.
     *
     * @return ObjectTableOrder
     */
    public function get_default_order_property()
    {
        if (method_exists($this->get_parent(), 'get_default_order_property'))
        {
            return $this->get_parent()->get_default_order_property();
        }
        return new OrderBy(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX));
    }

    public function tool_category_has_new_publications($category_id)
    {
        return \Chamilo\Application\Weblcms\Storage\DataManager :: tool_category_has_new_publications(
            $this->get_tool_id(),
            $this->get_user(),
            $this->get_course(),
            $category_id);
    }
}
