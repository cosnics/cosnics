<?php
namespace Chamilo\Core\Repository\Table\Link;

use Chamilo\Core\Repository\Publication\Service\PublicationAggregatorInterface;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumnFactory;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\Table\Link
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LinkPublicationsTableRenderer extends LinkTableRenderer implements TableRowActionsSupport
{
    use LinkRowActionTableRendererTrait;

    public function __construct(
        Translator $translator, UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        PublicationAggregatorInterface $publicationAggregator, RightsService $rightsService, User $user,
        Workspace $workspace, DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory
    )
    {
        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory
        );

        $this->publicationAggregator = $publicationAggregator;
        $this->rightsService = $rightsService;
        $this->user = $user;
        $this->workspace = $workspace;
    }

    protected function initializeColumns()
    {
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
        $contentObject = DataManager::retrieve_by_id(ContentObject::class, $attributes->get_content_object_id());

        $linkIdentifier = $attributes->get_application() . '|' . $this->renderIdentifierCell($attributes) . '|' .
            $attributes->getPublicationContext();

        return $this->renderLinkTableRowAction(
            $contentObject, self::TYPE_PUBLICATIONS, (string) $attributes->get_content_object_id(), $linkIdentifier
        );
    }
}
