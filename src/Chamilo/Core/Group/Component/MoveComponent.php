<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Architecture\Enum\ActionEnum;
use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Service\GroupMembershipService;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Group\Service\GroupUrlGenerator;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\UserInterface\Form\GroupMoveType;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
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
    protected FormFactoryInterface $formFactory;

    protected GroupMoveType $groupMoveType;

    protected Environment $twigFormEnvironment;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator,
        GroupMembershipService $groupMembershipService, GroupUrlGenerator $groupUrlGenerator,
        AlertsManager $alertsManager, BreadcrumbTrail $breadcrumbTrail, GroupService $groupService,
        UserService $userService, UrlGenerator $urlGenerator, FormFactoryInterface $formFactory,
        Environment $twigFormEnvironment, GroupMoveType $groupMoveType
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $groupMembershipService,
            $groupUrlGenerator, $alertsManager, $breadcrumbTrail, $groupService, $userService, $urlGenerator,
        );

        $this->formFactory = $formFactory;
        $this->twigFormEnvironment = $twigFormEnvironment;
        $this->groupMoveType = $groupMoveType;
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

        $groupIdentifier = $this->getRequest()->query->get(self::PARAM_GROUP_ID);

        $group = $this->getGroupService()->findGroupByIdentifier($this->getRequest()->query->get(self::PARAM_GROUP_ID));

        $formUri = $this->getUrlGenerator()->fromParameters(
            [
                self::PARAM_CONTEXT => Manager::CONTEXT,
                self::PARAM_ACTION => ActionEnum::MOVE->value,
                self::PARAM_GROUP_ID => $groupIdentifier
            ]
        );

        $form = $this->getFormFactory()->create(
            GroupMoveType::class,
            [NestedSet::PROPERTY_PARENT_ID => $group->getParentId(), Group::PROPERTY_NAME => $group->getName(), 'description' => '<p>Whiiiiiiiii</><p><strong>Bold</strong> Whiiiiiiiii</>'],
            ['action' => $formUri, 'disabledGroupIdentifiers' => [$groupIdentifier]]
        );
        $form->handleRequest($this->getRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            $submittedData = $form->getData();

            $success = $this->getGroupService()->moveGroup(
                $group, $submittedData[NestedSet::PROPERTY_PARENT_ID], $currentUser
            );

            $message = $translator->trans(
                $success ? 'ObjectMoved' : 'ObjectNotMoved', ['%Object%' => $translator->trans('Group')],
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
                self::PARAM_GROUP_ID => $submittedData[NestedSet::PROPERTY_PARENT_ID]
            ]));
        }
        else {
            $html = [];

            $html[] = $this->renderHeader($currentUser);
            $html[] = $this->getTwigFormEnvironment()->render('form.html.twig', [
                'form' => $form->createView(),
            ]);
            $html[] = $this->renderFooter();

            return new Response(implode(PHP_EOL, $html));
        }
    }

    public function getFormFactory(): FormFactoryInterface
    {
        return $this->formFactory;
    }

    public function getGroupMoveType(): GroupMoveType
    {
        return $this->groupMoveType;
    }

    public function getTwigFormEnvironment(): Environment
    {
        return $this->twigFormEnvironment;
    }
}
