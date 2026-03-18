<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Architecture\Enum\ActionEnum;
use Chamilo\Core\Group\Manager;
use Chamilo\Core\Group\Service\GroupMembershipService;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Group\Service\GroupUrlGenerator;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Group\UserInterface\Form\GroupFormType;
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
use Throwable;
use Twig\Environment;

/**
 * @package Chamilo\Core\Group\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CreateComponent extends Manager
{
    protected FormFactoryInterface $formFactory;

    protected GroupFormType $groupFormType;

    protected Environment $twigFormEnvironment;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator,
        GroupMembershipService $groupMembershipService, GroupUrlGenerator $groupUrlGenerator,
        AlertsManager $alertsManager, BreadcrumbTrail $breadcrumbTrail, GroupService $groupService,
        UserService $userService, UrlGenerator $urlGenerator, FormFactoryInterface $formFactory,
        GroupFormType $groupFormType, Environment $twigFormEnvironment
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $groupMembershipService,
            $groupUrlGenerator, $alertsManager, $breadcrumbTrail, $groupService, $userService, $urlGenerator
        );

        $this->formFactory = $formFactory;
        $this->groupFormType = $groupFormType;
        $this->twigFormEnvironment = $twigFormEnvironment;
    }

    /**
     * @throws \QuickformException
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Throwable
     */
    public function run(?User $currentUser = null): Response
    {
        if (!$currentUser instanceof User || !$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        $translator = $this->getTranslator();

        $parentGroupIdentifier = $this->getRequest()->query->get(self::PARAM_GROUP_ID, DataClass::EMPTY_UUID);

        $formUri = $this->getUrlGenerator()->fromParameters(
            [
                self::PARAM_CONTEXT => Manager::CONTEXT,
                self::PARAM_ACTION => ActionEnum::CREATE->value,
                self::PARAM_GROUP_ID => $parentGroupIdentifier
            ]
        );

        $form = $this->getFormFactory()->create(
            GroupFormType::class, [NestedSet::PROPERTY_PARENT_ID => $parentGroupIdentifier], ['action' => $formUri]
        );
        $form->handleRequest($this->getRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            $submittedData = $form->getData();
            dump($submittedData);
            exit;
            try {
                $group = $this->getGroupService()->createGroupFromParameters(
                    $submittedData[Group::PROPERTY_NAME], $submittedData[Group::PROPERTY_NAME],
                    $submittedData[Group::PROPERTY_NAME], $submittedData[Group::PROPERTY_NAME], $currentUser
                );

                $this->getAlertsManager()->addAlert(
                    new Alert(
                        $translator->trans(
                            'ObjectCreated', ['%Object%' => $translator->trans('Group', [], Manager::CONTEXT)],
                            StringUtilities::LIBRARIES
                        )
                    )
                );

                return new RedirectResponse($this->getUrlGenerator()->fromParameters([
                    ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                    ApplicationInterface::PARAM_ACTION => ActionEnum::BROWSE->value,
                    self::PARAM_GROUP_ID => $group->getId()
                ]));
            }
            catch (Throwable) {
                $this->getAlertsManager()->addAlert(
                    new Alert(
                        $translator->trans(
                            'ObjectNotCreated', ['%Object%' => $translator->trans('Group', [], Manager::CONTEXT)],
                            StringUtilities::LIBRARIES
                        ), AlertEnum::DANGER
                    )
                );

                return new RedirectResponse($this->getUrlGenerator()->fromParameters([
                    ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                    ApplicationInterface::PARAM_ACTION => ActionEnum::BROWSE->value,
                    self::PARAM_GROUP_ID => $parentGroupIdentifier
                ]));
            }
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

    public function getGroupFormType(): GroupFormType
    {
        return $this->groupFormType;
    }

    public function getTwigFormEnvironment(): Environment
    {
        return $this->twigFormEnvironment;
    }
}
