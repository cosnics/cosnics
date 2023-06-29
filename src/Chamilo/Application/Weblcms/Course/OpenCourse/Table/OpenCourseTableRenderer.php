<?php
namespace Chamilo\Application\Weblcms\Course\OpenCourse\Table;

use Chamilo\Application\Weblcms\Course\OpenCourse\Manager;
use Chamilo\Application\Weblcms\Course\OpenCourse\Service\OpenCourseService;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Table\CourseTableRenderer;
use Chamilo\Core\Rights\Structure\Service\AuthorizationChecker;
use Chamilo\Core\User\Roles\Storage\DataClass\Role;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumnFactory;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Course\OpenCourse\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class OpenCourseTableRenderer extends CourseTableRenderer
{
    public const TABLE_IDENTIFIER = Manager::PARAM_COURSE_ID;

    protected AuthorizationChecker $authorizationChecker;

    protected OpenCourseService $openCourseService;

    protected User $user;

    public function __construct(
        AuthorizationChecker $authorizationChecker, OpenCourseService $openCourseService, User $user,
        Translator $translator, UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory
    )
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->openCourseService = $openCourseService;
        $this->user = $user;

        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory
        );
    }

    public function getAuthorizationChecker(): AuthorizationChecker
    {
        return $this->authorizationChecker;
    }

    public function getOpenCourseService(): OpenCourseService
    {
        return $this->openCourseService;
    }

    public function getTableActions(): TableActions
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        $actions->addAction(
            new TableAction(
                $urlGenerator->fromParameters(
                    [Application::PARAM_CONTEXT => Manager::CONTEXT, Manager::PARAM_ACTION => Manager::ACTION_DELETE]
                ), $translator->trans('RemoveSelected', [], StringUtilities::LIBRARIES)
            )
        );

        return $actions;
    }

    public function getUser(): User
    {
        return $this->user;
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

        $isAuthorized =
            $this->getAuthorizationChecker()->isAuthorized($this->getUser(), Manager::CONTEXT, 'ManageOpenCourses');

        if ($isAuthorized)
        {
            $this->addColumn(
                new DataClassPropertyTableColumn(
                    Role::class, Role::PROPERTY_ROLE, $translator->trans('Role', [], Manager::CONTEXT), false
                )
            );
        }
    }

    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $courseRecord): string
    {
        if ($column instanceof DataClassPropertyTableColumn)
        {
            switch ($column->get_class_name())
            {
                case Course::class :
                {
                    if ($column->get_name() == Course::PROPERTY_TITLE)
                    {
                        $viewUrl = $this->getUrlGenerator()->fromParameters([
                            Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::CONTEXT,
                            Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE,
                            \Chamilo\Application\Weblcms\Manager::PARAM_COURSE => $courseRecord[DataClass::PROPERTY_ID]
                        ]);

                        $course_title = parent::renderCell($column, $resultPosition, $courseRecord);

                        return '<a href="' . $viewUrl . '">' . $course_title . '</a>';
                    }
                    break;
                }
                case Role::class :
                {
                    if ($column->get_name() == Role::PROPERTY_ROLE)
                    {
                        $courseObject = new Course($courseRecord);
                        $roles = $this->getOpenCourseService()->getRolesForOpenCourse($courseObject);

                        $rolesHtml = [];

                        $rolesHtml[] = '<select>';
                        foreach ($roles as $role)
                        {
                            $rolesHtml[] = '<option>' . $role->getRole() . '</option>';
                        }
                        $rolesHtml[] = '</select>';

                        return implode(PHP_EOL, $rolesHtml);
                    }
                    break;
                }
            }
        }

        return parent::renderCell($column, $resultPosition, $courseRecord);
    }

}
