<?php
namespace Chamilo\Core\Repository\Viewer\Table;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Viewer\Manager;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupportInterface;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumnFactory;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
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
class ContentObjectTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport
{
    public const DEFAULT_ORDER_COLUMN_DIRECTION = SORT_DESC;
    public const DEFAULT_ORDER_COLUMN_INDEX = 3;

    public const TABLE_IDENTIFIER = Manager::PARAM_ID;

    protected DatetimeUtilities $datetimeUtilities;

    protected RightsService $rightsService;

    protected StringUtilities $stringUtilities;

    protected User $user;

    public function __construct(
        Translator $translator, UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        StringUtilities $stringUtilities, DatetimeUtilities $datetimeUtilities, RightsService $rightsService,
        User $user, DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory
    )
    {
        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory
        );

        $this->stringUtilities = $stringUtilities;
        $this->datetimeUtilities = $datetimeUtilities;
        $this->rightsService = $rightsService;
        $this->user = $user;
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

    public function getUser(): User
    {
        return $this->user;
    }

    protected function initializeColumns(): void
    {
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                ContentObject::class, ContentObject::PROPERTY_TYPE
            )
        );
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
                ContentObject::class, ContentObject::PROPERTY_MODIFICATION_DATE
            )
        );
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $contentObject): string
    {
        $stringUtilities = $this->getStringUtilities();

        switch ($column->get_name())
        {
            case ContentObject::PROPERTY_TYPE :
                return $contentObject->get_icon_image(IdentGlyph::SIZE_MINI);
            case ContentObject::PROPERTY_TITLE :
                return $stringUtilities->truncate($contentObject->get_title(), 50);
            case ContentObject::PROPERTY_DESCRIPTION :
                return $stringUtilities->truncate($contentObject->get_description(), 50);
            case ContentObject::PROPERTY_MODIFICATION_DATE :
                return $this->getDatetimeUtilities()->formatLocaleDate(null, $contentObject->get_modification_date());
        }

        return parent::renderCell($column, $resultPosition, $contentObject);
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $contentObject): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);
        $rightsService = $this->getRightsService();

        if ($rightsService->canUseContentObject($this->getUser(), $contentObject))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Publish', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('share-square'),
                    $urlGenerator->fromRequest([Manager::PARAM_ID => $contentObject->getId()]),
                    ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if ($rightsService->canViewContentObject($this->getUser(), $contentObject))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Preview', [], Manager::CONTEXT), new FontAwesomeGlyph('desktop'),
                    $urlGenerator->fromRequest(
                        [
                            Manager::PARAM_TAB => Manager::TAB_VIEWER,
                            Manager::PARAM_ACTION => Manager::ACTION_VIEWER,
                            Manager::PARAM_VIEW_ID => $contentObject->getId()
                        ]
                    ), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if ($rightsService->canEditContentObject($this->getUser(), $contentObject) &&
            $rightsService->canUseContentObject($this->getUser(), $contentObject))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('EditAndPublish', [], Manager::CONTEXT), new FontAwesomeGlyph('edit'),
                    $urlGenerator->fromRequest(
                        [
                            Manager::PARAM_TAB => Manager::TAB_CREATOR,
                            Manager::PARAM_ACTION => Manager::ACTION_CREATOR,
                            Manager::PARAM_EDIT_ID => $contentObject->getId()
                        ]
                    ), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if ($contentObject instanceof ComplexContentObjectSupportInterface &&
            $rightsService->canViewContentObject($this->getUser(), $contentObject))
        {

            $preview_url = \Chamilo\Core\Repository\Manager::get_preview_content_object_url($contentObject);
            $onclick = '" onclick="javascript:openPopup(\'' . addslashes($preview_url) . '\'); return false;';
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Preview', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('desktop'),
                    $preview_url, ToolbarItem::DISPLAY_ICON, false, $onclick, '_blank'
                )
            );
        }

        return $toolbar->render();
    }
}
