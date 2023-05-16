<?php
namespace Chamilo\Core\Repository\Quota\Table;

use Chamilo\Core\Repository\Quota\Manager;
use Chamilo\Core\Repository\Quota\Rights\Service\RightsService;
use Chamilo\Core\Repository\Quota\Storage\DataClass\Request;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumnFactory;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\User\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RequestTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport, TableActionsSupport
{
    public const TABLE_IDENTIFIER = Manager::PARAM_REQUEST_ID;

    public const TYPE_DENIED = 4;
    public const TYPE_GRANTED = 3;
    public const TYPE_PENDING = 2;
    public const TYPE_PERSONAL = 1;

    protected DatetimeUtilities $datetimeUtilities;

    protected RightsService $rightsService;

    protected User $user;

    public function __construct(
        Translator $translator, UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        RightsService $rightsService, DatetimeUtilities $datetimeUtilities, User $user,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory
    )
    {
        $this->rightsService = $rightsService;
        $this->datetimeUtilities = $datetimeUtilities;
        $this->user = $user;

        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory
        );
    }

    public function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->datetimeUtilities;
    }

    public function getRightsService(): RightsService
    {
        return $this->rightsService;
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

    protected function initializeColumns()
    {
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(Request::class, Request::PROPERTY_CREATION_DATE)
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(Request::class, Request::PROPERTY_QUOTA)
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(Request::class, Request::PROPERTY_MOTIVATION)
        );
    }

    /**
     * @param \Chamilo\Core\Repository\Quota\Storage\DataClass\Request $request
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $request): string
    {
        $datetimeUtilities = $this->getDatetimeUtilities();

        switch ($column->get_name())
        {
            case Request::PROPERTY_QUOTA :
                return Filesystem::format_file_size($request->get_quota());
            case Request::PROPERTY_CREATION_DATE :
                return $datetimeUtilities->formatLocaleDate(null, $request->get_creation_date());
        }

        return parent::renderCell($column, $resultPosition, $request);
    }

    /**
     * @param \Chamilo\Core\Repository\Quota\Storage\DataClass\Request $request
     *
     * @throws \Exception
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $request): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();
        $rightsService = $this->getRightsService();

        $toolbar = new Toolbar();

        if ($rightsService->canUserViewQuotaRequests($this->getUser()))
        {
            if (!$request->was_granted() && $rightsService->isUserIdentifierTargetForUser(
                    $request->get_user_id(), $this->getUser()
                ))
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        $translator->trans('Grant', [], Manager::CONTEXT),
                        new FontAwesomeGlyph('play', ['text-success'], null, 'fas'), $urlGenerator->fromParameters(
                        [
                            Application::PARAM_CONTEXT => Manager::CONTEXT,
                            Manager::PARAM_ACTION => Manager::ACTION_GRANT,
                            Manager::PARAM_REQUEST_ID => $request->getId()
                        ]
                    ), ToolbarItem::DISPLAY_ICON
                    )
                );
            }

            if (!$request->is_pending() && $rightsService->isUserIdentifierTargetForUser(
                    $request->get_user_id(), $this->getUser()
                ))
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        $translator->trans('Deny', [], Manager::CONTEXT),
                        new FontAwesomeGlyph('stop', ['text-danger'], null, 'fas'), $urlGenerator->fromParameters(
                        [
                            Application::PARAM_CONTEXT => Manager::CONTEXT,
                            Manager::PARAM_ACTION => Manager::ACTION_DENY,
                            Manager::PARAM_REQUEST_ID => $request->getId()
                        ]
                    ), ToolbarItem::DISPLAY_ICON
                    )
                );
            }
        }

        if ($this->getUser()->is_platform_admin() ||
            ($this->getUser()->getId() == $request->get_user_id() && $request->is_pending()))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                    $urlGenerator->fromParameters(
                        [
                            Application::PARAM_CONTEXT => Manager::CONTEXT,
                            Manager::PARAM_ACTION => Manager::ACTION_DELETE,
                            Manager::PARAM_REQUEST_ID => $request->getId()
                        ]
                    ), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        return $toolbar->render();
    }
}
