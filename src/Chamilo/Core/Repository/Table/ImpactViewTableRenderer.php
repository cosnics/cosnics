<?php
namespace Chamilo\Core\Repository\Table;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Publication\Service\PublicationAggregator;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumnFactory;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ImpactViewTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport
{
    public const COLUMN_CATEGORY = 'category';
    public const COLUMN_SAFE_DELETE = 'safe_delete';

    protected PublicationAggregator $publicationAggregator;

    public function __construct(
        Translator $translator, UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        PublicationAggregator $publicationAggregator,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory
    )
    {
        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory
        );

        $this->publicationAggregator = $publicationAggregator;
    }

    public function getContentObjectPreviewUrl(ContentObject $contentObject): string
    {
        return $this->getUrlGenerator()->fromRequest(
            [
                Application::PARAM_ACTION => Manager::ACTION_VIEW_CONTENT_OBJECTS,
                Manager::PARAM_CONTENT_OBJECT_ID => $contentObject->getId(),
                Manager::PARAM_CATEGORY_ID => $contentObject->get_parent_id()
            ]
        );
    }

    public function getPublicationAggregator(): PublicationAggregator
    {
        return $this->publicationAggregator;
    }

    protected function initializeColumns(): void
    {
        $translator = $this->getTranslator();

        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                ContentObject::class, ContentObject::PROPERTY_TITLE
            )
        );

        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                ContentObject::class, ContentObject::PROPERTY_TYPE
            )
        );

        $this->addColumn(
            new StaticTableColumn(
                self::COLUMN_CATEGORY, $translator->trans('Category', [], Manager::CONTEXT)
            )
        );

        $this->addColumn(
            new StaticTableColumn(
                self::COLUMN_SAFE_DELETE, $translator->trans('SafeDelete', [], Manager::CONTEXT)
            )
        );
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \ReflectionException
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $contentObject): string
    {
        $translator = $this->getTranslator();

        switch ($column->get_name())
        {
            case ContentObject::PROPERTY_TYPE :
                return $contentObject->get_icon_image(IdentGlyph::SIZE_MINI);
            case self::COLUMN_CATEGORY :
                if ($contentObject->get_parent_id() != 0)
                {
                    $category = DataManager::retrieve_by_id(
                        RepositoryCategory::class, $contentObject->get_parent_id()
                    );

                    return $category->get_name();
                }
                else
                {
                    return $translator->trans('MyRepository', [], Manager::CONTEXT);
                }

            case self::COLUMN_SAFE_DELETE :
                return $this->render_is_linked_column($contentObject);
        }

        return parent::renderCell($column, $resultPosition, $contentObject);
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $contentObject): string
    {
        $translator = $this->getTranslator();

        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Preview', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('desktop'),
                $this->getContentObjectPreviewUrl($contentObject), ToolbarItem::DISPLAY_ICON, false, null, null, null, [
                    'onclick' => 'javascript:openPopup(\'' .
                        addslashes($this->getContentObjectPreviewUrl($contentObject)) . '\');return false;'
                ]
            )
        );

        return $toolbar->render();
    }

    private function render_is_linked_column($content_object): string
    {
        if (!DataManager::content_object_deletion_allowed($content_object))
        {
            $glyph = new FontAwesomeGlyph('exclamation-circle', ['text-warning'], null, 'fas');

            return $glyph->render() . ' ' . $this->getTranslator()->trans('PublicationsFound', [], Manager::CONTEXT);
        }
        else
        {
            if ($this->getPublicationAggregator()->canContentObjectBeUnlinked($content_object))
            {
                $glyph = new FontAwesomeGlyph('check-circle', ['text-success'], null, 'fas');
            }
            else
            {

                $glyph = new FontAwesomeGlyph('minus-circle', ['text-danger'], null, 'fas');
            }

            return $glyph->render();
        }
    }
}
