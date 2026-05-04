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
use Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Enum\AlertEnum;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertsManager;
use Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\BreadcrumbTrail;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Chamilo\Libraries\UserInterface\Tree\Architecture\Domain\OptionsTreeChoice;
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
    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator, UrlGenerator $urlGenerator,
        AlertsManager $alertsManager, BreadcrumbTrail $breadcrumbTrail, GroupMembershipService $groupMembershipService,
        GroupService $groupService, GroupUrlGenerator $groupUrlGenerator, UserService $userService,
        protected readonly FormFactoryInterface $formFactory, protected readonly Environment $twigFormEnvironment
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $urlGenerator, $alertsManager,
            $breadcrumbTrail, $groupMembershipService, $groupService, $groupUrlGenerator, $userService
        );
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
            $group = $this->groupService->retrieveGroupByIdentifier($groupIdentifier);

            $formUri = $this->getUrlGenerator()->fromParameters(
                [
                    self::PARAM_CONTEXT => Manager::CONTEXT,
                    self::PARAM_ACTION => ActionEnum::UPDATE->value,
                    DataClass::PROPERTY_ID => $groupIdentifier
                ]
            );

            $data = $group->getDefaultProperties();
            $data[Group::PROPERTY_PARENT_ID] = new OptionsTreeChoice($group->getParentId(), '');

            $form = $this->formFactory->create(
                GroupFormType::class, $data, ['action' => $formUri, 'disabledGroupIdentifiers' => [$groupIdentifier]]
            );
            $form->handleRequest($this->getRequest());

            if ($form->isSubmitted() && $form->isValid()) {
                $submittedData = $form->getData();

                try {
                    $group = $this->groupService->updateGroupFromParameters(
                        $group, $submittedData[Group::PROPERTY_NAME],
                        $submittedData[Group::PROPERTY_PARENT_ID]->getValue(),
                        $submittedData[Group::PROPERTY_DESCRIPTION], $submittedData[Group::PROPERTY_CODE], $currentUser
                    );

                    $this->alertsManager->addAlert(
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
                    $this->alertsManager->addAlert(
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
            $html[] = $this->twigFormEnvironment->render('form.html.twig', [
                'form' => $form->createView(),
            ]);
            $html[] = $this->renderFooter();

            return new Response(implode(PHP_EOL, $html));
        }
        else {
            throw new NoSuchParameterException(DataClass::PROPERTY_ID);
        }
    }
}
