<?php

namespace Chamilo\Application\Weblcms\Tool;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\CourseSettingsConnector;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\PublicationDetailTemplate;
use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Renderer\ToolList\Type\ShortcutToolListRenderer;
use Chamilo\Application\Weblcms\Rights\Entities\CourseGroupEntity;
use Chamilo\Application\Weblcms\Rights\Entities\CoursePlatformGroupEntity;
use Chamilo\Application\Weblcms\Rights\Entities\CourseUserEntity;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Service\ServiceFactory;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSetting;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseTool;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Service\CategoryBreadcrumbsGenerator;
use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Introduction\Storage\DataClass\Introduction;
use Chamilo\Core\Repository\Viewer\ActionSelector;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Rights\Exception\RightsLocationNotFoundException;
use Chamilo\Core\Rights\RightsUtil;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Architecture\Factory\ApplicationFactory;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 * This is the base class for all tools used in applications.
 *
 * @author Tim De Pauw
 * @author Hans De Bisschop - Erasmus Hogeschool Brussel - Refactoring
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring, Cleanup
 */
abstract class Manager extends Application
{
    /**
     * **************************************************************************************************************
     * Actions *
     * **************************************************************************************************************
     */
    const ACTION_BROWSE = 'Browser';

    const ACTION_BUILD_COMPLEX_CONTENT_OBJECT = 'ComplexBuilder';

    const ACTION_CREATE_BOOKMARK = 'CourseBookmarkCreator';

    const ACTION_DELETE = 'Deleter';

    const ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT = 'ComplexDisplay';

    const ACTION_EDIT_RIGHTS = 'RightsEditor';

    const ACTION_EVALUATE_TOOL_PUBLICATION = 'Evaluate';

    const ACTION_HIDE_PUBLICATION = 'HidePublication';

    const ACTION_MAIL_PUBLICATION = 'PublicationMailer';

    const ACTION_MANAGE_CATEGORIES = 'CategoryManager';

    const ACTION_MOVE = 'Mover';

    const ACTION_MOVE_TO_CATEGORY = 'CategoryMover';

    const ACTION_PUBLISH = 'Publisher';

    const ACTION_PUBLISH_INTRODUCTION = 'IntroductionPublisher';

    const ACTION_SHOW_PUBLICATION = 'ShowPublication';

    const ACTION_TOGGLE_VISIBILITY = 'ToggleVisibility';

    const ACTION_UPDATE = 'Updater';

    const ACTION_UPDATE_CONTENT_OBJECT = 'ContentObjectUpdater';

    const ACTION_UPDATE_PUBLICATION = 'PublicationUpdater';

    const ACTION_VIEW = 'Viewer';

    const ACTION_VIEW_ATTACHMENT = 'AttachmentViewer';

    const ACTION_VIEW_REPORTING_TEMPLATE = 'ReportingViewer';

    const DEFAULT_ACTION = self::ACTION_BROWSE;

    /**
     * **************************************************************************************************************
     * URL Parameters *
     * **************************************************************************************************************
     */
    const PARAM_ACTION = 'tool_action';

    const PARAM_BROWSER_TYPE = 'browser';

    const PARAM_BROWSE_PUBLICATION_TYPE = 'pub_type';

    const PARAM_COMPLEX_ID = 'cid';

    const PARAM_MOVE = 'move';

    const PARAM_MOVE_DIRECTION = 'move_direction';

    const PARAM_MOVE_DIRECTION_DOWN = 1;

    /**
     * **************************************************************************************************************
     * Move Directions *
     * **************************************************************************************************************
     */
    const PARAM_MOVE_DIRECTION_UP = - 1;

    const PARAM_OBJECT_ID = 'object_id';

    const PARAM_PUBLICATION_ID = 'publication';

    const PARAM_PUBLISH_MODE = 'publish_mode';

    const PARAM_TEMPLATE_NAME = 'template_name';

    const PARAM_VIEW_AS_COURSE_ID = 'view_as_course_id';

    const PARAM_VIEW_AS_ID = 'view_as_id';

    const PARAM_VISIBILITY = 'visible';

