<?php
namespace Chamilo\Core\Repository\ContentObject\Glossary\Display\Component\Renderer\Type;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Glossary\Display\GlossaryDisplayContextProviderInterface;
use Chamilo\Core\Repository\ContentObject\Glossary\Display\Manager;
use Chamilo\Core\Repository\ContentObject\GlossaryItem\Storage\DataClass\GlossaryItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\ContentObject\Glossary\Display\Component\Renderer\Type
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class GlossayViewerTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport
{
    public const TABLE_IDENTIFIER = Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID;

    protected GlossaryDisplayContextProviderInterface $glossaryDisplayContextProvider;

    public function __construct(
        GlossaryDisplayContextProviderInterface $glossaryDisplayContextProvider, Translator $translator,
        UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer, Pager $pager
    )
    {
        $this->glossaryDisplayContextProvider = $glossaryDisplayContextProvider;

        parent::__construct($translator, $urlGenerator, $htmlTableRenderer, $pager);
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

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Glossary\Storage\DataClass\ComplexGlossary $complexGlossary
     *
     * @throws \ReflectionException
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $complexGlossary): string
    {
        $glossaryItem = DataManager::retrieve_by_id(
            GlossaryItem::class, $complexGlossary->get_ref()
        );

        switch ($column->get_name())
        {
            case ContentObject::PROPERTY_TITLE :
                return $glossaryItem->get_title();
            case ContentObject::PROPERTY_DESCRIPTION :
                return ContentObjectRenditionImplementation::launch(
                    $glossaryItem, ContentObjectRendition::FORMAT_HTML, ContentObjectRendition::VIEW_DESCRIPTION
                );
        }

        return parent::renderCell($column, $resultPosition, $complexGlossary);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Glossary\Storage\DataClass\ComplexGlossary $complexGlossary
     *
     * @throws \ReflectionException
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $complexGlossary): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        if ($component->is_allowed_to_edit_content_object())
        {
            $updateUrl = $urlGenerator->fromRequest([
                \Chamilo\Core\Repository\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Display\Manager::ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM,
                \Chamilo\Core\Repository\Display\Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complexGlossary->getId(
                ),
                \Chamilo\Core\Repository\Display\Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $component->get_complex_content_object_item_id(
                )
            ]);

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                    $updateUrl, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if ($component->is_allowed_to_delete_child())
        {
            $deleteUrl = $urlGenerator->fromRequest([
                \Chamilo\Core\Repository\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Display\Manager::ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM,
                \Chamilo\Core\Repository\Display\Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complexGlossary->getId(
                ),
                \Chamilo\Core\Repository\Display\Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $component->get_complex_content_object_item_id(
                )
            ]);

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                    $deleteUrl, ToolbarItem::DISPLAY_ICON, true
                )
            );
        }

        return $toolbar->render();
    }
}
