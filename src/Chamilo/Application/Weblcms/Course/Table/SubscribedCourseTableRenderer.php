<?php
namespace Chamilo\Application\Weblcms\Course\Table;

use Chamilo\Application\Weblcms\Course\Manager;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Storage\DataManager;
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
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Course\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SubscribedCourseTableRenderer extends RecordListTableRenderer implements TableRowActionsSupport
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

    protected function isSubscribedAsCourseAdmin(int $courseIdentifier, User $user): bool
    {
        return DataManager::is_teacher_by_direct_subscription($courseIdentifier, $user->getId()) ||
            DataManager::is_teacher_by_platform_group_subscription($courseIdentifier, $user);
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

        if (DataManager::is_user_direct_subscribed_to_course(
                $this->getUser()->getId(), $course[DataClass::PROPERTY_ID]
            ) && CourseManagementRights::getInstance()->is_allowed_management(
                CourseManagementRights::DIRECT_UNSUBSCRIBE_RIGHT, $course[DataClass::PROPERTY_ID]
            ) && !$this->isSubscribedAsCourseAdmin($course[DataClass::PROPERTY_ID], $this->getUser()))
        {
            $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);

            $unsubscribeUrl = $urlGenerator->fromParameters([
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_UNSUBSCRIBE,
                Manager::PARAM_COURSE_ID => $course[DataClass::PROPERTY_ID]
            ]);

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Unsubscribe', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('minus-square'), $unsubscribeUrl, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        return $toolbar->render();
    }
}