    /**
     * **************************************************************************************************************
     * Publication Types *
     * **************************************************************************************************************
     */
    const PUBLICATION_TYPE_ALL = 1;

    const PUBLICATION_TYPE_FOR_ME = 2;

    const PUBLICATION_TYPE_FROM_ME = 3;

    const PUBLISH_DEPENDENT = 1;

    /**
     * **************************************************************************************************************
     * Publish dependent on tool type
     * **************************************************************************************************************
     */
    const PUBLISH_INDEPENDENT = 0;

    const PUBLISH_MODE_FULL = 2;

    const PUBLISH_MODE_QUICK = 1;

    const PUBLISH_TYPE_AUTO = 2;

    const PUBLISH_TYPE_BOTH = 3;

    const PUBLISH_TYPE_FORM = 1;

    /**
     * **************************************************************************************************************
     * Construct Functionality *
     * **************************************************************************************************************
     */

    private $introduction_cache;

    /**
     * Constructor.
     *
     * @param $parent Application - The component in which this tool runs
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        if ($this->get_course_id() && !$this->get_parent()->is_tool_accessible())
        {
            throw new NotAllowedException();
        }
        $this->set_parameter(
            \Chamilo\Application\Weblcms\Manager::PARAM_CATEGORY,
            Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_CATEGORY)
        );
        $this->set_parameter(self::PARAM_BROWSER_TYPE, $this->get_browser_type());
    }

    public function run()
    {
        return $this->getApplicationFactory()->getApplication(
            Action\Manager::context(), new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
        )->run();
    }

    /**
     * Adds a breadcrumb to the browser component
     *
     * @param BreadcrumbTrail $breadcrumbTrail
     */
    protected function addBrowserBreadcrumb(BreadcrumbTrail $breadcrumbTrail)
    {
        $breadcrumbTrail->add(
            new Breadcrumb(
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE), array(self::PARAM_PUBLICATION_ID)),
                Translation::getInstance()->getTranslation('BrowserComponent', [], __NAMESPACE__)
            )
        );
    }

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $this->addBrowserBreadcrumb($breadcrumbtrail);
    }

    /**
     * Converts a tool class name to the corresponding tool name.
     *
     * @param $class string The class name.
     *
     * @return string The tool name.
     */
    public static function class_to_type($class)
    {
        return (string) StringUtilities::getInstance()->createString(str_replace('Tool', '', $class))->underscored()
            ->__toString();
    }

    public function display_course_menus($tools, $show_introduction_text = false)
    {
        $html = [];

        $html[] = $this->renderShortcuts($tools);
        // $html[] = $this->renderHomeActions();
        $html[] = '<div class="clearfix"></div>';

        return implode(PHP_EOL, $html);
    }

    public function display_introduction_text($introduction_text)
    {
        $html = [];

        if ($introduction_text)
        {
            $toolbar = new ButtonToolBar();
            $buttonGroup = new ButtonGroup();

            $repositoryRightsService = RightsService::getInstance();
            $weblcmsRightsService = ServiceFactory::getInstance()->getRightsService();

            $canEditContentObject = $repositoryRightsService->canEditContentObject(
                $this->getUser(), $introduction_text->get_content_object()
            );

            $canEditPublicationContentObject = $weblcmsRightsService->canUserEditPublication(
                $this->getUser(), $introduction_text, $this->get_course()
            );

            if ($this->is_allowed(WeblcmsRights::EDIT_RIGHT) &&
                ($canEditContentObject || $canEditPublicationContentObject))
            {
                $buttonGroup->addButton(
                    new Button(
                        Translation::get('Edit', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                        $this->get_url(
                            array(
                                self::PARAM_ACTION => self::ACTION_UPDATE_CONTENT_OBJECT,
                                self::PARAM_PUBLICATION_ID => $introduction_text->get_id()
                            )
                        ), Button::DISPLAY_ICON_AND_LABEL
                    )
                );
            }

            if ($this->is_allowed(WeblcmsRights::DELETE_RIGHT))
            {
                $buttonGroup->addButton(
                    new Button(
                        Translation::get('Delete', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                        $this->get_url(
                            array(
                                self::PARAM_ACTION => self::ACTION_DELETE,
                                self::PARAM_PUBLICATION_ID => $introduction_text->get_id()
                            )
                        ), Button::DISPLAY_ICON_AND_LABEL, true
                    )
                );
            }

            if ($buttonGroup->hasButtons())
            {
                $toolbar->addButtonGroup($buttonGroup);
            }

            $content_object = $introduction_text->get_content_object();

            $rendition_implementation = ContentObjectRenditionImplementation::factory(
                $content_object, ContentObjectRendition::FORMAT_HTML, ContentObjectRendition::VIEW_DESCRIPTION, $this
            );

            $html[] = '<div class="panel panel-default">';
            $html[] = '<div class="panel-heading">';
            $html[] = '<h3 class="panel-title">' . $introduction_text->get_content_object()->get_title() . '</h3>';
            $html[] = '</div>';
            $html[] = '<div class="panel-body">';
            $html[] = $rendition_implementation->render();

            if ($toolbar->hasItems())
            {
                $renderer = new ButtonToolBarRenderer($toolbar);
                $html[] = '<div class="pull-right">' . $renderer->render() . '</div><div class="clearfix"></div>';
            }

            $html[] = '</div>';
            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * Launches the tool with a given type
     *
     * @param $type string
     * @param $application Application
     */
    public static function factory_and_launch($namespace, $application)
    {
        $class = $namespace . '\Manager';

        if (!class_exists($class))
        {
            throw new UserException(Translation::get('ToolTypeDoesNotExist', array('type' => $namespace)));
        }

        $container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
        $applicationFactory = $container->get(ApplicationFactory::class);

        return $applicationFactory->getApplication(
            $namespace, new ApplicationConfiguration($application->getRequest(), $application->get_user(), $application)
        )->run();
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Service\CategoryBreadcrumbsGenerator
     */
    public function getCategoryBreadcrumbsGenerator()
    {
        return $this->getService(CategoryBreadcrumbsGenerator::class);
    }

    public function get_access_details_toolbar_item($parent)
    {
        if (Request::get(self::PARAM_PUBLICATION_ID))
        {
            $url = $this->get_parent()->get_url(
                array(
                    self::PARAM_ACTION => self::ACTION_VIEW_REPORTING_TEMPLATE,
                    self::PARAM_PUBLICATION_ID => Request::get(self::PARAM_PUBLICATION_ID),
                    self::PARAM_TEMPLATE_NAME => PublicationDetailTemplate::class
                )
            );

            return new Button(
                Translation::get('AccessDetails'), new FontAwesomeGlyph('chart-pie'), $url
            );
        }
        else
        {
            return new ToolbarItem('');
        }
    }

    public static function get_allowed_types()
    {
        return [];
    }

    public function get_available_browser_types()
    {
        $browser_types = [];
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_LIST;
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_TABLE;

        return $browser_types;
    }

    /**
     * **************************************************************************************************************
     * Main Functionality *
     * **************************************************************************************************************
     */
    public function get_browser_type()
    {
        $browser_type = Request::get(self::PARAM_BROWSER_TYPE);

        if ($browser_type && in_array($browser_type, $this->get_available_browser_types()))
        {
            return $browser_type;
        }
        else
        {
            $available_browser_types = $this->get_available_browser_types();

            return $available_browser_types[0];
        }
    }

    /**
     *
     * @see Application::get_category()
     */
    public function get_category($id)
    {
        return $this->get_parent()->get_category($id);
    }

    public function get_complex_builder_url($pid)
    {
        return $this->get_url(
            array(self::PARAM_ACTION => self::ACTION_BUILD_COMPLEX_CONTENT_OBJECT, self::PARAM_PUBLICATION_ID => $pid)
        );
    }

    public function get_complex_display_url($pid)
    {
        return $this->get_url(
            array(self::PARAM_ACTION => self::ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT, self::PARAM_PUBLICATION_ID => $pid)
        );
    }

    public function get_content_object_display_attachment_url($attachment)
    {
        return $this->get_url(
            array(self::PARAM_ACTION => self::ACTION_VIEW_ATTACHMENT, self::PARAM_OBJECT_ID => $attachment->get_id())
        );
    }

    /**
     *
     * @return Course
     */
    public function get_course()
    {
        return $this->get_parent()->get_course();
    }

    public function get_course_group()
    {
        return $this->get_parent()->get_course_group();
    }

    public function get_course_groups()
    {
        return $this->get_parent()->get_course_groups();
    }

    public function get_course_id()
    {
        return $this->get_parent()->get_course_id();
    }

    public function get_entities()
    {
        $entities = [];
        $entities[CourseGroupEntity::ENTITY_TYPE] = CourseGroupEntity::getInstance($this->get_course_id());
        $entities[CourseUserEntity::ENTITY_TYPE] = CourseUserEntity::getInstance();
        $entities[CoursePlatformGroupEntity::ENTITY_TYPE] = CoursePlatformGroupEntity::getInstance(
            $this->get_course_id()
        );

        return $entities;
    }

    public function get_introduction_text()
    {
        $course_id = $this->get_course_id();
        $tool_id = $this->get_tool_id();

        if (is_null($this->introduction_cache[$course_id][$tool_id]))
        {
            $this->introduction_cache[$course_id][$tool_id] = false;

            $publication = DataManager::retrieve_introduction_publication_by_course_and_tool(
                $course_id, $tool_id
            );

            if ($publication)
            {
                $this->introduction_cache[$course_id][$tool_id] = $publication;
            }
            else
            {
                $this->introduction_cache[$course_id][$tool_id] = null;
            }
        }

        return $this->introduction_cache[$course_id][$tool_id];
    }

    /**
     *
     * @see WeblcmsManager::get_last_visit_date()
     */
    public function get_last_visit_date()
    {
        return $this->get_parent()->get_last_visit_date();
    }

    /**
     * Retrieves the location in the given category, or the location in the current category by publication id
     *
     * @param $category_id type
     *
     * @return type
     */
    public function get_location($category_id = null)
    {
        $locations = $this->get_locations($category_id);

        return $locations[0];
    }

    /**
     * Retrieves the locations in the given category, or the locations in the current category by publication id
     *
     * @param $category_id type
     *
     * @return type array
     */
    public function get_locations($category_id = null)
    {
        $course_id = $this->get_course_id();

        if (!isset($category_id))
        {
            $category_id = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_CATEGORY);
        }

        $publications = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION);

        $rights_util = WeblcmsRights::getInstance();

        if ($publications)
        {
            if (!is_array($publications))
            {
                $publications = array($publications);
            }

            foreach ($publications as $publication)
            {
                $locations[] = $rights_util->get_weblcms_location_by_identifier_from_courses_subtree(
                    WeblcmsRights::TYPE_PUBLICATION, $publication, $course_id
                );
            }
        }
        else
        {
            if ($category_id)
            {
                $locations[] = $rights_util->get_weblcms_location_by_identifier_from_courses_subtree(
                    WeblcmsRights::TYPE_COURSE_CATEGORY, $category_id, $course_id
                );
            }
            else
            {
                $course_tool = $this->get_parent()->get_tool_registration();
                $course_tool_name = $course_tool->get_name();

                if ($course_tool_name && $course_tool_name != 'Rights')
                {
                    $locations[] = $rights_util->get_weblcms_location_by_identifier_from_courses_subtree(
                        WeblcmsRights::TYPE_COURSE_MODULE, $course_tool->get_id(), $course_id
                    );
                }
                else
                {
                    $locations[] = $rights_util->get_courses_subtree_root($course_id);
                }
            }
        }

        return $locations;
    }

    public static function get_packages_from_filesystem($type = null)
    {
        $types = [];

        $directories = Filesystem::get_directory_content(
            Path::getInstance()->namespaceToFullPath(__NAMESPACE__ . '\Implementation'), Filesystem::LIST_DIRECTORIES,
            false
        );

        foreach ($directories as $directory)
        {
            $namespace = __NAMESPACE__ . '\Implementation\\' . $directory;

            if (Package::exists($namespace))
            {
                $types[] = $namespace;
            }
        }

        return $types;
    }

    public static function get_pcattree_parents($pcattree)
    {
        $parent = DataManager::retrieve_by_id(
            ContentObjectPublication::class, $pcattree
        );

        $parents[] = $parent;

        while ($parent && $parent->get_parent() != 0)
        {
            $parent = DataManager::retrieve_by_id(
                ContentObjectPublication::class, $parent->get_parent()
            );

            $parents[] = $parent;
        }
        $parents = array_reverse($parents);

        return $parents;
    }

    /**
     * Returns the name of the tool
     *
     * @return String
     */
    public function get_tool_id()
    {
        return $this->get_tool_registration()->get_name();
    }

    /**
     * Returns the registration object of the tool
     *
     * @return CourseTool
     */
    public function get_tool_registration()
    {
        return $this->get_parent()->get_tool_registration();
    }

    public static function get_tool_type_namespace($type)
    {
        return 'Chamilo\Application\Weblcms\Tool\Implementation\\' . $type;
    }

    public function get_user_id()
    {
        $va_id = Session::get(self::PARAM_VIEW_AS_ID);
        $course_id = Session::get(self::PARAM_VIEW_AS_COURSE_ID);
        // fake the id with the set "login as id" only if we're in the right
        // course
        if (isset($va_id) && isset($course_id))
        {
            if ($course_id == $this->get_course_id())
            {
                return $va_id;
            }
        }

        return parent::get_user_id();
    }

    public function get_user_info($user_id)
    {
        return $this->get_parent()->get_user_info($user_id);
    }

    /**
     * Returns the visible tools for this course
     *
     * @return CourseTool[]
     */
    public function get_visible_tools()
    {
        $tools = [];

        $course_tools = DataManager::retrieves(
            CourseTool::class, new DataClassRetrievesParameters()
        );

        $edit_right = $this->is_allowed(WeblcmsRights::EDIT_RIGHT);

        $course_settings_controller = CourseSettingsController::getInstance();

        foreach ($course_tools as $tool)
        {
            $tool_active = $course_settings_controller->get_course_setting(
                $this->get_course(), CourseSetting::COURSE_SETTING_TOOL_ACTIVE, $tool->get_id()
            );

            $tool_visible = $course_settings_controller->get_course_setting(
                $this->get_course(), CourseSetting::COURSE_SETTING_TOOL_VISIBLE, $tool->get_id()
            );

            if ($tool_active && ($edit_right || $tool_visible))
            {
                $tools[] = $tool;
            }
        }

        return $tools;
    }

    /**
     * Check if the current user has a given right in this tool
     *
     * @param $right int
     * @param $publication ContentObjectPublication
     * @param int $category_id
     *
     * @return bool True if the current user has the right
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function is_allowed($right, $publication = null, $category_id = null)
    {
        try
        {
            if (empty($this->get_course_id()))
            {
                $this->getExceptionLogger()->logException(
                    new Exception('The is_allowed method has been called without a valid course_id'),
                    ExceptionLoggerInterface::EXCEPTION_LEVEL_FATAL_ERROR
                );
            }

            if ($publication instanceof ContentObjectPublication && !empty($this->get_course_id()) &&
                $publication->get_course_id() != $this->get_course_id())
            {
                throw new NotAllowedException();
            }

            if (is_array($publication) && array_key_exists('course_id', $publication) &&
                !empty($this->get_course_id()) && $publication['course_id'] != $this->get_course_id())
            {
                throw new NotAllowedException();
            }

            $category = null;
            if ($category_id != 0)
            {
                $category = DataManager::retrieve_by_id(
                    ContentObjectPublicationCategory::class, $category_id
                );

                if ($category instanceof ContentObjectPublicationCategory && !empty($this->get_course_id()) &&
                    $category->get_course() != $this->get_course_id())
                {
                    throw new NotAllowedException();
                }
            }

            $studentview = Session::retrieve('studentview');
            if ($studentview == 1)
            {
                return false;
            }
            // add check for student view/login as
            $id = Session::get_user_id();
            $va_id = Session::get(self::PARAM_VIEW_AS_ID);
            $course_id = Session::get(self::PARAM_VIEW_AS_COURSE_ID);

            // fake the id with the set "login as id" only if we're in the right
            // course
            if (isset($va_id) && isset($course_id))
            {
                if ($course_id == $this->get_course_id())
                {
                    $id = $va_id;
                }
            }

            if ($this->get_parent()->is_teacher()) // also checks "view as" id.
            {
                return true;
            }

            if ($publication)
            {
                if ($publication instanceof ContentObjectPublication)
                {
                    $category_id = $publication->get_category_id();
                    $publication_id = $publication->get_id();
                    $publisher_id = $publication->get_publisher_id();
                    $hidden = !$publication->is_visible_for_target_users();
                }
                else
                {
                    $category_id = $publication[ContentObjectPublication::PROPERTY_CATEGORY_ID];
                    $publication_id = $publication[ContentObjectPublication::PROPERTY_ID];
                    $publisher_id = $publication[ContentObjectPublication::PROPERTY_PUBLISHER_ID];
                    $hidden = $publication[ContentObjectPublication::PROPERTY_HIDDEN];

                    $fromDate = $publication[ContentObjectPublication::PROPERTY_FROM_DATE];
                    $toDate = $publication[ContentObjectPublication::PROPERTY_TO_DATE];
                    if (!empty($fromDate) && !empty($toDate))
                    {
                        $hidden = $hidden || $fromDate > time() || $toDate < time();
                    }
                }

                if ($category_id != 0)
                {
                    $category = DataManager::retrieve_by_id(
                        ContentObjectPublicationCategory::class, $category_id
                    );

                    if ($category instanceof ContentObjectPublicationCategory && !$category->is_recursive_visible())
                    {
                        return false;
                    }
                }

                if ($publisher_id == $id)
                {
                    return true; // the publisher has all the rights
                }

                if ($hidden && $right == WeblcmsRights::VIEW_RIGHT)
                {
                    return WeblcmsRights::getInstance()->is_allowed_in_courses_subtree(
                        WeblcmsRights::EDIT_RIGHT, $publication_id, WeblcmsRights::TYPE_PUBLICATION,
                        $this->get_course_id(), $id
                    );
                }

                return WeblcmsRights::getInstance()->is_allowed_in_courses_subtree(
                    $right, $publication_id, WeblcmsRights::TYPE_PUBLICATION, $this->get_course_id(), $id
                );
            }
            else
            {
                if (is_null($category_id))
                {
                    $category_id = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_CATEGORY);
                }

                if ($category_id && $category_id !== 0)
                {
                    $category = DataManager::retrieve_by_id(
                        ContentObjectPublicationCategory::class, $category_id
                    );

                    if (empty($category))
                    {
                        throw new ObjectNotExistException(
                            Translation::get('ContentObjectPublicationCategory'), $category_id
                        );
                    }

                    if ($category->is_recursive_visible())
                    {
                        return WeblcmsRights::getInstance()->is_allowed_in_courses_subtree(
                            $right, $category_id, WeblcmsRights::TYPE_COURSE_CATEGORY, $this->get_course_id(), $id
                        );
                    }
                    else
                    {
                        return false;
                    }
                }

                if ($this->get_tool_id() == 'home')
                {
                    return WeblcmsRights::getInstance()->is_allowed_in_courses_subtree(
                        $right, 0, RightsUtil::TYPE_ROOT, $this->get_course_id(), $id
                    );
                }

                $tool_registration = $this->get_tool_registration();
                $course_settings_controller = CourseSettingsController::getInstance();

                $module_visible = $course_settings_controller->get_course_setting(
                    $this->get_course(), CourseSetting::COURSE_SETTING_TOOL_VISIBLE, $tool_registration->get_id()
                );

                if (!$module_visible)
                {
                    return false;
                }

                return WeblcmsRights::getInstance()->is_allowed_in_courses_subtree(
                    $right, $tool_registration->get_id(), WeblcmsRights::TYPE_COURSE_MODULE, $this->get_course_id(), $id
                );
            }
        }
        catch (RightsLocationNotFoundException $ex)
        {
            $this->getExceptionLogger()->logException($ex, ExceptionLoggerInterface::EXCEPTION_LEVEL_FATAL_ERROR);

            return false;
        }
    }

    /**
     *
     * @return string
     */
    public function renderHomeActions()
    {
        $courseSettingsController = CourseSettingsController::getInstance();
        $introductionTextAllowed = $courseSettingsController->get_course_setting(
            $this->get_course(), CourseSettingsConnector::ALLOW_INTRODUCTION_TEXT
        );

        $buttonToolbar = new ButtonToolBar();

        $is_subscribed = \Chamilo\Application\Weblcms\Course\Storage\DataManager::is_subscribed(
            $this->get_course(), $this->get_user()
        );

        // if (! $is_subscribed)
        // {
        // $params = [];
        // $params[Application::PARAM_ACTION] = Manager::ACTION_CREATE_BOOKMARK;
        //
        // $bookmark_url = $this->get_parent()->get_url($params);
        //
        // // $onclick = '" onclick="javascript:openPopup(\'' . $bookmark_url . '\'); return false;';
        //
        // $buttonToolbar->addItem(
        // new Button(
        // Translation::get('MakeBookmark'),
        // null,
        // $bookmark_url,
        // Button::DISPLAY_ICON_AND_LABEL,
        // false,
        // null,
        // '_blank'));
        // }

        if ($introductionTextAllowed)
        {
            $introduction_text = $this->get_introduction_text();

            if (!$introduction_text)
            {
                if ($this->is_allowed(WeblcmsRights::EDIT_RIGHT))
                {
                    $parameters = $this->get_parameters();
                    $parameters[self::PARAM_ACTION] = self::ACTION_PUBLISH_INTRODUCTION;

                    $actionSelector = new ActionSelector(
                        $this, $this->getUser()->getId(), array(Introduction::class), $parameters
                    );

                    $buttonToolbar->addItem(
                        $actionSelector->getActionButton(
                            Translation::get('PublishIntroductionText', null, StringUtilities::LIBRARIES),
                            new FontAwesomeGlyph('book')
                        )
                    );
                }
            }
        }

        $html = [];

        if ($buttonToolbar->hasItems())
        {
            $buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
            $html[] = $buttonToolbarRenderer->render();
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * Renders the page title
     *
     * @return string
     */
    protected function renderPageTitle()
    {
        $html = [];

        $html[] = '<div class="row">';
        $html[] = '<div class="col-md-9">';
        $html[] = parent::renderPageTitle();
        $html[] = '</div>';

        if (Page::getInstance()->isFullPage())
        {
            $visible_tools = $this->get_visible_tools();

            $html[] = '<div class="col-md-3">';
            $html[] = $this->display_course_menus($visible_tools);
            $html[] = '</div>';
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param unknown $tools
     */
    public function renderShortcuts($tools)
    {
        $courseSettingsController = CourseSettingsController::getInstance();

        $toolShortcut = $courseSettingsController->get_course_setting(
            $this->get_course(), CourseSettingsConnector::TOOL_SHORTCUT_MENU
        );

        $html = [];

        if ($toolShortcut && count($tools) > 0)
        {
            $renderer = new ShortcutToolListRenderer($this, $tools);

            $html[] = '<div class="pull-right weblcms-tool-navigation">';
            $html[] = $renderer->toHtml();
            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }

    public function tool_has_new_publications($tool_name, $course = null)
    {
        return $this->get_parent()->tool_has_new_publications($tool_name, $course);
    }

    /**
     * Converts a tool name to the corresponding class name.
     *
     * @param $tool string The tool name.
     *
     * @return string The class name.
     */
    public static function type_to_class($tool)
    {
        $toolName = (string) StringUtilities::getInstance()->createString($tool)->upperCamelize();

        return __NAMESPACE__ . '\Implementation\\' . $toolName . '\\' . $toolName . 'Tool';
    }
}
