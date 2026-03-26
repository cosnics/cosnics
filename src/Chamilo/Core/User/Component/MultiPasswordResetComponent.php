<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Service\UserUrlGenerator;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException;
use Chamilo\Libraries\Protocol\Mail\Architecture\Interface\MailerInterface;
use Chamilo\Libraries\Protocol\Security\Service\HashingAlgorithm;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Enum\AlertEnum;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertsManager;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Hackzilla\PasswordGenerator\Generator\PasswordGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\User\Component
 */
class MultiPasswordResetComponent extends Manager
{
    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator, UrlGenerator $urlGenerator,
        AuthenticationValidator $authenticationValidator, UserUrlGenerator $userUrlGenerator,
        MailerInterface $activeMailer, AlertsManager $alertsManager, UserService $userService,
        protected readonly HashingAlgorithm $hashingAlgorithm,
        protected readonly PasswordGeneratorInterface $passwordGenerator
    )
    {
        parent::__construct(
            $request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $urlGenerator,
            $authenticationValidator, $userUrlGenerator, $activeMailer, $alertsManager, $userService
        );
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchParameterException
     */
    public function run(?User $currentUser = null): Response
    {
        $userIdentifiers = (array) $this->getRequest()->getFromRequestOrQuery(self::PARAM_USER_ID, []);
        $translator = $this->getTranslator();

        if (!$currentUser->isPlatformAdministrator()) {
            throw new NotAllowedException();
        }

        if (count($userIdentifiers) > 0) {
            $failures = 0;

            foreach ($userIdentifiers as $userIdentifier) {
                $userToReset = $this->userService->findUserByIdentifier($userIdentifier);

                if (!$this->userService->createNewPasswordForUser($userToReset, $currentUser)) {
                    $failures ++;
                }
            }

            if ($failures) {
                if (count($userIdentifiers) == 1) {
                    $message = $translator->trans(
                        'UserPasswordNotResetted', [], Manager::CONTEXT
                    );
                }
                else {
                    $message = $translator->trans(
                        'UserPasswordsNotResetted', [], Manager::CONTEXT
                    );
                }
            }
            elseif (count($userIdentifiers) == 1) {
                $message = $translator->trans(
                    'UserPasswordResetted', [], Manager::CONTEXT
                );
            }
            else {
                $message = $translator->trans(
                    'UserPasswordResetted', [], Manager::CONTEXT
                );
            }

            $this->alertsManager->addAlert(
                new Alert(
                    $message, $failures ? AlertEnum::DANGER : AlertEnum::SUCCESS
                )
            );

            return new RedirectResponse($this->getUrlGenerator()->fromParameters([
                ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                ApplicationInterface::PARAM_ACTION => ActionEnum::BROWSE->value
            ]));
        }
        else {
            throw new NoSuchParameterException(self::PARAM_USER_ID);
        }
    }
}
