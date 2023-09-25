<?php
namespace Chamilo\Application\Weblcms\Course\Table;

use Chamilo\Application\Weblcms\Course\Manager;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\CourseSettingsConnector;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumnFactory;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordListTableRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Course\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UnsubscribedCourseTableRenderer extends RecordListTableRenderer implements TableRowActionsSupport
{

    protected User $user;

    protected UserService $userService;

    public function __construct(
        UserService $userService, User $user, Translator $translator, UrlGenerator $urlGenerator,
        ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory
    )
    {
        $this->user = $user;
        $this->userService = $userService;

        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory
        );
    }

    private function canAccessCourse($course): bool
    {
        $user = $this->getUser();
        $course_id = $course[DataClass::PROPERTY_ID];
        $courseObject = DataManager::retrieve_by_id(Course::class, $course_id);

        if ($this->isTeacher($course_id))
        {
            $allowed = true;
        }
        else
        {
            $course_settings_controller = CourseSettingsController::getInstance();
            $course_access = $course_settings_controller->get_course_setting(
                $courseObject, CourseSettingsConnector::COURSE_ACCESS
            );

            if ($course_access == CourseSettingsConnector::COURSE_ACCESS_CLOSED)
            {
                $allowed = false;
            }
            else
            {
                $open_course_access_type = $course_settings_controller->get_course_setting(
                    $courseObject, CourseSettingsConnector::OPEN_COURSE_ACCESS_TYPE
                );

                $is_subscribed = CourseDataManager::is_subscribed($course_id, $user);

                if ($is_subscribed || $open_course_access_type == CourseSettingsConnector::OPEN_COURSE_ACCESS_WORLD)
                {
                    $allowed = true;
                }
                elseif ($open_course_access_type == CourseSettingsConnector::OPEN_COURSE_ACCESS_PLATFORM &&
                    !$user->is_anonymous_user())
                {
                    $allowed = true;
                }
                else
                {
                    $allowed = false;
                }
            }
        }

        return $allowed;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }

    protected function initializeColumns(): void
    {
        $translator = $this->getTranslator();

        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(Course::class, Course::PROPERTY_VISUAL_CODE)
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(Course::class, Course::PROPERTY_TITLE)
        );

        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                Course::class, Course::PROPERTY_TITULAR_ID, null, false
            )
        );

        $this->addColumn(
            new DataClassPropertyTableColumn(
                CourseType::class, CourseType::PROPERTY_TITLE, $translator->trans('CourseType', [], Manager::CONTEXT)
            )
        );
    }

    private function isTeacher($courseIdentifier): bool
    {
        $user = $this->getUser();

        if ($courseIdentifier != null)
        {
            $relation =
                CourseDataManager::retrieve_course_user_relation_by_course_and_user($courseIdentifier, $user->getId());

            if (($relation && $relation->get_status() == 1) || $user->isPlatformAdmin())
            {
                return true;
            }
            else
            {
                return CourseDataManager::is_teacher_by_platform_group_subscription($courseIdentifier, $user);
            }
        }

        return false;
    }

    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $course): string
    {
        $translator = $this->getTranslator();

        if ($column instanceof DataClassPropertyTableColumn)
        {
            switch ($column->get_class_name())
            {
                case Course::class :
                {
                    switch ($column->get_name())
                    {
                        case Course::PROPERTY_TITLE :
                            return parent::renderCell($column, $resultPosition, $course);
                        case Course::PROPERTY_TITULAR_ID :
                            return $this->getUserService()->getUserFullNameByIdentifier(
                                $course[Course::PROPERTY_TITULAR_ID]
                            );
                    }
                    break;
                }
                case CourseType::class :
                {
                    if ($column->get_name() == CourseType::PROPERTY_TITLE)
                    {
                        $course_type_title = $course[Course::PROPERTY_COURSE_TYPE_TITLE];

                        return !$course_type_title ? $translator->trans('NoCourseType', [], Manager::CONTEXT) :
                            $course_type_title;
                    }
                }
            }
        }

        return parent::renderCell($column, $resultPosition, $course);
    }

    public function renderTableRowActions(TableResultPosition $resultPosition, $course): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        if ($this->canAccessCourse($course))
        {
            $viewUrl = $urlGenerator->fromParameters([
                Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::CONTEXT,
                Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE,
                \Chamilo\Application\Weblcms\Manager::PARAM_COURSE => $course[DataClass::PROPERTY_ID]
            ]);

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('ViewCourseHome', [], Manager::CONTEXT), new FontAwesomeGlyph('home'), $viewUrl,
                    ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if (CourseManagementRights::getInstance()->is_allowed_management(
            CourseManagementRights::DIRECT_SUBSCRIBE_RIGHT, $course[DataClass::PROPERTY_ID]
        ))
        {
            $subscribeUrl = $urlGenerator->fromParameters([
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_SUBSCRIBE,
                Manager::PARAM_COURSE_ID => $course[DataClass::PROPERTY_ID]
            ]);

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Subscribe', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('plus-circle'), $subscribeUrl, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        return $toolbar->render();
    }
}
