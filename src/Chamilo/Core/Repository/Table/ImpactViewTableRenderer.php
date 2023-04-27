<?php
namespace Chamilo\Core\Repository\Table;

use Chamilo\Core\Repository\Publication\Service\PublicationAggregator;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Table\ImpactView\ImpactViewTableColumnModel;
use Chamilo\Core\Repository\Viewer\Manager;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Translation\Translation;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\User\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ImpactViewTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport
{
    public const COLUMN_CATEGORY = 'category';
    public const COLUMN_SAFE_DELETE = 'safe_delete';

    protected PublicationAggregator $publicationAggregator;

    public function __construct(
        Translator $translator, UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        PublicationAggregator $publicationAggregator
    )
    {
        parent::__construct($translator, $urlGenerator, $htmlTableRenderer, $pager);

        $this->publicationAggregator = $publicationAggregator;
    }

    public function getPublicationAggregator(): PublicationAggregator
    {
        return $this->publicationAggregator;
    }

    protected function initializeColumns()
    {
        $translator = $this->getTranslator();

        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_TITLE)
        );

        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_TYPE)
        );

        $this->addColumn(
            new StaticTableColumn(
                self::COLUMN_CATEGORY,
                $translator->trans('Category', [], \Chamilo\Core\Repository\Manager::CONTEXT)
            )
        );

        $this->addColumn(
            new StaticTableColumn(
                self::COLUMN_SAFE_DELETE,
                $translator->trans('SafeDelete', [], Manager::CONTEXT)
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
        $translator = $this->getTranslator();

        switch ($column->get_name())
        {
            case ContentObject::PROPERTY_TYPE :
                return $contentObject->get_icon_image(IdentGlyph::SIZE_MINI);
            case ImpactViewTableColumnModel::COLUMN_CATEGORY :
                if ($contentObject->get_parent_id() != 0)
                {
                    $category = DataManager::retrieve_by_id(
                        RepositoryCategory::class, $contentObject->get_parent_id()
                    );

                    return $category->get_name();
                }
                else
                {
                    return $translator->trans('MyRepository', [], \Chamilo\Core\Repository\Manager::CONTEXT);
                }

            case ImpactViewTableColumnModel::COLUMN_SAFE_DELETE :
                return $this->render_is_linked_column($contentObject);
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

        return $toolbar->render();
    }

    private function render_is_linked_column($content_object): string
    {
        if (!DataManager::content_object_deletion_allowed($content_object))
        {
            $glyph = new FontAwesomeGlyph('exclamation-circle', ['text-warning'], null, 'fas');

            return $glyph->render() . ' ' .
                $this->getTranslator()->trans('PublicationsFound', [], \Chamilo\Core\Repository\Manager::CONTEXT);
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
