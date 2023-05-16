<?php
namespace Chamilo\Core\Repository\Table\Link;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Publication\Service\PublicationAggregatorInterface;
use Chamilo\Core\Repository\Service\ContentObjectUrlGenerator;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumnFactory;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\Table\Link
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LinkAttachesTableRenderer extends LinkTableRenderer implements TableRowActionsSupport
{

    use LinkContentObjectTableRendererTrait;
    use LinkRowActionTableRendererTrait;

    protected ChamiloRequest $request;

    public function __construct(
        Translator $translator, UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        ContentObjectUrlGenerator $contentObjectUrlGenerator, StringUtilities $stringUtilities,
        PublicationAggregatorInterface $publicationAggregator, RightsService $rightsService, User $user,
        Workspace $workspace, ChamiloRequest $request,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory
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
        $this->request = $request;
    }

    /**
     * @todo Temporary solution until a more appropriate data-transfer-object has been implemented for attached objects
     */
    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $contentObject): string
    {
        return $this->renderLinkTableRowAction(
            $contentObject, self::TYPE_ATTACHES, $this->getRequest()->query->get(Manager::PARAM_CONTENT_OBJECT_ID),
            $this->renderIdentifierCell($contentObject)
        );
    }
}
