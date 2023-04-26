<?php
namespace Chamilo\Core\Repository\Table\Link;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Publication\Service\PublicationAggregatorInterface;
use Chamilo\Core\Repository\Service\ContentObjectUrlGenerator;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class LinkTableRenderer extends DataClassListTableRenderer
{
    public const TYPE_ATTACHED_TO = 4;
    public const TYPE_ATTACHES = 5;
    public const TYPE_CHILDREN = 3;
    public const TYPE_INCLUDED_IN = 6;
    public const TYPE_INCLUDES = 7;
    public const TYPE_PARENTS = 2;
    public const TYPE_PUBLICATIONS = 1;

    protected ContentObjectUrlGenerator $contentObjectUrlGenerator;

    protected PublicationAggregatorInterface $publicationAggregator;

    protected RightsService $rightsService;

    protected StringUtilities $stringUtilities;

    protected User $user;

    protected Workspace $workspace;

    public function __construct(
        User $user, RightsService $rightsService, Workspace $workspace, Translator $translator,
        UrlGenerator $urlGenerator, ContentObjectUrlGenerator $contentObjectUrlGenerator,
        ListHtmlTableRenderer $htmlTableRenderer, Pager $pager, StringUtilities $stringUtilities
    )
    {
        $this->user = $user;
        $this->rightsService = $rightsService;
        $this->workspace = $workspace;
        $this->contentObjectUrlGenerator = $contentObjectUrlGenerator;
        $this->stringUtilities = $stringUtilities;

        parent::__construct($translator, $urlGenerator, $htmlTableRenderer, $pager);
    }

    public function getContentObjectUrlGenerator(): ContentObjectUrlGenerator
    {
        return $this->contentObjectUrlGenerator;
    }

    protected function getDeleteLinkUrl(int $type, string $objectIdentifier, string $linkIdentifier): string
    {
        $parameters = [];

        $parameters[Application::PARAM_ACTION] = Manager::ACTION_DELETE_LINK;
        $parameters[Manager::PARAM_LINK_TYPE] = $type;
        $parameters[Manager::PARAM_CONTENT_OBJECT_ID] = $objectIdentifier;
        $parameters[Manager::PARAM_LINK_ID] = $linkIdentifier;

        return $this->getUrlGenerator()->fromParameters($parameters);
    }

    public function getPublicationAggregator(): PublicationAggregatorInterface
    {
        return $this->publicationAggregator;
    }

    public function getRightsService(): RightsService
    {
        return $this->rightsService;
    }

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getWorkspace(): Workspace
    {
        return $this->workspace;
    }

    protected function getWorkspaceRightsService(): RightsService
    {
        return $this->rightsService;
    }

    public function isAllowedToModify(ContentObject $contentObject): bool
    {
        return $this->getWorkspaceRightsService()->canEditContentObject(
                $this->getUser(), $contentObject, $this->getWorkspace()
            ) && $this->getPublicationAggregator()->canContentObjectBeEdited((int) $contentObject->getId());
    }
}
