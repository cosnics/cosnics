<?php
namespace Chamilo\Core\Repository\Table;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Service\ContentObjectUrlGenerator;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
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
class VersionTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport, TableActionsSupport
{
    public const DEFAULT_ORDER_COLUMN_DIRECTION = SORT_DESC;
    public const DEFAULT_ORDER_COLUMN_INDEX = 4;

    public const PROPERTY_TYPE = 'type';
    public const PROPERTY_USER = 'user';

    public const TABLE_IDENTIFIER = Manager::PARAM_CONTENT_OBJECT_ID;

    protected ContentObjectUrlGenerator $contentObjectUrlGenerator;

    protected DatetimeUtilities $datetimeUtilities;

    protected RightsService $rightsService;

    protected StringUtilities $stringUtilities;

    protected User $user;

    protected UserService $userService;

    public function __construct(
        User $user, RightsService $rightsService, DatetimeUtilities $datetimeUtilities,
        ContentObjectUrlGenerator $contentObjectUrlGenerator, StringUtilities $stringUtilities,
        UserService $userService, Translator $translator, UrlGenerator $urlGenerator,
        ListHtmlTableRenderer $htmlTableRenderer, Pager $pager
    )
    {
        $this->stringUtilities = $stringUtilities;
        $this->userService = $userService;
        $this->contentObjectUrlGenerator = $contentObjectUrlGenerator;
        $this->datetimeUtilities = $datetimeUtilities;
        $this->user = $user;
        $this->rightsService = $rightsService;

        parent::__construct($translator, $urlGenerator, $htmlTableRenderer, $pager);
    }

    public function getContentObjectUrlGenerator(): ContentObjectUrlGenerator
    {
        return $this->contentObjectUrlGenerator;
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
        $translator = $this->getTranslator();
        $urlGenerator = $this->getUrlGenerator();

        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        $actions->addAction(
            new TableAction(
                $urlGenerator->fromRequest(
                    [
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        Application::PARAM_ACTION => Manager::ACTION_COMPARE_CONTENT_OBJECTS,
                    ]
                ), $translator->trans('CompareSelected', [], Manager::CONTEXT), false
            )
        );

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

    protected function initializeColumns()
    {
        $translator = $this->getTranslator();

        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_TYPE)
        );

        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_TITLE)
        );

        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION)
        );

        $this->addColumn(
            new StaticTableColumn(
                self::PROPERTY_USER, $translator->trans('User', [], \Chamilo\Core\User\Manager::CONTEXT)
            )
        );

        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_MODIFICATION_DATE)
        );

        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_COMMENT)
        );

        $glyph = new FontAwesomeGlyph('folder', [], $translator->trans('Type', [], Manager::CONTEXT));
        $this->addColumn(
            new StaticTableColumn(self::PROPERTY_TYPE, $glyph->render())
        );
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @throws \Exception
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $contentObject): string
    {
        $translator = $this->getTranslator();
        $stringUtilities = $this->getStringUtilities();
        $datetimeUtilities = $this->getDatetimeUtilities();

        switch ($column->get_name())
        {
            case ContentObject::PROPERTY_TYPE :
            case self::PROPERTY_TYPE :
                return $contentObject->get_icon_image(
                    IdentGlyph::SIZE_MINI, true, ['fa-fw']
                );
            case ContentObject::PROPERTY_TITLE :
                $title = parent::renderCell($column, $resultPosition, $contentObject);
                $title_short = $stringUtilities->truncate($title, 50);

                $viewUrl = $this->getContentObjectUrlGenerator()->getViewUrl($contentObject);

                return '<a href="' . htmlentities($viewUrl) . '" title="' . htmlentities($title) . '">' . $title_short .
                    '</a>';
            case ContentObject::PROPERTY_DESCRIPTION :
                return $stringUtilities->truncate(
                    html_entity_decode($contentObject->get_description()), 50
                );
            case ContentObject::PROPERTY_OWNER_ID :
                return $this->getUserService()->getUserFullNameByIdentifier((string) $contentObject->get_owner_id());
            case ContentObject::PROPERTY_MODIFICATION_DATE :
                return $datetimeUtilities->formatLocaleDate(
                    $translator->trans('DateTimeFormatLong', [], StringUtilities::LIBRARIES),
                    $contentObject->get_modification_date()
                );
        }

        return parent::renderCell($column, $resultPosition, $contentObject);
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $contentObject): string
    {
        $rightsService = $this->getRightsService();
        $translator = $this->getTranslator();
        $contentObjectUrlGenerator = $this->getContentObjectUrlGenerator();
        $toolbar = new Toolbar();

        if ($rightsService->canDestroyContentObject($this->getUser(), $contentObject))
        {
            $deleteUrl = $contentObjectUrlGenerator->getDeleteUrl($contentObject, 'version');
            if ($deleteUrl)
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                        $deleteUrl, ToolbarItem::DISPLAY_ICON
                    )
                );
            }
            else
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        $translator->trans('DeleteNotAvailable', [], StringUtilities::LIBRARIES),
                        new FontAwesomeGlyph('times', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                    )
                );
            }
        }

        if ($rightsService->canEditContentObject($this->getUser(), $contentObject))
        {
            $revertUrl = $contentObjectUrlGenerator->getRevertUrl($contentObject);

            if ($revertUrl)
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        $translator->trans('Revert', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('undo'),
                        $revertUrl, ToolbarItem::DISPLAY_ICON
                    )
                );
            }
            else
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        $translator->trans('RevertNotAvailable', [], StringUtilities::LIBRARIES),
                        new FontAwesomeGlyph('undo', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                    )
                );
            }
        }

        return $toolbar->render();
    }
}
