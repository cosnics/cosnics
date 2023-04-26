<?php
namespace Chamilo\Core\Repository\Table\Link;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Repository\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LinkParentsTableRenderer extends LinkTableRenderer implements TableRowActionsSupport
{

    protected function initializeColumns()
    {
        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_TYPE, null, false)
        );
        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_TITLE, null, false)
        );
        $this->addColumn(
            new DataClassPropertyTableColumn(
                ContentObject::class, ContentObject::PROPERTY_DESCRIPTION, null, false
            )
        );
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem $complexContentObjectItem
     *
     * @throws \Exception
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $complexContentObjectItem
    ): string
    {
        $stringUtilities = $this->getStringUtilities();
        $contentObject = DataManager::retrieve_by_id(
            ContentObject::class, $complexContentObjectItem->get_parent()
        );

        switch ($column->get_name())
        {
            case ContentObject::PROPERTY_DESCRIPTION :
                return $stringUtilities->truncate($contentObject->get_description(), 50);
            case ContentObject::PROPERTY_TITLE :
                $viewUrl = $this->getContentObjectUrlGenerator()->getViewUrl($contentObject);

                return '<a href="' . $viewUrl . '">' . $stringUtilities->truncate($contentObject->get_title(), 50) .
                    '</a>';
            case ContentObject::PROPERTY_TYPE :
                return $contentObject->get_icon_image();
        }

        return parent::renderCell($column, $resultPosition, $complexContentObjectItem);
    }

    /**
     * @param \Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes $attributes
     *
     * @throws \ReflectionException
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $attributes): string
    {
        $translator = $this->getTranslator();
        $toolbar = new Toolbar();

        $contentObject = DataManager::retrieve_by_id(ContentObject::class, $attributes->get_content_object_id());

        if ($this->isAllowedToModify($contentObject))
        {
            $linkIdentifier = $this->renderIdentifierCell($contentObject);

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                    $this->getDeleteLinkUrl(
                        self::TYPE_PUBLICATIONS, (string) $attributes->get_content_object_id(), $linkIdentifier
                    ), ToolbarItem::DISPLAY_ICON, true
                )
            );
        }

        return $toolbar->render();
    }
}
