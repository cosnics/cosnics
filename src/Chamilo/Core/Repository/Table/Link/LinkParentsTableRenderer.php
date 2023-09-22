<?php
namespace Chamilo\Core\Repository\Table\Link;

use Chamilo\Core\Repository\Publication\Service\PublicationAggregatorInterface;
use Chamilo\Core\Repository\Service\ContentObjectUrlGenerator;
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
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\Table\Link
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LinkParentsTableRenderer extends LinkTableRenderer implements TableRowActionsSupport
{
    use LinkContentObjectTableRendererTrait
    {
        renderCell as renderContentObjectCell;
    }
    use LinkRowActionTableRendererTrait;

    public function __construct(
        Translator $translator, UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        ContentObjectUrlGenerator $contentObjectUrlGenerator, StringUtilities $stringUtilities,
        PublicationAggregatorInterface $publicationAggregator, RightsService $rightsService, User $user,
        Workspace $workspace, DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory
    )
    {
        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory
        );

        $this->contentObjectUrlGenerator = $contentObjectUrlGenerator;
        $this->stringUtilities = $stringUtilities;
        $this->publicationAggregator = $publicationAggregator;
        $this->rightsService = $rightsService;
        $this->user = $user;
        $this->workspace = $workspace;
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem $complexContentObjectItem
     *
     * @throws \Exception
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $complexContentObjectItem
    ): string
    {
        $contentObject = DataManager::retrieve_by_id(
            ContentObject::class, $complexContentObjectItem->get_parent()
        );

        return $this->renderContentObjectCell($column, $resultPosition, $contentObject);
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem $complexContentObjectItem
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $complexContentObjectItem): string
    {
        $contentObject = DataManager::retrieve_by_id(ContentObject::class, $complexContentObjectItem->get_parent());

        return $this->renderLinkTableRowAction(
            $contentObject, self::TYPE_PARENTS, $complexContentObjectItem->get_ref(),
            $this->renderIdentifierCell($complexContentObjectItem)
        );
    }
}
