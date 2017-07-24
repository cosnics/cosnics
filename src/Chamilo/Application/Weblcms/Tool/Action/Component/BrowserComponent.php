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
use Chamilo\Core\Repository\ContentObject\Introduction\Storage\DataClass\Introduction;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Viewer\ActionSelector;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\Categorizable;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButtonDivider;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButtonHeader;
use Chamilo\Libraries\Format\Structure\Glyph\BootstrapGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
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

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    private $introductionText;

    private $publications;

    /**
     *
     * @var boolean
     */
    private $isCourseAdmin;

    public function run()
    {
        $content = array();

        $content[] = $this->renderToolHeader();
        $content[] = '<div class="publication_container row">';

        try
        {
            $this->checkAuthorization();
            $publicationsContent = $this->renderPublications();
        }
        catch (\Exception $ex)
        {
            $publicationsContent = '<div class="alert alert-danger">' .
                 Translation::getInstance()->getTranslation('NoViewRights', null, Manager::context()) . '</div>';
        }

        if ($this->get_parent() instanceof Categorizable)
        {
            $content[] = $this->renderCategories($publicationsContent);
        }
        else
        {
            $content[] = '<div class="publication_renderer col-xs-12">';
            $content[] = $publicationsContent;
            $content[] = '</div>';
        }

        $content[] = '</div>';

        $html = array();

        $html[] = $this->render_header();
        $html[] = implode(PHP_EOL, $content);
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function renderToolHeader()
    {
        $html = array();

        $html[] = $this->renderIntroduction();
        $html[] = $this->getButtonToolbarRenderer()->render();

        if ($this->get_publication_count() > 0 &&
             $this->get_parent()->get_tool_registration()->get_section_type() == CourseSection::TYPE_DISABLED)
        {
            $html[] = Display::warning_message(Translation::get('ToolInvisible'));
        }

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @throws NotAllowedException
     */
    public function checkAuthorization()
    {
        // set if we are browsing as course admin, used for displaying the
        // additional tabs and actions
        if (! $this->is_allowed(WeblcmsRights::VIEW_RIGHT))
        {
            throw new NotAllowedException();
        }
    }

    public function getIntroductionText()
    {
        if (! isset($this->introductionText))
        {
            $this->introductionText = $this->get_parent()->get_introduction_text();
        }

        return $this->introductionText;
    }

    /**
     *
     * @return string
     */
    public function renderIntroduction()
    {
        $course_settings_controller = CourseSettingsController::getInstance();

        if ($course_settings_controller->get_course_setting(
            $this->get_course(),
            \Chamilo\Application\Weblcms\CourseSettingsConnector::ALLOW_INTRODUCTION_TEXT))
        {
            return $this->get_parent()->display_introduction_text($this->getIntroductionText());
        }
    }

    public function renderCategories($renderedPublications)
    {
        $publicationCategoryTree = new PublicationCategoriesTree($this);

        $html = array();

        if ($this->hasCategories())
        {

            $html[] = '<div class="col-md-3 col-lg-2 col-sm-12">';

            $categoryId = intval(Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_CATEGORY));

            if (! $categoryId || $categoryId == 0)
            {
                $categoryName = Translation::get('Root');
            }
            else
            {
                $category = $this->retrieve_category($categoryId);
                if ($category)
                {
                    $categoryName = $category->get_name();
                }
                else
                {
                    $categoryName = Translation::get('Root');
                }
            }

            $html[] = '<div id="tree">';
            $html[] = $publicationCategoryTree->render_as_tree();
            $html[] = '</div>';
            $html[] = '</div>';

            $html[] = '<div class="publication_renderer col-md-9 col-lg-10 col-sm-12">';
            $html[] = $renderedPublications;
            $html[] = '</div>';
            $html[] = '</div>';
        }
        else
        {
            $html[] = '<div class="publication_renderer col-md-12">';
            $html[] = $renderedPublications;
            $html[] = '</div>';
            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return boolean
     */
    public function hasCategories()
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class_name(),
                ContentObjectPublicationCategory::PROPERTY_COURSE),
            new StaticConditionVariable($this->get_course_id()));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class_name(),
                ContentObjectPublicationCategory::PROPERTY_TOOL),
            new StaticConditionVariable($this->get_tool_id()));

        return \Chamilo\Libraries\Storage\DataManager\DataManager::count(
            ContentObjectPublicationCategory::class_name(),
            new DataClassCountParameters(new AndCondition($conditions))) > 0;
    }

    /**
     *
     * @return boolean
     */
    public function isCourseAdmin()
    {
        if (! isset($this->isCourseAdmin))
        {
            $this->isCourseAdmin = $this->get_course()->is_course_admin($this->get_user());
        }

        return $this->isCourseAdmin;
    }

    /**
     *
     * @return string
     */
    public function renderPublications()
    {
        $publicationRenderer = ContentObjectPublicationListRenderer::factory(
            $this->get_parent()->get_browser_type(),
            $this);

        $actions = new TableFormActions(
            'Chamilo\Application\Weblcms\Table\Publication\Table',
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);

        if (method_exists($this->get_parent(), 'get_additional_form_actions'))
        {
            $additional_form_actions = $this->get_parent()->get_additional_form_actions();

            foreach ($additional_form_actions as $form_action)
            {
                $actions->add_form_action($form_action);
            }
        }

        if ($this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $actions->add_form_action(
                new TableFormAction(
                    $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_DELETE)),
                    Translation::get('RemoveSelected', null, Utilities::COMMON_LIBRARIES)));

            $actions->add_form_action(
                new TableFormAction(
                    $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_TOGGLE_VISIBILITY)),
                    Translation::get('ToggleVisibility'),
                    false));
        }

        if ($this->is_allowed(WeblcmsRights::EDIT_RIGHT) && $this->get_parent() instanceof Categorizable)
        {
            $actions->add_form_action(
                new TableFormAction(
                    $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_MOVE_TO_CATEGORY)),
                    Translation::get('MoveSelected', null, Utilities::COMMON_LIBRARIES),
                    false));
        }

        $publicationRenderer->set_actions($actions);

        return $publicationRenderer->as_html();
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

            if ($this->get_publication_type() == \Chamilo\Application\Weblcms\Tool\Manager::PUBLICATION_TYPE_FROM_ME)
            {

                $publications_resultset = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_my_publications(
                    $this->get_location(),
                    $this->get_entities(),
                    $this->get_publication_conditions(),
                    $object_table_order,
                    $offset,
                    $max_objects,
                    $this->get_user_id());
            }
            elseif ($this->get_publication_type() == \Chamilo\Application\Weblcms\Tool\Manager::PUBLICATION_TYPE_ALL)
            {
                $publications_resultset = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_content_object_publications(
                    $this->get_publication_conditions(),
                    $object_table_order,
                    $offset,
                    $max_objects);
            }
            else
            {
                $publications_resultset = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_content_object_publications_with_view_right_granted_in_category_location(
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
        if ($this->get_publication_type() == \Chamilo\Application\Weblcms\Tool\Manager::PUBLICATION_TYPE_FROM_ME)
        {
            $count = \Chamilo\Application\Weblcms\Storage\DataManager::count_my_publications(
                $this->get_location(),
                $this->get_entities(),
                $this->get_publication_conditions(),
                $this->get_user_id());
        }
        elseif ($this->get_publication_type() == \Chamilo\Application\Weblcms\Tool\Manager::PUBLICATION_TYPE_ALL)
        {
            $count = \Chamilo\Application\Weblcms\Storage\DataManager::count_content_object_publications(
                $this->get_publication_conditions());
        }
        else
        {
            $count = \Chamilo\Application\Weblcms\Storage\DataManager::count_content_object_publications_with_view_right_granted_in_category_location(
                $this->get_location(),
                $this->get_entities(),
                $this->get_publication_conditions(),
                $this->get_user_id());
        }

        return $count;
    }

    public function getPublicationButton($label, $glyph, $allowedContentObjectTypes, $parameters,
        $extraActions = array(), $classes = null)
    {
        $actionSelector = new ActionSelector(
            $this,
            $this->getUser()->getId(),
            $allowedContentObjectTypes,
            $parameters,
            $extraActions,
            $classes);

        return $actionSelector->getActionButton($label, $glyph);
    }

    public function getButtonToolbarRenderer()
    {
        if (! isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());

            $publishActions = new ButtonGroup();
            $toolActions = new ButtonGroup();
            $manageActions = new ButtonGroup();

            if ($this->is_allowed(WeblcmsRights::ADD_RIGHT))
            {

                $parameters = $this->get_parameters();
                $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] = \Chamilo\Application\Weblcms\Tool\Manager::ACTION_PUBLISH;

                $publishActions->addButton(
                    $this->getPublicationButton(
                        Translation::get('Publish', null, Utilities::COMMON_LIBRARIES),
                        new BootstrapGlyph('plus'),
                        $this->get_allowed_content_object_types(),
                        $parameters,
                        array(),
                        'btn-primary'));

                $courseSettingsController = CourseSettingsController::getInstance();

                if (! $this->getIntroductionText() && $this->isCourseAdmin() && $courseSettingsController->get_course_setting(
                    $this->get_course(),
                    CourseSettingsConnector::ALLOW_INTRODUCTION_TEXT))
                {
                    $parameters = $this->get_parameters();
                    $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] = \Chamilo\Application\Weblcms\Tool\Manager::ACTION_PUBLISH_INTRODUCTION;

                    $publishActions->addButton(
                        $this->getPublicationButton(
                            Translation::get('PublishIntroductionText', null, Utilities::COMMON_LIBRARIES),
                            new FontAwesomeGlyph('book'),  // new BootstrapGlyph('info-sign'),
                            array(Introduction::class_name()),
                            $parameters));
                }

                if ($this->isCourseAdmin())
                {
                    $link = $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_EDIT_RIGHTS,
                            \Chamilo\Application\Weblcms\Manager::PARAM_CATEGORY => Request::get(
                                \Chamilo\Application\Weblcms\Manager::PARAM_CATEGORY)));
                }

                $manageActions->addButton(
                    new Button(
                        Translation::get('ManageRights', null, Utilities::COMMON_LIBRARIES),
                        new BootstrapGlyph('lock'),
                        $link,
                        Button::DISPLAY_ICON_AND_LABEL));
            }

            if ($this->is_allowed(WeblcmsRights::MANAGE_CATEGORIES_RIGHT) && $this->get_parent() instanceof Categorizable)
            {
                $manageActions->addButton(
                    new Button(
                        Translation::get('ManageCategories', null, Utilities::COMMON_LIBRARIES),
                        new BootstrapGlyph('folder-close'),
                        $this->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_MANAGE_CATEGORIES)),
                        Button::DISPLAY_ICON_AND_LABEL));
            }

            if (method_exists($this->get_parent(), 'get_tool_actions'))
            {
                $toolActions->setButtons($this->get_parent()->get_tool_actions());
            }

            if (method_exists($this->get_parent(), 'addToolActions'))
            {
                $toolActions->setButtons($this->get_parent()->addToolActions($toolActions));
            }

            $filterAction = new DropdownButton(Translation::get('FilterView'), new BootstrapGlyph('th'));

            $browser_types = $this->get_parent()->get_available_browser_types();

            if (count($browser_types) > 1)
            {
                $filterActions = array();

                $filterActions[] = new SubButtonHeader(Translation::get('ViewTypesHeader'));

                foreach ($browser_types as $browser_type)
                {
                    if ($this->get_parent()->get_browser_type() != $browser_type)
                    {
                        $action = $this->get_url(
                            array(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_BROWSER_TYPE => $browser_type));
                        $classes = 'not-selected';
                    }
                    else
                    {
                        $action = '';
                        $classes = 'selected';
                    }

                    $filterActions[] = new SubButton(
                        Translation::get(
                            (string) StringUtilities::getInstance()->createString($browser_type)->upperCamelize() .
                                 'View',
                                null,
                                Utilities::COMMON_LIBRARIES),
                        Theme::getInstance()->getCommonImagePath('View/' . $browser_type),
                        $action,
                        Button::DISPLAY_LABEL,
                        false,
                        $classes);
                }

                $filterActions[] = new SubButtonDivider();

                $filterAction->addSubButtons($filterActions);
            }

            if (method_exists($this->get_parent(), 'getFilterActions'))
            {
                $filterAction->addSubButtons($this->get_parent()->getFilteractions());
            }

            $filterActions = array();

            $filterActions[] = new SubButtonHeader(Translation::get('ViewPublicationsHeader'));

            $publicationType = $this->get_publication_type();

            if ($this->isCourseAdmin())
            {
                $isSelected = ($publicationType == \Chamilo\Application\Weblcms\Tool\Manager::PUBLICATION_TYPE_ALL ? 'selected' : 'not-selected');

                $filterActions[] = new SubButton(
                    Translation::get('AllPublications'),
                    null,
                    $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_BROWSE_PUBLICATION_TYPE => \Chamilo\Application\Weblcms\Tool\Manager::PUBLICATION_TYPE_ALL)),
                    Button::DISPLAY_LABEL,
                    false,
                    $isSelected);
            }

            $isSelected = ($publicationType == \Chamilo\Application\Weblcms\Tool\Manager::PUBLICATION_TYPE_FOR_ME ? 'selected' : 'not-selected');

            $filterActions[] = new SubButton(
                Translation::get('PublishedForMe'),
                null,
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_BROWSE_PUBLICATION_TYPE => \Chamilo\Application\Weblcms\Tool\Manager::PUBLICATION_TYPE_FOR_ME)),
                Button::DISPLAY_LABEL,
                false,
                $isSelected);

            $isSelected = ($publicationType == \Chamilo\Application\Weblcms\Tool\Manager::PUBLICATION_TYPE_FROM_ME ? 'selected' : 'not-selected');

            $filterActions[] = new SubButton(
                Translation::get('MyPublications'),
                null,
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_BROWSE_PUBLICATION_TYPE => \Chamilo\Application\Weblcms\Tool\Manager::PUBLICATION_TYPE_FROM_ME)),
                Button::DISPLAY_LABEL,
                false,
                $isSelected);

            $filterAction->addSubButtons($filterActions);

            $buttonToolbar->addItem($publishActions);
            $buttonToolbar->addItem($manageActions);
            $buttonToolbar->addItem($toolActions);
            $buttonToolbar->addItem($filterAction);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
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
            case \Chamilo\Application\Weblcms\Tool\Manager::PUBLICATION_TYPE_FROM_ME :
                $va_id = Session::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_VIEW_AS_ID);
                $course_id = Session::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_VIEW_AS_COURSE_ID);
                $user_id = Session::get_user_id();

                $publisher_id = (isset($va_id) && isset($course_id) && $course_id == $this->get_course_id()) ? $va_id : $user_id;

                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        ContentObjectPublication::class_name(),
                        ContentObjectPublication::PROPERTY_PUBLISHER_ID),
                    new StaticConditionVariable($publisher_id));

            case \Chamilo\Application\Weblcms\Tool\Manager::PUBLICATION_TYPE_ALL :
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        ContentObjectPublication::class_name(),
                        ContentObjectPublication::PROPERTY_COURSE_ID),
                    new StaticConditionVariable($this->get_course_id()));

                if ($this->get_tool_id())
                {
                    $conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(
                            ContentObjectPublication::class_name(),
                            ContentObjectPublication::PROPERTY_TOOL),
                        new StaticConditionVariable($this->get_tool_id()));
                }

                $category_id = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_CATEGORY);
                if (! $category_id)
                {
                    $category_id = 0;
                }

                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        ContentObjectPublication::class_name(),
                        ContentObjectPublication::PROPERTY_CATEGORY_ID),
                    new StaticConditionVariable($category_id));

                break;
            default :

                $from_date_variables = new PropertyConditionVariable(
                    ContentObjectPublication::class_name(),
                    ContentObjectPublication::PROPERTY_FROM_DATE);

                $to_date_variable = new PropertyConditionVariable(
                    ContentObjectPublication::class_name(),
                    ContentObjectPublication::PROPERTY_TO_DATE);

                $time_conditions = array();

                $time_conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        ContentObjectPublication::class_name(),
                        ContentObjectPublication::PROPERTY_HIDDEN),
                    new StaticConditionVariable(0));

                $forever_conditions = array();

                $forever_conditions[] = new EqualityCondition($from_date_variables, new StaticConditionVariable(0));

                $forever_conditions[] = new EqualityCondition($to_date_variable, new StaticConditionVariable(0));

                $forever_condition = new AndCondition($forever_conditions);

                $between_conditions = array();

                $between_conditions[] = new InequalityCondition(
                    $from_date_variables,
                    InequalityCondition::LESS_THAN_OR_EQUAL,
                    new StaticConditionVariable(time()));

                $between_conditions[] = new InequalityCondition(
                    $to_date_variable,
                    InequalityCondition::GREATER_THAN_OR_EQUAL,
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
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_TYPE),
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
        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();
        if (isset($query) && $query != '')
        {
            $conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_TITLE),
                '*' . $query . '*');

            $conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_DESCRIPTION),
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
                ContentObjectPublicationCategory::class_name(),
                ContentObjectPublicationCategory::PROPERTY_TOOL),
            new StaticConditionVariable($this->get_tool_id()));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class_name(),
                ContentObjectPublicationCategory::PROPERTY_COURSE),
            new StaticConditionVariable($this->get_course_id()));

        $condition = new AndCondition($conditions);

        return \Chamilo\Application\Weblcms\Storage\DataManager::count(
            ContentObjectPublicationCategory::class_name(),
            $condition);
    }

    private function retrieve_category($category_id)
    {
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class_name(),
                ContentObjectPublicationCategory::PROPERTY_ID),
            new StaticConditionVariable($category_id));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class_name(),
                ContentObjectPublicationCategory::PROPERTY_COURSE),
            new StaticConditionVariable($this->get_parent()->get_course_id()));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class_name(),
                ContentObjectPublicationCategory::PROPERTY_TOOL),
            new StaticConditionVariable($this->get_parent()->get_tool_id()));

        $condition = new AndCondition($conditions);

        $objects = \Chamilo\Application\Weblcms\Storage\DataManager::retrieves(
            ContentObjectPublicationCategory::class_name(),
            new DataClassRetrievesParameters($condition));

        return $objects->next_result();
    }

    public function get_publication_type()
    {
        $type = Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_BROWSE_PUBLICATION_TYPE);
        if (! $type)
        {
            if ($this->isCourseAdmin())
            {
                $type = \Chamilo\Application\Weblcms\Tool\Manager::PUBLICATION_TYPE_ALL;
            }
            else
            {
                $type = \Chamilo\Application\Weblcms\Tool\Manager::PUBLICATION_TYPE_FOR_ME;
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
                ContentObjectPublication::class_name(),
                ContentObjectPublication::PROPERTY_DISPLAY_ORDER_INDEX));
    }

    public function tool_category_has_new_publications($category_id)
    {
        return \Chamilo\Application\Weblcms\Storage\DataManager::tool_category_has_new_publications(
            $this->get_tool_id(),
            $this->get_user(),
            $this->get_course(),
            $category_id);
    }
}
