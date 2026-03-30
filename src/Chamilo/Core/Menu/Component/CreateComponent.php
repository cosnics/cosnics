<?php
namespace Chamilo\Core\Menu\Component;

use Chamilo\Core\Menu\Architecture\Domain\ItemRendererRegistry;
use Chamilo\Core\Menu\Architecture\Enum\ActionEnum;
use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\UserInterface\Form\ItemFormType;
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
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;
use Twig\Environment;

/**
 * @package Chamilo\Core\Menu\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class CreateComponent extends Manager
{
    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator, UrlGenerator $urlGenerator,
        CachedItemService $cachedItemService, ItemRendererRegistry $itemRendererRegistry, ItemService $itemService,
        AlertsManager $alertsManager, protected readonly BreadcrumbTrail $breadcrumbTrail,
        protected readonly FormFactoryInterface $formFactory, protected readonly ItemFormType $itemFormType,
        protected readonly Environment $twigFormEnvironment
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $urlGenerator,
            $cachedItemService, $itemRendererRegistry, $itemService, $alertsManager
        );
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\DisplayOrderException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageLastInsertedIdentifierException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Symfony\Component\Cache\Exception\CacheException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
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

        $itemRenderer = $this->itemRendererRegistry->getItemRenderer($itemType);

        $this->breadcrumbTrail->add(
            new Breadcrumb(
                $this->getTranslator()->trans(
                    'AddMenuItemComponentTitle', ['%ItemType%' => $itemRenderer->getRendererTypeName()],
                    Manager::CONTEXT
                ), $this->getUrlGenerator()->fromRequest()
            )
        );

        $itemUri = $this->getUrlGenerator()->fromParameters(
            [
                ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                ApplicationInterface::PARAM_ACTION => ActionEnum::CREATE->value,
                self::PARAM_TYPE => $itemType
            ]
        );

        $form = $this->formFactory->create(
            ItemFormType::class, [], ['action' => $itemUri, 'itemType' => $itemType]
        );
        $form->handleRequest($this->getRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            $item = $this->cachedItemService->createItemForTypeFromValues(
                $itemType, $form->getData()
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

            $this->alertsManager->addAlert(
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
        $html[] = $this->twigFormEnvironment->render('form.html.twig', [
            'form' => $form->createView(),
        ]);
        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }
}
