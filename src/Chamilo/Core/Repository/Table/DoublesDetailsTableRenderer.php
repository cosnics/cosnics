<?php
namespace Chamilo\Core\Repository\Table;

use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DoublesDetailsTableRenderer extends DataClassListTableRenderer
{
    public const PROPERTY_TYPE = 'type';

    protected StringUtilities $stringUtilities;

    public function __construct(
        StringUtilities $stringUtilities, Translator $translator, UrlGenerator $urlGenerator,
        ListHtmlTableRenderer $htmlTableRenderer, Pager $pager
    )
    {
        $this->stringUtilities = $stringUtilities;

        parent::__construct($translator, $urlGenerator, $htmlTableRenderer, $pager);
    }

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    protected function initializeColumns()
    {
        $translator = $this->getTranslator();
        $typeGlyph = new FontAwesomeGlyph('folder', [], $translator->trans('Type', [], Manager::CONTEXT));

        $this->addColumn(new StaticTableColumn(self::PROPERTY_TYPE, $typeGlyph->render()));

        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_TITLE)
        );

        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION)
        );

        $duplicatesGlyph = new FontAwesomeGlyph('clone', [], $translator->trans('Duplicates', [], Manager::CONTEXT));
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @throws \Exception
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $contentObject): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $stringUtilities = $this->getStringUtilities();

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
        }

        return parent::renderCell($column, $resultPosition, $contentObject);
    }
}
