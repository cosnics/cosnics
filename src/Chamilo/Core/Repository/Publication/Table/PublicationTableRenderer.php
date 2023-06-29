<?php
namespace Chamilo\Core\Repository\Publication\Table;

use Chamilo\Core\Repository\Publication\Manager;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
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
 * @package Chamilo\Core\Repository\Publication\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport
{
    public const DEFAULT_NUMBER_OF_ROWS_PER_PAGE = 500;

    public const TABLE_IDENTIFIER = Manager::PARAM_PUBLICATION_ID;

    protected DatetimeUtilities $datetimeUtilities;

    protected StringUtilities $stringUtilities;

    public function __construct(
        StringUtilities $stringUtilities, DatetimeUtilities $datetimeUtilities, Translator $translator,
        UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory
    )
    {
        $this->datetimeUtilities = $datetimeUtilities;
        $this->stringUtilities = $stringUtilities;

        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory
        );
    }

    public function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->datetimeUtilities;
    }

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    protected function initializeColumns(): void
    {
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(Attributes::class, Attributes::PROPERTY_TITLE)
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                Attributes::class, Attributes::PROPERTY_APPLICATION
            )
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(Attributes::class, Attributes::PROPERTY_LOCATION)
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(Attributes::class, Attributes::PROPERTY_DATE)
        );
    }

    /**
     * @param \Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes $attributes
     *
     * @throws \Exception
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $attributes): string
    {
        $translator = $this->getTranslator();
        $stringUtilities = $this->getStringUtilities();
        $datetimeUtilities = $this->getDatetimeUtilities();

        switch ($column->get_name())
        {
            case Attributes::PROPERTY_DATE :
                return $datetimeUtilities->formatLocaleDate(
                    $translator->trans('DateFormatShort', [], StringUtilities::LIBRARIES) . ', ' .
                    $translator->trans('TimeNoSecFormat', [], StringUtilities::LIBRARIES), $attributes->get_date()
                );
            case Attributes::PROPERTY_APPLICATION :
                return $translator->trans('TypeName', [], $attributes->get_application());
            case Attributes::PROPERTY_TITLE :
                $url = $attributes->get_url();

                return '<a href="' . $url . '"><span title="' . htmlentities($attributes->get_title()) . '">' .
                    $stringUtilities->truncate($attributes->get_title(), 50) . '</span></a>';
        }

        return parent::renderCell($column, $resultPosition, $attributes);
    }

    /**
     * @param \Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes $attributes
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $attributes): string
    {
        $translator = $this->getTranslator();
        $urlGenerator = $this->getUrlGenerator();
        $toolbar = new Toolbar();

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                $urlGenerator->fromParameters(
                    [
                        Manager::PARAM_ACTION => Manager::ACTION_DELETE,
                        Manager::PARAM_PUBLICATION_ID => $attributes->getId(),
                        Manager::PARAM_PUBLICATION_APPLICATION => $attributes->get_application(),
                        Manager::PARAM_PUBLICATION_CONTEXT => $attributes->getPublicationContext()
                    ]
                ), ToolbarItem::DISPLAY_ICON, true
            )
        );

        if (!$attributes->get_content_object()->is_current())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Update', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('undo'),
                    $urlGenerator->fromParameters(
                        [
                            Manager::PARAM_ACTION => Manager::ACTION_UPDATE,
                            Manager::PARAM_PUBLICATION_APPLICATION => $attributes->get_application(),
                            Manager::PARAM_PUBLICATION_ID => $attributes->getId()
                        ]
                    ), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        return $toolbar->render();
    }
}
