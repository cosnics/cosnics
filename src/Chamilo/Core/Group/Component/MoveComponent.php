<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Architecture\Enum\ActionEnum;
use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Service\GroupMembershipService;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Group\Service\GroupUrlGenerator;
use Chamilo\Core\Group\UserInterface\Form\GroupMoveFormType;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\Storage\Architecture\Domain\DataClass;
use Chamilo\Libraries\Storage\Architecture\Domain\NestedSet;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Enum\AlertEnum;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertsManager;
use Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\BreadcrumbTrail;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;
use Twig\Environment;

/**
 * @package Chamilo\Core\Group\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MoveComponent extends Manager
{
    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator, UrlGenerator $urlGenerator,
        AlertsManager $alertsManager, BreadcrumbTrail $breadcrumbTrail, GroupMembershipService $groupMembershipService,
        GroupService $groupService, GroupUrlGenerator $groupUrlGenerator, UserService $userService,
        protected FormFactoryInterface $formFactory, protected GroupMoveFormType $groupMoveFormType,
        protected Environment $twigFormEnvironment
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $urlGenerator, $alertsManager,
            $breadcrumbTrail, $groupMembershipService, $groupService, $groupUrlGenerator, $userService
        );
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function run(?User $currentUser = null): Response
    {
        if (!$currentUser instanceof User || !$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $translator = $this->getTranslator();

        $groupIdentifier = $this->getRequest()->query->get(DataClass::PROPERTY_ID);

        $group = $this->groupService->findGroupByIdentifier($groupIdentifier);

        $formUri = $this->getUrlGenerator()->fromParameters(
            [
                self::PARAM_CONTEXT => Manager::CONTEXT,
                self::PARAM_ACTION => ActionEnum::MOVE->value,
                DataClass::PROPERTY_ID => $groupIdentifier
            ]
        );

        $form = $this->formFactory->create(
            GroupMoveFormType::class, $group->getDefaultProperties(),
            ['action' => $formUri, 'disabledGroupIdentifiers' => [$groupIdentifier]]
        );
        $form->handleRequest($this->getRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            $submittedData = $form->getData();

            $success = $this->groupService->moveGroup(
                $group, $submittedData[NestedSet::PROPERTY_PARENT_ID], $currentUser
            );

            $message = $translator->trans(
                $success ? 'ObjectMoved' : 'ObjectNotMoved', ['%Object%' => $translator->trans('Group')],
                StringUtilities::LIBRARIES
            );

            $this->alertsManager->addAlert(
                new Alert(
                    $message, $success ? AlertEnum::SUCCESS : AlertEnum::DANGER
                )
            );

            return new RedirectResponse($this->getUrlGenerator()->fromParameters([
                ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                ApplicationInterface::PARAM_ACTION => ActionEnum::BROWSE->value,
                DataClass::PROPERTY_ID => $submittedData[NestedSet::PROPERTY_PARENT_ID]
            ]));
        }
        else {
            $html = [];

            $html[] = $this->renderHeader($currentUser);
            $html[] = $this->twigFormEnvironment->render('form.html.twig', [
                'form' => $form->createView(),
            ]);
            $html[] = $this->renderFooter();

            return new Response(implode(PHP_EOL, $html));
        }
    }
}
