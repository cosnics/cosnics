<?php
namespace Chamilo\Core\Repository\Table;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Service\ContentObjectActionRenderer;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassGalleryTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\GalleryHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ContentObjectGalleryTableRenderer extends DataClassGalleryTableRenderer
    implements TableActionsSupport, TableRowActionsSupport
{

    public const TABLE_IDENTIFIER = Manager::PARAM_CONTENT_OBJECT_ID;

    protected ContentObjectActionRenderer $contentObjectActionRenderer;

    protected DatetimeUtilities $datetimeUtilities;

    protected RightsService $rightsService;

    protected StringUtilities $stringUtilities;

    protected User $user;

    protected UserService $userService;

    protected Workspace $workspace;

    public function __construct(
        Workspace $workspace, User $user, RightsService $rightsService, DatetimeUtilities $datetimeUtilities,
        ContentObjectActionRenderer $contentObjectActionRenderer, StringUtilities $stringUtilities,
        UserService $userService, Translator $translator, UrlGenerator $urlGenerator,
        GalleryHtmlTableRenderer $htmlTableRenderer, Pager $pager
    )
    {
        $this->stringUtilities = $stringUtilities;
        $this->userService = $userService;
        $this->contentObjectActionRenderer = $contentObjectActionRenderer;
        $this->datetimeUtilities = $datetimeUtilities;
        $this->user = $user;
        $this->workspace = $workspace;
        $this->rightsService = $rightsService;

        parent::__construct($translator, $urlGenerator, $htmlTableRenderer, $pager);
    }

    public function getContentObjectActionRenderer(): ContentObjectActionRenderer
    {
        return $this->contentObjectActionRenderer;
    }

    public function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->datetimeUtilities;
    }

    public function getRightsService(): RightsService
    {
        return $this->rightsService;
    }

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    public function getTableActions(): TableActions
    {
        $rightsService = $this->getRightsService();
        $translator = $this->getTranslator();
        $urlGenerator = $this->getUrlGenerator();

        $user = $this->getUser();
        $workspace = $this->getWorkspace();

        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        if ($rightsService->isWorkspaceCreator($user, $workspace))
        {
            $recycleUrl = $urlGenerator->fromParameters(
                [
                    Application::PARAM_CONTEXT => Manager::CONTEXT,
                    Application::PARAM_ACTION => Manager::ACTION_IMPACT_VIEW_RECYCLE
                ]
            );

            $actions->addAction(
                new TableAction(
                    $recycleUrl, $translator->trans('RemoveSelected', [], StringUtilities::LIBRARIES), false
                )
            );

            $unlinkUrl = $urlGenerator->fromParameters(
                [
                    Application::PARAM_CONTEXT => Manager::CONTEXT,
                    Application::PARAM_ACTION => Manager::ACTION_UNLINK_CONTENT_OBJECTS
                ]
            );

            $actions->addAction(
                new TableAction(
                    $unlinkUrl, $translator->trans('UnlinkSelected', [], StringUtilities::LIBRARIES)
                )
            );
        }

        $moveUrl = $urlGenerator->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_MOVE_CONTENT_OBJECTS
            ]
        );

        $actions->addAction(
            new TableAction(
                $moveUrl, $translator->trans('MoveSelected', [], StringUtilities::LIBRARIES), false
            )
        );

        $publishUrl = $urlGenerator->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_PUBLICATION,
                \Chamilo\Core\Repository\Publication\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Publication\Manager::ACTION_PUBLISH
            ]
        );

        $actions->addAction(
            new TableAction(
                $publishUrl, $translator->trans('PublishSelected', [], StringUtilities::LIBRARIES), false
            )
        );

        $exportUrl = $urlGenerator->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_EXPORT_CONTENT_OBJECTS
            ]
        );

        $actions->addAction(
            new TableAction(
                $exportUrl, $translator->trans('ExportSelected', [], StringUtilities::LIBRARIES), false
            )
        );

        if ($rightsService->isWorkspaceCreator($user, $workspace))
        {
            $shareUrl = $urlGenerator->fromParameters(
                [
                    Application::PARAM_CONTEXT => \Chamilo\Core\Repository\Workspace\Manager::CONTEXT,
                    \Chamilo\Core\Repository\Workspace\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Workspace\Manager::ACTION_SHARE
                ]
            );

            $actions->addAction(
                new TableAction(
                    $shareUrl, $translator->trans('ShareSelected', [], Manager::CONTEXT), false
                )
            );
        }
        else
        {
            $canDelete = $rightsService->canDeleteContentObjects(
                $user, $workspace
            );

            if ($canDelete)
            {
                $unshareUrl = $urlGenerator->fromParameters(
                    [
                        Application::PARAM_CONTEXT => \Chamilo\Core\Repository\Workspace\Manager::CONTEXT,
                        \Chamilo\Core\Repository\Workspace\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Workspace\Manager::ACTION_UNSHARE
                    ]
                );

                $actions->addAction(
                    new TableAction(
                        $unshareUrl, $translator->trans('UnshareSelected', [], Manager::CONTEXT), false
                    )
                );
            }
        }

        return $actions;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }

    public function getWorkspace(): Workspace
    {
        return $this->workspace;
    }

    protected function initializeColumns()
    {
        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_TITLE)
        );

        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION)
        );
    }

    public function renderContent($result): string
    {
        return 'aa';
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $contentObject): string
    {
        return $this->getContentObjectActionRenderer()->renderActions($contentObject);
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     */
    public function renderTitle($contentObject): string
    {
        return $contentObject->get_title();
    }
}
