<?php
namespace Chamilo\Application\Weblcms\Table;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Storage\DataClass\CommonRequest;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseRequest;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumnFactory;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AdminRequestTableRenderer extends DataClassListTableRenderer implements TableActionsSupport
{
    public const PROPERTY_COURSE_NAME = 'course_name';
    public const PROPERTY_USER_NAME = 'user_name';

    public const TABLE_IDENTIFIER = Manager::PARAM_REQUEST;

    protected DatetimeUtilities $datetimeUtilities;

    protected UserService $userService;

    public function __construct(
        DatetimeUtilities $datetimeUtilities, UserService $userService, Translator $translator,
        UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory
    )
    {
        $this->datetimeUtilities = $datetimeUtilities;
        $this->userService = $userService;

        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory
        );
    }

    public function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->datetimeUtilities;
    }

    public function getTableActions(): TableActions
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        $actions->addAction(
            new TableAction(
                $urlGenerator->fromParameters(
                    [
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        Application::PARAM_ACTION => Manager::ACTION_COURSE_USER_SUBSCRIPTION_REQUEST_GRANT
                    ]
                ), $translator->trans('GrantSelected', [], StringUtilities::LIBRARIES), false
            )
        );

        return $actions;
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }

    protected function initializeColumns()
    {
        $translator = $this->getTranslator();

        $this->addColumn(
            new StaticTableColumn(self::PROPERTY_USER_NAME, $translator->trans('UserName', [], Manager::CONTEXT))
        );
        $this->addColumn(
            new StaticTableColumn(self::PROPERTY_COURSE_NAME, $translator->trans('CourseName', [], Manager::CONTEXT))
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                CourseRequest::class, CommonRequest::PROPERTY_SUBJECT
            )
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                CourseRequest::class, CommonRequest::PROPERTY_MOTIVATION
            )
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                CourseRequest::class, CommonRequest::PROPERTY_CREATION_DATE
            )
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                CourseRequest::class, CommonRequest::PROPERTY_DECISION_DATE
            )
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\CourseRequest $request
     *
     * @throws \ReflectionException
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $request): string
    {
        $datetimeUtilities = $this->getDatetimeUtilities();

        switch ($column->get_name())
        {
            case CommonRequest::PROPERTY_MOTIVATION :
                $motivation = strip_tags(parent::renderCell($column, $resultPosition, $request));
                if (strlen($motivation) > 175)
                {
                    $motivation = mb_substr($motivation, 0, 200) . '&hellip;';
                }

                return $motivation;
            case self::PROPERTY_USER_NAME :
                return $this->getUserService()->getUserFullNameByIdentifier(
                    $request->get_user_id()
                );
            case self::PROPERTY_COURSE_NAME :
                return DataManager::retrieve_by_id(Course::class, $request->get_course_id())->get_title();
            case CommonRequest::PROPERTY_SUBJECT :
                return $request->get_subject();

            case CommonRequest::PROPERTY_CREATION_DATE :
                return $datetimeUtilities->formatLocaleDate(null, $request->get_creation_date());

            case CommonRequest::PROPERTY_DECISION_DATE :
                if ($request->get_decision_date() != null)
                {
                    return $datetimeUtilities->formatLocaleDate(null, $request->get_decision_date());
                }
                else
                {
                    return $request->get_decision_date();
                }
        }

        return parent::renderCell($column, $resultPosition, $request);
    }
}
