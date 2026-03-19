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
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException;
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
class UpdateComponent extends Manager
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
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException
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

        if ($groupIdentifier) {
            $group = $this->getGroupService()->findGroupByIdentifier($groupIdentifier);

            $formUri = $this->getUrlGenerator()->fromParameters(
                [
                    self::PARAM_CONTEXT => Manager::CONTEXT,
                    self::PARAM_ACTION => ActionEnum::UPDATE->value,
                    DataClass::PROPERTY_ID => $groupIdentifier
                ]
            );

            $form = $this->getFormFactory()->create(
                GroupFormType::class, $group->getDefaultProperties(), ['action' => $formUri]
            );
            $form->handleRequest($this->getRequest());

            if ($form->isSubmitted() && $form->isValid()) {
                $submittedData = $form->getData();

                try {
                    $group = $this->getGroupService()->updateGroupFromParameters(
                        $group, $submittedData[Group::PROPERTY_NAME],
                        $submittedData[NestedSet::PROPERTY_PARENT_ID]->getValue(),
                        $submittedData[Group::PROPERTY_DESCRIPTION], $submittedData[Group::PROPERTY_CODE], $currentUser
                    );

                    $this->getAlertsManager()->addAlert(
                        new Alert(
                            $translator->trans(
                                'ObjectUpdated', ['%Object%' => $translator->trans('Group', [], Manager::CONTEXT)],
                                StringUtilities::LIBRARIES
                            ), AlertEnum::SUCCESS
                        )
                    );

                    return new RedirectResponse($this->getUrlGenerator()->fromParameters([
                        ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                        ApplicationInterface::PARAM_ACTION => ActionEnum::BROWSE->value,
                        DataClass::PROPERTY_ID => $group->getId()
                    ]));
                }
                catch (Throwable) {
                    $this->getAlertsManager()->addAlert(
                        new Alert(
                            $translator->trans(
                                'ObjectNotUpdated', ['%Object%' => $translator->trans('Group', [], Manager::CONTEXT)],
                                StringUtilities::LIBRARIES
                            ), AlertEnum::DANGER
                        )
                    );
                }
            }

            $html = [];

            $html[] = $this->renderHeader($currentUser);
            $html[] = $this->getTwigFormEnvironment()->render('form.html.twig', [
                'form' => $form->createView(),
            ]);
            $html[] = $this->renderFooter();

            return new Response(implode(PHP_EOL, $html));
        }
        else {
            throw new NoSuchParameterException(DataClass::PROPERTY_ID);
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
