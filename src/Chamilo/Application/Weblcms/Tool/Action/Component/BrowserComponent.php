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
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Action\Manager;
use Chamilo\Core\Repository\ContentObject\Introduction\Storage\DataClass\Introduction;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Viewer\ActionSelector;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\CategorizableInterface;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbLessComponentInterface;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButtonDivider;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButtonHeader;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\ContainsCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package repository.lib.complex_display.assessment.component
 */
class BrowserComponent extends Manager implements BreadcrumbLessComponentInterface
{

    /**
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    private $introductionText;

    /**
     * @var bool
     */
    private $isCourseAdmin;

    private $publications;

    public function run()
    {
        $content = [];

        $content[] = $this->renderToolHeader();
        $content[] = '<div class="publication_container row">';

        $this->checkAuthorization('');

        $publicationsContent = $this->renderPublications();

        if ($this->get_parent() instanceof CategorizableInterface)
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

        $html = [];

        $html[] = $this->render_header();
        $html[] = implode(PHP_EOL, $content);
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws NotAllowedException
     */
    public function checkAuthorization($context, $action = null)
    {
        // set if we are browsing as course admin, used for displaying the
        // additional tabs and actions
        if (!$this->is_allowed(WeblcmsRights::VIEW_RIGHT))
        {
            throw new NotAllowedException();
        }
    }

    public function count_tool_categories()
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_TOOL
            ), new StaticConditionVariable($this->get_tool_id())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_COURSE
            ), new StaticConditionVariable($this->get_course_id())
        );

        $condition = new AndCondition($conditions);

        return DataManager::count(
            ContentObjectPublicationCategory::class, new DataClassCountParameters($condition)
        );
    }

    public function getButtonToolbarRenderer()
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());

            $publishActions = new ButtonGroup();
            $toolActions = new ButtonGroup();
            $manageActions = new ButtonGroup();

            if ($this->is_allowed(WeblcmsRights::ADD_RIGHT))
            {

                $parameters = $this->get_parameters();
                $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] =
                    \Chamilo\Application\Weblcms\Tool\Manager::ACTION_PUBLISH;

                $publishActions->addButton(
                    $this->getPublicationButton(
                        Translation::get('Publish', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('plus'),
                        $this->get_allowed_content_object_types(), $parameters, [], ['btn-primary']
                    )
                );

                $courseSettingsController = CourseSettingsController::getInstance();

                if (!$this->getIntroductionText() && $this->isCourseAdmin() &&
                    $courseSettingsController->get_course_setting(
                        $this->get_course(), CourseSettingsConnector::ALLOW_INTRODUCTION_TEXT
                    ))
                {
                    $parameters = $this->get_parameters();
                    $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] =
                        \Chamilo\Application\Weblcms\Tool\Manager::ACTION_PUBLISH_INTRODUCTION;

                    $publishActions->addButton(
                        $this->getPublicationButton(
                            Translation::get('PublishIntroductionText', null, StringUtilities::LIBRARIES),
                            new FontAwesomeGlyph('book'),  // new FontAwesomeGlyph('info-circle'),
                            [Introduction::class], $parameters
                        )
                    );
                }

                if ($this->isCourseAdmin())
                {
                    $link = $this->get_url(
                        [
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_EDIT_RIGHTS,
                            \Chamilo\Application\Weblcms\Manager::PARAM_CATEGORY => $this->getRequest()->query->get(
                                \Chamilo\Application\Weblcms\Manager::PARAM_CATEGORY
                            )
                        ]
                    );
                }

                $manageActions->addButton(
                    new Button(
                        Translation::get('ManageRights', null, StringUtilities::LIBRARIES),
                        new FontAwesomeGlyph('lock'), $link, Button::DISPLAY_ICON_AND_LABEL
                    )
                );
            }

            if ($this->is_allowed(WeblcmsRights::MANAGE_CATEGORIES_RIGHT) &&
                $this->get_parent() instanceof CategorizableInterface)
            {
                $manageActions->addButton(
                    new Button(
                        Translation::get('ManageCategories', null, StringUtilities::LIBRARIES),
                        new FontAwesomeGlyph('folder'), $this->get_url(
                        [
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_MANAGE_CATEGORIES
                        ]
                    ), Button::DISPLAY_ICON_AND_LABEL
                    )
                );
            }

            if (method_exists($this->get_parent(), 'get_tool_actions'))
            {
                $toolActions->setButtons($this->get_parent()->get_tool_actions());
            }

            if (method_exists($this->get_parent(), 'addToolActions'))
            {
                $toolActions->setButtons($this->get_parent()->addToolActions($toolActions));
            }

            $filterAction = new DropdownButton(Translation::get('FilterView'), new FontAwesomeGlyph('th'));

            $browser_types = $this->get_parent()->get_available_browser_types();

            if (count($browser_types) > 1)
            {
                $filterActions = [];

                $filterActions[] = new SubButtonHeader(Translation::get('ViewTypesHeader'));

                foreach ($browser_types as $browser_type)
                {
                    if ($this->get_parent()->get_browser_type() != $browser_type)
                    {
                        $action = $this->get_url(
                            [\Chamilo\Application\Weblcms\Tool\Manager::PARAM_BROWSER_TYPE => $browser_type]
                        );
                        $isActive = false;
                    }
                    else
                    {
                        $action = '';
                        $isActive = true;
                    }

                    $filterActions[] = new SubButton(
                        Translation::get(
                            (string) StringUtilities::getInstance()->createString($browser_type)->upperCamelize() .
                            'View', null, StringUtilities::LIBRARIES
                        ), null, $action, Button::DISPLAY_LABEL, null, [], null, $isActive
                    );
                }

                $filterActions[] = new SubButtonDivider();

                $filterAction->addSubButtons($filterActions);
            }

            if (method_exists($this->get_parent(), 'getFilterActions'))
            {
                $filterAction->addSubButtons($this->get_parent()->getFilteractions());
            }

            $filterActions = [];

            $filterActions[] = new SubButtonHeader(Translation::get('ViewPublicationsHeader'));

            $publicationType = $this->get_publication_type();

            if ($this->isCourseAdmin())
            {
                $isSelected = $publicationType == \Chamilo\Application\Weblcms\Tool\Manager::PUBLICATION_TYPE_ALL;

                $filterActions[] = new SubButton(
                    Translation::get('AllPublications'), null, $this->get_url(
                    [
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_BROWSE_PUBLICATION_TYPE => \Chamilo\Application\Weblcms\Tool\Manager::PUBLICATION_TYPE_ALL
                    ]
                ), Button::DISPLAY_LABEL, null, [], null, $isSelected
                );
            }

            $isSelected = $publicationType == \Chamilo\Application\Weblcms\Tool\Manager::PUBLICATION_TYPE_FOR_ME;

            $filterActions[] = new SubButton(
                Translation::get('PublishedForMe'), null, $this->get_url(
                [
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_BROWSE_PUBLICATION_TYPE => \Chamilo\Application\Weblcms\Tool\Manager::PUBLICATION_TYPE_FOR_ME
                ]
            ), Button::DISPLAY_LABEL, null, [], null, $isSelected
            );

            $isSelected = $publicationType == \Chamilo\Application\Weblcms\Tool\Manager::PUBLICATION_TYPE_FROM_ME;

            $filterActions[] = new SubButton(
                Translation::get('MyPublications'), null, $this->get_url(
                [
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_BROWSE_PUBLICATION_TYPE => \Chamilo\Application\Weblcms\Tool\Manager::PUBLICATION_TYPE_FROM_ME
                ]
            ), Button::DISPLAY_LABEL, null, [], null, $isSelected
            );

            $filterAction->addSubButtons($filterActions);

            $buttonToolbar->addItem($publishActions);
            $buttonToolbar->addItem($manageActions);
            $buttonToolbar->addItem($toolActions);
            $buttonToolbar->addItem($filterAction);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    /**
     * Returns the default object table order for the browser.
     * Can be "overridden" by the individual component to force
     * a different order if needed. Because the individual component is not an actual implementation but merely this
     * parent, there is a check if the method exists.
     *
     * @return \Chamilo\Libraries\Storage\Query\OrderBy
     */
    public function getDefaultOrderBy()
    {
        if (method_exists($this->get_parent(), 'getDefaultOrderBy'))
        {
            return $this->get_parent()->getDefaultOrderBy();
        }

        return new OrderBy([
            new OrderProperty(
                new PropertyConditionVariable(
                    ContentObjectPublication::class, ContentObjectPublication::PROPERTY_DISPLAY_ORDER_INDEX
                )
            )
        ]);
    }

    public function getIntroductionText()
    {
        if (!isset($this->introductionText))
        {
            $this->introductionText = $this->get_parent()->get_introduction_text();
        }

        return $this->introductionText;
    }

    public function getPublicationButton(
        $label, $glyph, $allowedContentObjectTypes, $parameters, $extraActions = [], array $classes = []
    )
    {
        $actionSelector = new ActionSelector(
            $this, $this->getUser()->getId(), $allowedContentObjectTypes, $parameters, $extraActions, $classes
        );

        return $actionSelector->getActionButton($label, $glyph);
    }

    public function get_publication_conditions()
    {
        $conditions = [];

        $type = $this->get_publication_type();
        switch ($type)
        {
            // Begin with the publisher condition when FROM_ME and add the
            // remaining conditions. Skip the publisher
            // condition when ALL.
            case \Chamilo\Application\Weblcms\Tool\Manager::PUBLICATION_TYPE_FROM_ME :
                $va_id = $this->getSession()->get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_VIEW_AS_ID);
                $course_id = $this->getSession()->get(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_VIEW_AS_COURSE_ID
                );
                $user_id = $this->getSession()->get(\Chamilo\Core\User\Manager::SESSION_USER_ID);

                $publisher_id =
                    (isset($va_id) && isset($course_id) && $course_id == $this->get_course_id()) ? $va_id : $user_id;

                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        ContentObjectPublication::class, ContentObjectPublication::PROPERTY_PUBLISHER_ID
                    ), new StaticConditionVariable($publisher_id)
                );

            case \Chamilo\Application\Weblcms\Tool\Manager::PUBLICATION_TYPE_ALL :
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        ContentObjectPublication::class, ContentObjectPublication::PROPERTY_COURSE_ID
                    ), new StaticConditionVariable($this->get_course_id())
                );

                if ($this->get_tool_id())
                {
                    $conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(
                            ContentObjectPublication::class, ContentObjectPublication::PROPERTY_TOOL
                        ), new StaticConditionVariable($this->get_tool_id())
                    );
                }

                $category_id = $this->getRequest()->query->get(\Chamilo\Application\Weblcms\Manager::PARAM_CATEGORY);
                if (!$category_id)
                {
                    $category_id = 0;
                }

                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        ContentObjectPublication::class, ContentObjectPublication::PROPERTY_CATEGORY_ID
                    ), new StaticConditionVariable($category_id)
                );

                break;
            default :

                $from_date_variables = new PropertyConditionVariable(
                    ContentObjectPublication::class, ContentObjectPublication::PROPERTY_FROM_DATE
                );

                $to_date_variable = new PropertyConditionVariable(
                    ContentObjectPublication::class, ContentObjectPublication::PROPERTY_TO_DATE
                );

                $time_conditions = [];

                $time_conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        ContentObjectPublication::class, ContentObjectPublication::PROPERTY_HIDDEN
                    ), new StaticConditionVariable(0)
                );

                $forever_conditions = [];

                $forever_conditions[] = new EqualityCondition($from_date_variables, new StaticConditionVariable(0));

                $forever_conditions[] = new EqualityCondition($to_date_variable, new StaticConditionVariable(0));

                $forever_condition = new AndCondition($forever_conditions);

                $between_conditions = [];

                $between_conditions[] = new ComparisonCondition(
                    $from_date_variables, ComparisonCondition::LESS_THAN_OR_EQUAL, new StaticConditionVariable(time())
                );

                $between_conditions[] = new ComparisonCondition(
                    $to_date_variable, ComparisonCondition::GREATER_THAN_OR_EQUAL, new StaticConditionVariable(time())
                );

                $between_condition = new AndCondition($between_conditions);

                $time_conditions[] = new OrCondition([$forever_condition, $between_condition]);

                $conditions[] = new AndCondition($time_conditions);
                break;
        }

        if ($this->get_search_condition())
        {
            $conditions[] = $this->get_search_condition();
        }

        $conditions[] = new InCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TYPE),
            $this->get_allowed_types()
        );

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

    /**
     * Retrieves the number of published content objects
     *
     * @return int
     */
    public function get_publication_count()
    {
        if ($this->get_publication_type() == \Chamilo\Application\Weblcms\Tool\Manager::PUBLICATION_TYPE_FROM_ME)
        {
            $count = DataManager::count_my_publications(
                $this->get_location(), $this->get_entities(), $this->get_publication_conditions(), $this->get_user_id()
            );
        }
        elseif ($this->get_publication_type() == \Chamilo\Application\Weblcms\Tool\Manager::PUBLICATION_TYPE_ALL)
        {
            $count = DataManager::count_content_object_publications(
                $this->get_publication_conditions()
            );
        }
        else
        {
            $count = DataManager::count_content_object_publications_with_view_right_granted_in_category_location(
                $this->get_location(), $this->get_entities(), $this->get_publication_conditions(), $this->get_user_id()
            );
        }

        return $count;
    }

    public function get_publication_type()
    {
        $type =
            $this->getRequest()->query->get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_BROWSE_PUBLICATION_TYPE);
        if (!$type)
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
     * Retrieves the publications
     *
     * @param int $offset
     * @param int $max_objects
     * @param array $object_table_order
     *
     * @return array An array of ContentObjectPublication objects
     */
    public function get_publications($offset = 0, $max_objects = 0, $object_table_order = [])
    {
        if (empty($this->publications))
        {

            if ($this->get_publication_type() == \Chamilo\Application\Weblcms\Tool\Manager::PUBLICATION_TYPE_FROM_ME)
            {

                $publications_resultset = DataManager::retrieve_my_publications(
                    $this->get_location(), $this->get_entities(), $this->get_publication_conditions(),
                    $object_table_order, $offset, $max_objects, $this->get_user_id()
                );
            }
            elseif ($this->get_publication_type() == \Chamilo\Application\Weblcms\Tool\Manager::PUBLICATION_TYPE_ALL)
            {
                $publications_resultset = DataManager::retrieve_content_object_publications(
                    $this->get_publication_conditions(), $object_table_order, $offset, $max_objects
                );
            }
            else
            {
                $publications_resultset =
                    DataManager::retrieve_content_object_publications_with_view_right_granted_in_category_location(
                        $this->get_location(), $this->get_entities(), $this->get_publication_conditions(),
                        $object_table_order, $offset, $max_objects, $this->get_user_id()
                    );
            }

            if ($publications_resultset)
            {
                $this->publications = $publications_resultset;
            }
        }

        if ($this->publications)
        {
            return $this->publications;
        }
    }

    public function get_search_condition()
    {
        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();
        if (isset($query) && $query != '')
        {
            $conditions[] = new ContainsCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TITLE), $query
            );

            $conditions[] = new ContainsCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION), $query
            );

            return new OrCondition($conditions);
        }

        return null;
    }

    /**
     * @return bool
     */
    public function hasCategories()
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_COURSE
            ), new StaticConditionVariable($this->get_course_id())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_TOOL
            ), new StaticConditionVariable($this->get_tool_id())
        );

        return \Chamilo\Libraries\Storage\DataManager\DataManager::count(
                ContentObjectPublicationCategory::class, new DataClassCountParameters(new AndCondition($conditions))
            ) > 0;
    }

    /**
     * @return bool
     */
    public function isCourseAdmin()
    {
        if (!isset($this->isCourseAdmin))
        {
            $this->isCourseAdmin = $this->get_course()->is_course_admin($this->get_user());
        }

        return $this->isCourseAdmin;
    }

    public function renderCategories($renderedPublications)
    {
        $publicationCategoryTree = new PublicationCategoriesTree($this);

        $html = [];

        if ($this->hasCategories())
        {

            $html[] = '<div class="col-md-3 col-lg-2 col-sm-12">';

            $categoryId = intval($this->getRequest()->query->get(\Chamilo\Application\Weblcms\Manager::PARAM_CATEGORY));

            if (!$categoryId || $categoryId == 0)
            {
                $categoryName = Translation::get('Root');
            }
            else
            {
                $category = $this->retrieve_category($categoryId);
                $this->getCategoryBreadcrumbsGenerator()->generateBreadcrumbsForCategory(
                    $this->getBreadcrumbTrail(), $this, $category
                );

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
     * @return string
     */
    public function renderIntroduction()
    {
        $course_settings_controller = CourseSettingsController::getInstance();

        if ($course_settings_controller->get_course_setting(
            $this->get_course(), CourseSettingsConnector::ALLOW_INTRODUCTION_TEXT
        ))
        {
            return $this->get_parent()->display_introduction_text($this->getIntroductionText());
        }
    }

    /**
     * @return string
     */
    public function renderPublications()
    {
        $publicationRenderer = ContentObjectPublicationListRenderer::factory(
            $this->get_parent()->get_browser_type(), $this
        );

        $actions = new TableActions(
            'Chamilo\Application\Weblcms\Table\Publication\Table',
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID
        );

        if (method_exists($this->get_parent(), 'get_additional_form_actions'))
        {
            $additional_form_actions = $this->get_parent()->get_additional_form_actions();

            foreach ($additional_form_actions as $form_action)
            {
                $actions->addAction($form_action);
            }
        }

        if ($this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $actions->addAction(
                new TableAction(
                    $this->get_url(
                        [
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_DELETE
                        ]
                    ), Translation::get('RemoveSelected', null, StringUtilities::LIBRARIES)
                )
            );

            $actions->addAction(
                new TableAction(
                    $this->get_url(
                        [
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_TOGGLE_VISIBILITY
                        ]
                    ), Translation::get('ToggleVisibility'), false
                )
            );
        }

        if ($this->is_allowed(WeblcmsRights::EDIT_RIGHT) && $this->get_parent() instanceof CategorizableInterface)
        {
            $actions->addAction(
                new TableAction(
                    $this->get_url(
                        [
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_MOVE_TO_CATEGORY
                        ]
                    ), Translation::get('MoveSelected', null, StringUtilities::LIBRARIES), false
                )
            );
        }

        $publicationRenderer->set_actions($actions);

        return $publicationRenderer->as_html();
    }

    public function renderToolHeader()
    {
        $html = [];

        $html[] = $this->renderIntroduction();
        $html[] = $this->getButtonToolbarRenderer()->render();

        if ($this->get_publication_count() > 0 &&
            $this->get_parent()->get_tool_registration()->get_section_type() == CourseSection::TYPE_DISABLED)
        {
            $html[] = Display::warning_message(Translation::get('ToolInvisible'));
        }

        return implode(PHP_EOL, $html);
    }

    private function retrieve_category($category_id)
    {
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_ID
            ), new StaticConditionVariable($category_id)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_COURSE
            ), new StaticConditionVariable($this->get_parent()->get_course_id())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_TOOL
            ), new StaticConditionVariable($this->get_parent()->get_tool_id())
        );

        $condition = new AndCondition($conditions);

        $objects = DataManager::retrieves(
            ContentObjectPublicationCategory::class, new DataClassRetrievesParameters($condition)
        );

        return $objects->current();
    }

    public function tool_category_has_new_publications($category_id)
    {
        return DataManager::tool_category_has_new_publications(
            $this->get_tool_id(), $this->get_user(), $this->get_course(), $category_id
        );
    }
}
