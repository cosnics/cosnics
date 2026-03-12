<?php
namespace Chamilo\Core\Menu\Component;

use Chamilo\Core\Menu\Architecture\Domain\ItemRendererRegistry;
use Chamilo\Core\Menu\Architecture\Enum\ActionEnum;
use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\UserInterface\Form\ItemForm;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Enum\AlertEnum;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertsManager;
use Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\Breadcrumb;
use Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\BreadcrumbTrail;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Menu\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class CreateComponent extends Manager
{
    protected BreadcrumbTrail $breadcrumbTrail;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator, CachedItemService $cachedItemService,
        ItemRendererRegistry $itemRendererRegistry, ItemService $itemService, AlertsManager $alertsManager,
        BreadcrumbTrail $breadcrumbTrail, UrlGenerator $urlGenerator
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $cachedItemService,
            $itemRendererRegistry, $itemService, $alertsManager, $urlGenerator
        );

        $this->breadcrumbTrail = $breadcrumbTrail;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\DisplayOrderException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \QuickformException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function run(?User $currentUser = null): Response
    {
        if (!$currentUser instanceof User || !$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $itemType = $this->getRequest()->query->get(self::PARAM_TYPE);

        if (is_null($itemType)) {
            throw new NoSuchParameterException(self::PARAM_TYPE);
        }

        $itemRenderer = $this->getItemRendererFactory()->getItemRenderer($itemType);

        $this->getBreadcrumbTrail()->add(
            new Breadcrumb(
                $this->getTranslator()->trans(
                    'AddMenuItemComponentTitle', ['%ItemType%' => $itemRenderer->getRendererTypeName()],
                    Manager::CONTEXT
                ), $this->getUrlGenerator()->fromRequest()
            )
        );

        $itemForm = new ItemForm(
            $itemType, $this->getUrlGenerator()->fromParameters(
            [
                ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                ApplicationInterface::PARAM_ACTION => ActionEnum::CREATE->value,
                self::PARAM_TYPE => $itemType
            ]
        )
        );

        if ($itemForm->validate()) {
            $item = $this->getCachedItemService()->createItemForTypeFromValues(
                $itemType, $itemForm->exportValues()
            );

            $success = $item instanceof Item;

            if ($success) {
                $message = $this->getTranslator()->trans(
                    'ObjectCreated', ['%Object%' => $this->getTranslator()->trans('ManagerItem', [], Manager::CONTEXT)],
                    StringUtilities::LIBRARIES
                );
            }
            else {
                $message = $this->getTranslator()->trans(
                    'ObjectNotCreated',
                    ['%Object%' => $this->getTranslator()->trans('ManagerItem', [], Manager::CONTEXT)],
                    StringUtilities::LIBRARIES
                );
            }

            $this->getAlertsManager()->addAlert(
                new Alert(
                    $message, $success ? AlertEnum::SUCCESS : AlertEnum::DANGER
                )
            );

            return new RedirectResponse($this->getUrlGenerator()->fromParameters([
                ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                ApplicationInterface::PARAM_ACTION => ActionEnum::BROWSE->value,
                Manager::PARAM_PARENT => $item->getParentId()
            ]));
        }

        $html = [];

        $html[] = $this->renderHeader($currentUser);
        $html[] = $itemForm->render();
        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }

    public function getBreadcrumbTrail(): BreadcrumbTrail
    {
        return $this->breadcrumbTrail;
    }
}
