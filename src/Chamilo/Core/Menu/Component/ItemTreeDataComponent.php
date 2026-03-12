<?php
namespace Chamilo\Core\Menu\Component;

use Chamilo\Core\Menu\Architecture\Domain\ItemRendererRegistry;
use Chamilo\Core\Menu\Architecture\Enum\ActionEnum;
use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertsManager;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Chamilo\Libraries\UserInterface\Tree\Service\JsTreeMenuDataProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Group\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ItemTreeDataComponent extends Manager
{
    protected JsTreeMenuDataProvider $jsTreeMenuDataProvider;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator, CachedItemService $cachedItemService,
        ItemRendererRegistry $itemRendererRegistry, ItemService $itemService, AlertsManager $alertsManager,
        UrlGenerator $urlGenerator, JsTreeMenuDataProvider $jsTreeMenuDataProvider
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $cachedItemService,
            $itemRendererRegistry, $itemService, $alertsManager, $urlGenerator
        );

        $this->jsTreeMenuDataProvider = $jsTreeMenuDataProvider;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     */
    public function run(?User $currentUser = null): Response
    {
        if (!$currentUser instanceof User || !$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $urlFormat = $this->getUrlGenerator()->fromParameters(
            [
                ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                ApplicationInterface::PARAM_ACTION => ActionEnum::BROWSE->value,
                Manager::PARAM_PARENT => '%s'
            ]
        );

        return new JsonResponse(
            data: $this->getJsTreeDataProvider()->getData(
                $urlFormat, $this->getCurrentParentIdentifier()
            )
        );
    }

    public function getCurrentParentIdentifier(): ?string
    {
        return $this->getRequest()->query->get(Manager::PARAM_PARENT);
    }

    public function getJsTreeDataProvider(): JsTreeMenuDataProvider
    {
        return $this->jsTreeMenuDataProvider;
    }
}
