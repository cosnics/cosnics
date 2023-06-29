<?php
namespace Chamilo\Core\Repository\Table;

use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Service\ContentObjectActionRenderer;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumnFactory;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
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
 * @package Chamilo\Core\Repository\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ContentObjectTableRenderer extends DataClassListTableRenderer
    implements TableRowActionsSupport, TableActionsSupport
{
    public const DEFAULT_ORDER_COLUMN_DIRECTION = SORT_DESC;
    public const DEFAULT_ORDER_COLUMN_INDEX = 3;

    public const PROPERTY_TYPE = 'type';
    public const PROPERTY_VERSION = 'version';

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
        ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory
    )
    {
        $this->stringUtilities = $stringUtilities;
        $this->userService = $userService;
        $this->contentObjectActionRenderer = $contentObjectActionRenderer;
        $this->datetimeUtilities = $datetimeUtilities;
        $this->user = $user;
        $this->workspace = $workspace;
        $this->rightsService = $rightsService;

        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory
        );
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

    protected function initializeColumns(): void
    {
        $translator = $this->getTranslator();
        $typeGlyph = new FontAwesomeGlyph('folder', [], $translator->trans('Type', [], Manager::CONTEXT));

        $this->addColumn(new StaticTableColumn(self::PROPERTY_TYPE, $typeGlyph->render()));

        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                ContentObject::class, ContentObject::PROPERTY_TITLE
            )
        );

        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                ContentObject::class, ContentObject::PROPERTY_DESCRIPTION
            )
        );

        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                ContentObject::class, ContentObject::PROPERTY_OWNER_ID
            )
        );

        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                ContentObject::class, ContentObject::PROPERTY_MODIFICATION_DATE
            )
        );

        $versionGlyph = new FontAwesomeGlyph('undo', [], $translator->trans('Versions', [], Manager::CONTEXT));

        $this->addColumn(new StaticTableColumn(self::PROPERTY_VERSION, $versionGlyph->render()));
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @throws \Exception
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $contentObject): string
    {
        $translator = $this->getTranslator();
        $urlGenerator = $this->getUrlGenerator();
        $stringUtilities = $this->getStringUtilities();
        $datetimeUtilities = $this->getDatetimeUtilities();

        switch ($column->get_name())
        {
            case self::PROPERTY_TYPE :
                $image = $contentObject->get_icon_image(
                    IdentGlyph::SIZE_MINI, true, ['fa-fw']
                );

                $typeUrl = $urlGenerator->fromParameters([
                    Application::PARAM_CONTEXT => Manager::CONTEXT,
                    Application::PARAM_ACTION => Manager::ACTION_BROWSE_CONTENT_OBJECTS,
                    FilterData::FILTER_TYPE => $contentObject->get_template_registration_id()
                ]);

                return '<a href="' . htmlentities($typeUrl) . '" title="' .
                    htmlentities($contentObject->get_type_string()) . '">' . $image . '</a>';
            case ContentObject::PROPERTY_TITLE :
                $title = parent::renderCell($column, $resultPosition, $contentObject);
                $title_short = $stringUtilities->truncate($title, 50);

                $viewUrl = $urlGenerator->fromParameters(
                    [
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        Application::PARAM_ACTION => Manager::ACTION_VIEW_CONTENT_OBJECTS,
                        Manager::PARAM_CONTENT_OBJECT_ID => $contentObject->getId(),
                        FilterData::FILTER_CATEGORY => $contentObject->get_parent_id()
                    ]
                );

                return '<a href="' . htmlentities($viewUrl) . '" title="' . htmlentities($title) . '">' . $title_short .
                    '</a>';
            case ContentObject::PROPERTY_DESCRIPTION :
                return $stringUtilities->truncate(
                    html_entity_decode($contentObject->get_description()), 50
                );
            case ContentObject::PROPERTY_OWNER_ID :
                return $this->getUserService()->getUserFullNameByIdentifier((string) $contentObject->get_owner_id());
            case ContentObject::PROPERTY_CREATION_DATE :
                return $datetimeUtilities->formatLocaleDate(
                    $translator->trans('DateTimeFormatLong', [], StringUtilities::LIBRARIES),
                    $contentObject->get_creation_date()
                );
            case ContentObject::PROPERTY_MODIFICATION_DATE :
                return $datetimeUtilities->formatLocaleDate(
                    $translator->trans('DateTimeFormatLong', [], StringUtilities::LIBRARIES),
                    $contentObject->get_modification_date()
                );
            case self::PROPERTY_VERSION :
                if ($contentObject instanceof Versionable)
                {
                    if ($contentObject->has_versions())
                    {
                        $number = $contentObject->get_version_count();
                        $title = $translator->trans('VersionsAvailable', ['NUMBER' => $number], Manager::CONTEXT);
                        $glyph = new FontAwesomeGlyph(
                            'check', ['text-primary'], $title, 'fas'
                        );
                    }
                    else
                    {
                        $title = $translator->trans('NoVersionsAvailable', [], Manager::CONTEXT);
                        $glyph = new FontAwesomeGlyph(
                            'check', ['text-muted'], $title, 'fas'
                        );
                    }
                }
                else
                {
                    $title = $translator->trans('NotVersionable', [], Manager::CONTEXT);
                    $glyph = new FontAwesomeGlyph(
                        'check', ['text-muted'], $title, 'fas'
                    );
                }

                return $glyph->render();
        }

        return parent::renderCell($column, $resultPosition, $contentObject);
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $contentObject): string
    {
        return $this->getContentObjectActionRenderer()->renderActions($contentObject);
    }
}
