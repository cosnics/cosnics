<?php
namespace Chamilo\Core\Menu\Component;

use Chamilo\Core\Menu\Architecture\Domain\ItemRendererRegistry;
use Chamilo\Core\Menu\Architecture\Enum\ActionEnum;
use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Service\CachedItemService;
use Chamilo\Core\Menu\Service\ItemFormDataHandler;
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
use Chamilo\Libraries\Storage\Architecture\Exception\NoSuchObjectException;
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
class UpdateComponent extends Manager
{
    protected BreadcrumbTrail $breadcrumbTrail;

    protected FormFactoryInterface $formFactory;

    protected ItemFormDataHandler $itemFormDataHandler;

    protected ItemFormType $itemFormType;

    protected Environment $twigFormEnvironment;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator, CachedItemService $cachedItemService,
        ItemRendererRegistry $itemRendererRegistry, ItemService $itemService, AlertsManager $alertsManager,
        BreadcrumbTrail $breadcrumbTrail, UrlGenerator $urlGenerator, FormFactoryInterface $formFactory,
        ItemFormType $itemFormType, Environment $twigFormEnvironment, ItemFormDataHandler $itemFormDataHandler
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $cachedItemService,
            $itemRendererRegistry, $itemService, $alertsManager, $urlGenerator
        );

        $this->breadcrumbTrail = $breadcrumbTrail;
        $this->formFactory = $formFactory;
        $this->twigFormEnvironment = $twigFormEnvironment;
        $this->itemFormType = $itemFormType;
        $this->itemFormDataHandler = $itemFormDataHandler;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\DisplayOrderException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\NoSuchObjectException
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

        $item = $this->getItem();
        $itemRenderer = $this->getItemRendererFactory()->getItemRendererForItem($item);
        $itemFormDataHandler = $this->getItemFormDataHandler();

        $this->getBreadcrumbTrail()->add(
            new Breadcrumb(
                $this->getTranslator()->trans(
                    'EditMenuItemComponentTitle', ['%ItemName%' => $itemRenderer->renderTitleForCurrentLanguage($item)],
                    Manager::CONTEXT
                ), $this->getUrlGenerator()->fromRequest()
            )
        );

        $itemUri = $this->getUrlGenerator()->fromParameters(
            [
                ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                ApplicationInterface::PARAM_ACTION => ActionEnum::UPDATE->value,
                self::PARAM_TYPE => $item->getType(),
                self::PARAM_ITEM => $item->getId()
            ]
        );

        $form = $this->getFormFactory()->create(
            ItemFormType::class, $itemFormDataHandler->getDefaultFormData($item),
            ['action' => $itemUri, 'itemType' => $item->getType()]
        );
        $form->handleRequest($this->getRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            $success = $this->getCachedItemService()->saveItemFromValues(
                $item, $itemFormDataHandler->handleData($item->getType(), $form->getData())
            );

            $message = $this->getTranslator()->trans(
                $success ? 'ObjectCreated' : 'ObjectNotCreated',
                ['%Object%' => $this->getTranslator()->trans('ManagerItem', [], Manager::CONTEXT)],
                StringUtilities::LIBRARIES
            );

            $this->getAlertsManager()->addAlert(
                new Alert(
                    $message, $success ? AlertEnum::SUCCESS : AlertEnum::DANGER
                )
            );

            return new RedirectResponse($this->getUrlGenerator()->fromParameters([
                ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                ApplicationInterface::PARAM_ACTION => ActionEnum::BROWSE->value,
                Manager::PARAM_ITEM => $item->getParentId()
            ]));
        }

        $html = [];

        $html[] = $this->renderHeader($currentUser);
        $html[] = $this->getTwigFormEnvironment()->render('form.html.twig', [
            'form' => $form->createView(),
        ]);
        $html[] = $this->renderFooter();

        return new Response(implode(PHP_EOL, $html));
    }

    public function getBreadcrumbTrail(): BreadcrumbTrail
    {
        return $this->breadcrumbTrail;
    }

    public function getFormFactory(): FormFactoryInterface
    {
        return $this->formFactory;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\NoSuchObjectException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    protected function getItem(): Item
    {
        $itemIdentifier = $this->getRequest()->query->get(self::PARAM_ITEM);

        if (is_null($itemIdentifier)) {
            throw new NoSuchParameterException(self::PARAM_ITEM);
        }

        $item = $this->getItemService()->findItemByIdentifier($itemIdentifier);

        if (!$item instanceof Item) {
            throw new NoSuchObjectException($this->getTranslator()->trans('MenuItem', [], Manager::CONTEXT),
                $itemIdentifier);
        }

        return $item;
    }

    public function getItemFormDataHandler(): ItemFormDataHandler
    {
        return $this->itemFormDataHandler;
    }

    public function getItemFormType(): ItemFormType
    {
        return $this->itemFormType;
    }

    public function getTwigFormEnvironment(): Environment
    {
        return $this->twigFormEnvironment;
    }
}
