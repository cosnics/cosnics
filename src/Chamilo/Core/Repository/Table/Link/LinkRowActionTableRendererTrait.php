<?php
namespace Chamilo\Core\Repository\Table\Link;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Publication\Service\PublicationAggregatorInterface;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\Table\Link
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait LinkRowActionTableRendererTrait
{
    protected PublicationAggregatorInterface $publicationAggregator;

    protected RightsService $rightsService;

    protected User $user;

    protected Workspace $workspace;

    protected function getDeleteLinkUrl(int $type, string $contentObjectIdentifier, string $linkIdentifier): string
    {
        $parameters = [];

        $parameters[Application::PARAM_ACTION] = Manager::ACTION_DELETE_LINK;
        $parameters[Manager::PARAM_LINK_TYPE] = $type;
        $parameters[Manager::PARAM_CONTENT_OBJECT_ID] = $contentObjectIdentifier;
        $parameters[Manager::PARAM_LINK_ID] = $linkIdentifier;

        return $this->getUrlGenerator()->fromParameters($parameters);
    }

    public function getPublicationAggregator(): PublicationAggregatorInterface
    {
        return $this->publicationAggregator;
    }

    protected function getRightsService(): RightsService
    {
        return $this->rightsService;
    }

    abstract public function getTranslator(): Translator;

    abstract public function getUrlGenerator(): UrlGenerator;

    public function getUser(): User
    {
        return $this->user;
    }

    public function getWorkspace(): Workspace
    {
        return $this->workspace;
    }

    public function isAllowedToModify(ContentObject $contentObject): bool
    {
        return $this->getRightsService()->canEditContentObject(
                $this->getUser(), $contentObject, $this->getWorkspace()
            ) && $this->getPublicationAggregator()->canContentObjectBeEdited((int) $contentObject->getId());
    }

    public function renderLinkTableRowAction(
        ContentObject $contentObject, int $linkType, string $contentObjectIdentifier, string $linkIdentifier
    ): string
    {
        $translator = $this->getTranslator();
        $toolbar = new Toolbar();

        if ($this->isAllowedToModify($contentObject))
        {

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                    $this->getDeleteLinkUrl(
                        $linkType, $contentObjectIdentifier, $linkIdentifier
                    ), ToolbarItem::DISPLAY_ICON, true
                )
            );
        }

        return $toolbar->render();
    }
}