<?php
namespace Chamilo\Core\Repository\Table\Link;

use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
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
class LinkPublicationsTableRenderer extends LinkTableRenderer implements TableRowActionsSupport
{

    protected function initializeColumns()
    {
        $this->addColumn(
            new DataClassPropertyTableColumn(Attributes::class, Attributes::PROPERTY_APPLICATION)
        );
        $this->addColumn(
            new DataClassPropertyTableColumn(Attributes::class, Attributes::PROPERTY_LOCATION)
        );
        $this->addColumn(new DataClassPropertyTableColumn(Attributes::class, Attributes::PROPERTY_DATE));
    }

    /**
     * @param \Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes $attributes
     *
     * @throws \Exception
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $attributes): string
    {
        $translator = $this->getTranslator();

        switch ($column->get_name())
        {
            case Attributes::PROPERTY_APPLICATION :
                return $translator->trans('TypeName', [], $attributes->get_application());
            case Attributes::PROPERTY_LOCATION :
                return $attributes->get_location();
            case Attributes::PROPERTY_DATE :
                return date('Y-m-d, H:i', $attributes->get_date());
        }

        return parent::renderCell($column, $resultPosition, $attributes);
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
            $linkIdentifier = $attributes->get_application() . '|' . $this->renderIdentifierCell($attributes) . '|' .
                $attributes->getPublicationContext();

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
